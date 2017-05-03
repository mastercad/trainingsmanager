<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:40
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class Auth_RegisterController extends AbstractController
{

    public function indexAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        $str_register_email = null;
        $str_register_vorname = null;
        $str_register_passwort = null;
        $str_register_nachname = null;

        if($this->getRequest()->isPost()
            && true === array_key_exists('register_submit', $a_params)
        ) {
            $a_data = array();
            $obj_db_users = new Auth_Model_DbTable_Users();
            $b_all_valid = true;

            if(isset($a_params['register_email']))
            {
                $str_register_email = base64_decode($a_params['register_email']);
            }
            if(isset($a_params['register_vorname']))
            {
                $str_register_vorname = base64_decode($a_params['register_vorname']);
            }
            if(isset($a_params['register_passwort']))
            {
                $str_register_passwort = base64_decode($a_params['register_passwort']);
            }
            if(isset($a_params['register_nachname']))
            {
                $str_register_nachname = base64_decode($a_params['register_nachname']);
            }

            $obj_validate_email = new Zend_Validate_EmailAddress();
            $b_valid_email = $obj_validate_email->isValid($str_register_email);
            $b_email_exists = false;

            if($b_valid_email)
            {
                $b_email_exists = $this->checkEmailExists($str_register_email);
            }

            // valid und existiert noch nicht
            if($b_valid_email &&
                !$b_email_exists)
            {
                $a_data['user_email'] = $str_register_email;
                $a_data['user_login'] = $str_register_email;
            }
            // valid aber existiert
            else if($b_valid_email &&
                $b_email_exists)
            {
                $b_all_valid = false;
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Diese E-Mail Adresse ist bereits registriert!";
                $a_messages[$i_count_messages]['result'] = false;
            }
            // nicht valid
            else if(!$b_valid_email)
            {
                $b_all_valid = false;
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Bitte geben Sie eine gültige E-Mail Adresse ein!";
                $a_messages[$i_count_messages]['result'] = false;
            }

            if(strlen(trim($str_register_passwort)) >= 8)
            {
                $a_data['user_passwort'] = md5($str_register_passwort);
            }
            else
            {
                $b_all_valid = false;
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Bitte geben Sie ein Passwort mit mindestens 8 Stellen ein!";
                $a_messages[$i_count_messages]['result'] = false;
            }

            if(strlen(trim($str_register_vorname)) > 0)
            {
                $a_data['user_vorname'] = $str_register_vorname;
            }

            if(strlen(trim($str_register_nachname)) > 0)
            {
                $a_data['user_nachname'] = $str_register_nachname;
            }

            if( true === $b_all_valid)
            {
                $a_data['user_status_fk'] = 1;
                $a_data['user_rechte_gruppe_fk'] = 2;

                $result = $obj_db_users->setUser($a_data);

                if($result)
                {
                    $i_count_messages = count($a_messages);
                    $a_messages[$i_count_messages]['type'] = "meldung";
                    $a_messages[$i_count_messages]['message'] = "Ihr Login wurde erfolgreich angelegt!";
                    $a_messages[$i_count_messages]['confirm_func'] = "CAD.removeLastObject();";
                    $a_messages[$i_count_messages]['result'] = true;

                    $str_user_name = $str_register_email;
                    if(strlen(trim($str_register_vorname)))
                    {
                        $str_user_name = $str_register_vorname . " ";
                    }
                    if(strlen(trim($str_register_nachname)))
                    {
                        $str_user_name .= $str_register_nachname;
                    }

                    $str_message = "Hallo " . $str_user_name . ",<br /><br />";
                    $str_message .= "Sie haben sich erfolgreich auf " . PROJECT_NAME;
                    $str_message .= "registriert! Vielen Dank für Ihr Vertrauen!<br /><br />";
                    $str_message .= '<a href="' . PROJECT_URL . '">Klicken Sie hier, um sich gleich anzumelden.</a><br /><br />';
                    $str_message .= 'Mit freundlichen Grüßen und einen angenehmen Tag<br />';
                    $str_message .= 'Ihr Team von ' . PROJECT_NAME . '.';

                    $obj_mail = new Zend_Mail("UTF-8");
                    $obj_mail->addTo($str_register_email, $str_user_name);
                    $obj_mail->setBodyHtml($str_message);
                    $obj_mail->addBcc('andreas.kempe@byte-artist.de');
                    $obj_mail->setFrom("webservice@byte-artist.de", "Webservice von " . PROJECT_NAME);
                    $obj_mail->setSubject('Ihre Registrierung auf ' . PROJECT_NAME);

                    $result = $obj_mail->send();
                }
                else
                {
                    $i_count_messages = count($a_messages);
                    $a_messages[$i_count_messages]['type'] = "fehler";
                    $a_messages[$i_count_messages]['message'] = "Beim Anlegen Ihres Login ist ein Fehler aufgetreten!";
                    $a_messages[$i_count_messages]['result'] = false;
                }
            }
            $this->view->assign('json_string', json_encode($a_messages));
        } else {
            echo $this->view->render('register/form.phtml');
        }
    }

    public function checkEmailExists($str_email)
    {
        $obj_db_users = new Auth_Model_DbTable_Users();
        $b_result = $obj_db_users->checkEmailExists($str_email);

        return $b_result;
    }
}
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
    }

    public function saveAction() {

        $a_params = $this->getRequest()->getParams();

        $str_register_email = null;
        $str_register_vorname = null;
        $str_register_passwort = null;
        $str_register_nachname = null;

        if ($this->getRequest()->isPost()) {
            $a_data = array();
            $obj_db_users = new Auth_Model_DbTable_Users();
            $b_all_valid = true;

            if(isset($a_params['register_email'])) {
                $str_register_email = base64_decode($a_params['register_email']);
            }
            if(isset($a_params['register_first_name'])) {
                $str_register_vorname = base64_decode($a_params['register_first_name']);
            }
            if(isset($a_params['register_last_name'])) {
                $str_register_nachname = base64_decode($a_params['register_last_name']);
            }

            $obj_validate_email = new Zend_Validate_EmailAddress();
            $b_valid_email = $obj_validate_email->isValid($str_register_email);
            $b_email_exists = false;

            if($b_valid_email){
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
            else if($b_valid_email
                && $b_email_exists
            ) {
                $b_all_valid = false;
                Service_GlobalMessageHandler::appendMessage('Diese E-Mail Adresse ist bereits registriert!', Model_Entity_Message::STATUS_ERROR);
            // nicht valid
            } else if(!$b_valid_email) {
                $b_all_valid = false;
                Service_GlobalMessageHandler::appendMessage('Bitte geben Sie eine gültige E-Mail Adresse ein!', Model_Entity_Message::STATUS_ERROR);
            }

            if(strlen(trim($str_register_vorname)) > 0) {
                $a_data['user_first_name'] = $str_register_vorname;
            }

            if(strlen(trim($str_register_nachname)) > 0) {
                $a_data['user_last_name'] = $str_register_nachname;
            }

            if( true === $b_all_valid) {
                $obj_tools = new CAD_Tools();
                $a_data['user_password'] = $obj_tools->generatePasswort();
                $a_data['user_state_fk'] = 1;
                $a_data['user_right_group_fk'] = 2;

                $result = $obj_db_users->saveUser($a_data);

                if ($result) {
                    Service_GlobalMessageHandler::appendMessage('Ihr Login wurde erfolgreich angelegt!', Model_Entity_Message::STATUS_OK);

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

//                    $result = $obj_mail->send();
                } else {
                    Service_GlobalMessageHandler::appendMessage('Beim Anlegen Ihres Login ist ein Fehler aufgetreten!', Model_Entity_Message::STATUS_ERROR);
                }
            }
        }
    }

    private function checkEmailExists($str_email)
    {
        $obj_db_users = new Auth_Model_DbTable_Users();
        $b_result = $obj_db_users->checkEmailExists($str_email);

        return $b_result;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:41
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class Auth_PasswordLostController extends AbstractController
{

    public function indexAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        if ($this->getRequest()->isPost() &&
            isset($a_params['password_lost_email'])
        ) {
            $str_email = base64_decode($a_params['password_lost_email']);

            $obj_tools = new CAD_Tools();
            $obj_valid_email = new Zend_Validate_EmailAddress();
            $obj_db_users = new Auth_Model_DbTable_Users();
            $str_new_passwort = $obj_tools->generatePasswort();

            $b_email_valid = $obj_valid_email->isValid($str_email);
            if ($b_email_valid) {
                $a_user = $obj_db_users->getUserByEmail($str_email);
                if (is_array($a_user) &&
                    count($a_user) > 0
                ) {
                    $str_user_name = $str_email;
                    if (isset($a_user['user_vorname']) &&
                        strlen(trim($a_user['user_vorname']))
                    ) {
                        $str_user_name = $a_user['user_vorname'] . " ";
                    }
                    if (isset($a_user['user_nachname']) &&
                        strlen(trim($a_user['user_nachname']))
                    ) {
                        $str_user_name .= $a_user['user_nachname'];
                    }

                    $str_message = "Hallo " . $str_user_name . ",<br /><br />";
                    $str_message .= "Sie haben ein neues Paswort für den Login-Bereich ";
                    $str_message .= "auf " . PROJECT_NAME . " angefordert.<br /><br />";
                    $str_message .= "Es wurde erfolgreich geändert und lautet <strong>" . $str_new_passwort . "</strong><br /><br />";
                    $str_message .= '<a href="' . PROJECT_URL . '">Klicken Sie hier, um sich gleich anzumelden.</a><br /><br />';
                    $str_message .= 'Mit freundlichen Grüßen und einen angenehmen Tag<br />';
                    $str_message .= 'Ihr Team von ' . PROJECT_NAME . '.';

                    $obj_mail = new Zend_Mail("UTF-8");
                    $obj_mail->addBcc('andreas.kempe@byte-artist.de');
                    $obj_mail->addTo($str_email, $str_user_name);
                    $obj_mail->setBodyHtml($str_message);
                    $obj_mail->setFrom("webservice@byte-artist.de", "Webservice von " . PROJECT_NAME);
                    $obj_mail->setSubject('Ihr neues Passwort für ' . PROJECT_NAME);

                    $result = $obj_mail->send();

                    if ($result) {
                        $a_data = array();
                        $a_data['user_passwort'] = md5($str_new_passwort);

                        if ($obj_db_users->updateUser($a_data, $a_user['user_id'])) {
                            $i_count_messages = count($a_messages);
                            $a_messages[$i_count_messages]['type'] = "meldung";
                            $a_messages[$i_count_messages]['confirm_func'] = "CAD.removeLastObject();";
                            $a_messages[$i_count_messages]['message'] = "Das Passwort wurde erfolgreich geändert und Ihnen per E-Mail zugesandt!";
                            $a_messages[$i_count_messages]['result'] = false;
                        } else {
                            $i_count_messages = count($a_messages);
                            $a_messages[$i_count_messages]['type'] = "fehler";
                            $a_messages[$i_count_messages]['message'] =
                                "Die E-Mail wurde zwar verschickt, aber es " .
                                "trat ein Problem beim speichern in der Datenbank " .
                                "auf! Bitte versuchen Sie er erneut und/oder " .
                                "nutzen Sie bitte das Kontakt Formular, " .
                                "um den Fehler zu melden!";
                            $a_messages[$i_count_messages]['result'] = false;
                        }
                    } else {
                        $i_count_messages = count($a_messages);
                        $a_messages[$i_count_messages]['type'] = "fehler";
                        $a_messages[$i_count_messages]['message'] = "Die E-Mail mit dem neuen Passwort konnte leider nicht versendet werden!";
                        $a_messages[$i_count_messages]['result'] = false;
                    }
                } else {
                    $i_count_messages = count($a_messages);
                    $a_messages[$i_count_messages]['type'] = "fehler";
                    $a_messages[$i_count_messages]['message'] = "Die angegebene E-Mail Adresse konnte nicht gefunden werden!";
                    $a_messages[$i_count_messages]['result'] = false;
                }
            } else {
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Bitte geben Sie eine gültige E-Mail Adresse an!";
                $a_messages[$i_count_messages]['result'] = false;
            }
            $this->view->assign('json_string', json_encode($a_messages));
//        } else if ($this->getRequest()->isPost()) {
//            $i_count_messages = count($a_messages);
//            $a_messages[$i_count_messages]['type'] = "fehler";
//            $a_messages[$i_count_messages]['message'] = "Bitte geben Sie eine E-Mail Adresse an!";
//            $a_messages[$i_count_messages]['result'] = false;
//            $this->view->assign('json_string', json_encode($a_messages));
        } else {
            $this->view->assign('json_string', $this->view->render('password-lost/form.phtml'));
        }
    }
}
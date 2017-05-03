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
    }

    public function resetPasswordAction() {

        if ($this->getRequest()->isPost()
            && $this->getParam('password_lost_email')
        ) {
            $str_email = base64_decode($this->getParam('password_lost_email'));

            $obj_valid_email = new Zend_Validate_EmailAddress();
            $obj_db_users = new Auth_Model_DbTable_Users();

            $b_email_valid = $obj_valid_email->isValid($str_email);
            if ($b_email_valid) {

                $user = $obj_db_users->getUserByEmail($str_email);
                if ($user instanceof Zend_Db_Table_Row_Abstract) {
                    $obj_tools = new CAD_Tools();
                    $str_new_passwort = $obj_tools->generatePasswort();

                    $str_user_name = $str_email;
                    if ($user->offsetGet('user_first_name')
                        && strlen(trim($user->offsetGet('user_first_name')))
                    ) {
                        $str_user_name = $user->offsetGet('user_first_name') . " ";
                    }
                    if ($user->offsetGet('user_last_name')
                        && strlen(trim($user->offsetGet('user_last_name')))
                    ) {
                        $str_user_name .= $user->offsetGet('user_last_name');
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
                        $a_data['user_password'] = md5($str_new_passwort);

                        if ($obj_db_users->updateUser($a_data, $user->offsetGet('user_id'))) {
                            Service_GlobalMessageHandler::appendMessage('Das Passwort wurde erfolgreich geändert und Ihnen per E-Mail zugesandt!', Model_Entity_Message::STATUS_OK);
                        } else {
                            Service_GlobalMessageHandler::appendMessage('Es trat ein interner Fehler auf, bitte versuchen Sie es erneut!', Model_Entity_Message::STATUS_ERROR);
                        }
                    } else {
                        Service_GlobalMessageHandler::appendMessage('Die E-Mail mit dem neuen Passwort konnte leider nicht versendet werden!', Model_Entity_Message::STATUS_ERROR);
                    }
                } else {
                    Service_GlobalMessageHandler::appendMessage('Die angegebene E-Mail Adresse konnte nicht gefunden werden!', Model_Entity_Message::STATUS_ERROR);
                }
            } else {
                Service_GlobalMessageHandler::appendMessage('Bitte geben Sie eine gültige E-Mail Adresse an!', Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage('Bitte geben Sie eine gültige E-Mail Adresse an!', Model_Entity_Message::STATUS_ERROR);
        }
    }
}
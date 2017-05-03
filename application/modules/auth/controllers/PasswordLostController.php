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
                    $password = $obj_tools->generatePasswort();

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

                    $replacements = [
                        'USER_NAME' => $str_user_name,
                        'PROJECT_NAME' => PROJECT_NAME,
                        'PROJECT_URL' => PROJECT_URL,
                        'PASSWORD' => $password
                    ];

                    $templateReplacer = new CAD_Tool_TemplateHandler();
                    $mailContent = $this->translate('text_password_changed_success');
                    $mailContent = $templateReplacer->replace($replacements, $mailContent);

                    $obj_mail = new Zend_Mail("UTF-8");
                    $obj_mail->addBcc('andreas.kempe@byte-artist.de');
                    $obj_mail->addTo($str_email, $str_user_name);
                    $obj_mail->setBodyHtml($mailContent);
                    $obj_mail->setFrom("webservice@byte-artist.de", "Webservice " . $this->translate('of') . ' '. PROJECT_NAME);
                    $obj_mail->setSubject($this->translate('subject_password_changed') .' ' . PROJECT_NAME);

                    $result = $obj_mail->send();

                    if ($result) {
                        $a_data = array();
                        $a_data['user_password'] = md5($password);

                        if ($obj_db_users->updateUser($a_data, $user->offsetGet('user_id'))) {
                            Service_GlobalMessageHandler::appendMessage($this->translate('password_successfully_changed_and_send'), Model_Entity_Message::STATUS_OK);
                        } else {
                            Service_GlobalMessageHandler::appendMessage($this->translate('internal_error_please_try_again_later'), Model_Entity_Message::STATUS_ERROR);
                        }
                    } else {
                        Service_GlobalMessageHandler::appendMessage($this->translate('send_mail_with_new_password_failed'), Model_Entity_Message::STATUS_ERROR);
                    }
                } else {
                    Service_GlobalMessageHandler::appendMessage($this->translate('entered_email_does_not_exist'), Model_Entity_Message::STATUS_ERROR);
                }
            } else {
                Service_GlobalMessageHandler::appendMessage($this->translate('please_enter_valid_email'), Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage($this->translate('please_enter_valid_email'), Model_Entity_Message::STATUS_ERROR);
        }
    }
}
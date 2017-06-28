<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:41
 */

namespace Auth;

use \AbstractController;
use Zend_Validate_EmailAddress;
use Auth\Model\DbTable\Users;
use Zend_Db_Table_Row_Abstract;
use CAD_Tools;
use CAD_Tool_TemplateHandler;
use Zend_Mail;
use Service\GlobalMessageHandler;
use Model\Entity\Message;




require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class PasswordLostController extends AbstractController
{

    public function indexAction()
    {
    }

    public function resetPasswordAction() 
    {

        if ($this->getRequest()->isPost()
            && $this->getParam('password_lost_email')
        ) {
            $str_email = base64_decode($this->getParam('password_lost_email'));

            $obj_valid_email = new Zend_Validate_EmailAddress();
            $obj_db_users = new Users();

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
                            GlobalMessageHandler::appendMessage($this->translate('password_successfully_changed_and_send'), Message::STATUS_OK);
                        } else {
                            GlobalMessageHandler::appendMessage($this->translate('internal_error_please_try_again_later'), Message::STATUS_ERROR);
                        }
                    } else {
                        GlobalMessageHandler::appendMessage($this->translate('send_mail_with_new_password_failed'), Message::STATUS_ERROR);
                    }
                } else {
                    GlobalMessageHandler::appendMessage($this->translate('entered_email_does_not_exist'), Message::STATUS_ERROR);
                }
            } else {
                GlobalMessageHandler::appendMessage($this->translate('please_enter_valid_email'), Message::STATUS_ERROR);
            }
        } else {
            GlobalMessageHandler::appendMessage($this->translate('please_enter_valid_email'), Message::STATUS_ERROR);
        }
    }
}
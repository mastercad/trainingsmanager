<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:40
 */

namespace Auth;

use \AbstractController;
use Auth\Model\DbTable\Users;
use Zend_Validate_EmailAddress;
use Service\GlobalMessageHandler;
use Model\Entity\Message;
use CAD_Tools;
use CAD_Tool_TemplateHandler;
use Zend_Mail;




require_once APPLICATION_PATH . '/controllers/AbstractController.php';

class RegisterController extends AbstractController
{

    public function indexAction()
    {
    }

    public function saveAction() 
    {

        $a_params = $this->getRequest()->getParams();

        $str_register_email = null;
        $str_register_vorname = null;
        $str_register_passwort = null;
        $str_register_nachname = null;

        if ($this->getRequest()->isPost()) {
            $a_data = array();
            $obj_db_users = new Users();
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

            if($b_valid_email) {
                $b_email_exists = $this->checkEmailExists($str_register_email);
            }

            // valid und existiert noch nicht
            if($b_valid_email 
                && !$b_email_exists
            ) {
                $a_data['user_email'] = $str_register_email;
                $a_data['user_login'] = $str_register_email;
            }
            // valid aber existiert
            else if($b_valid_email
                && $b_email_exists
            ) {
                $b_all_valid = false;
                GlobalMessageHandler::appendMessage($this->translate('error_email_already_registered'), Message::STATUS_ERROR);
                // nicht valid
            } else if(!$b_valid_email) {
                $b_all_valid = false;
                GlobalMessageHandler::appendMessage($this->translate('please_enter_valid_email'), Message::STATUS_ERROR);
            }

            if(strlen(trim($str_register_vorname)) > 0) {
                $a_data['user_first_name'] = $str_register_vorname;
            }

            if(strlen(trim($str_register_nachname)) > 0) {
                $a_data['user_last_name'] = $str_register_nachname;
            }

            if(true === $b_all_valid) {
                $obj_tools = new CAD_Tools();
                $password = $obj_tools->generatePasswort();
                $a_data['user_password'] = md5($password);
                $a_data['user_state_fk'] = 1;
                $a_data['user_right_group_fk'] = 2;

                $result = $obj_db_users->saveUser($a_data);

                if ($result) {
                    GlobalMessageHandler::appendMessage($this->translate('login_successfully_created'), Message::STATUS_OK);

                    $str_user_name = $str_register_email;
                    if(strlen(trim($str_register_vorname))) {
                        $str_user_name = $str_register_vorname . " ";
                    }
                    if(strlen(trim($str_register_nachname))) {
                        $str_user_name .= $str_register_nachname;
                    }

                    $replacements = [
                        'USER_NAME' => $str_user_name,
                        'PROJECT_NAME' => PROJECT_NAME,
                        'PROJECT_URL' => PROJECT_URL,
                        'PASSWORD' => $password
                    ];

                    $templateReplacer = new CAD_Tool_TemplateHandler();
                    $mailContent = $this->translate('text_registration_success');
                    $mailContent = $templateReplacer->replace($replacements, $mailContent);

                    $obj_mail = new Zend_Mail("UTF-8");
                    $obj_mail->addTo($str_register_email, $str_user_name);
                    $obj_mail->setBodyHtml($mailContent);
                    $obj_mail->addBcc('andreas.kempe@byte-artist.de');
                    $obj_mail->setFrom("webservice@byte-artist.de", "Webservice " . $this->translate('of') . ' '. PROJECT_NAME);
                    $obj_mail->setSubject($this->translate('subject_registration_success') . ' ' . PROJECT_NAME);

                    $result = $obj_mail->send();
                } else {
                    GlobalMessageHandler::appendMessage($this->translate('unknown_error_while_create_login'), Message::STATUS_ERROR);
                }
            }
        }
    }

    private function checkEmailExists($str_email)
    {
        $obj_db_users = new Users();
        $b_result = $obj_db_users->checkEmailExists($str_email);

        return $b_result;
    }
}
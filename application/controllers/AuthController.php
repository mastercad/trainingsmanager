<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

/**
 * Class AuthController
 */
class AuthController extends AbstractController {

    /**
     *
     */
    public function indexAction() {
    }

    /**
     *
     */
    public function loginFormAction() {
    }

    /**
     *
     */
    public function loginAction() {
        $obj_user_identity = Zend_Auth::getInstance()->getIdentity();

        $a_messages = array();

        if($obj_user_identity) {
            $i_count_messages = count($a_messages);
            $a_messages[$i_count_messages]['type']= "meldung";
            $a_messages[$i_count_messages]['message'] = "Login erfolgreich!";
            $a_messages[$i_count_messages]['result'] = true;
        } else {
            $a_messages = array();
            $a_fehler = "Login Fehlgeschlagen!<br /><br />";

            if(is_array($this->view->a_messages)) {
                $a_fehler .= implode("<br />", $this->view->a_messages);
            }

            $i_count_messages = count($a_messages);
            $a_messages[$i_count_messages]['type']= "fehler";
            $a_messages[$i_count_messages]['message'] = $a_fehler;
            $a_messages[$i_count_messages]['result'] = true;

// 				echo $this->render('auth/login-fail-options.phtml');
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }

    /**
     *
     */
    public function logoutAction() {
/*
        unset($_SESSION['user_login']);
        unset($_SESSION['user_password']);

        $auth = CAD_Auth::getInstance();
        $auth->clearIdentity();

        Zend_Session::namespaceUnset('Zend_Auth_Ghost');
        Zend_Session::namespaceUnset('Zend_Auth');
*/
    }

    /**
     *
     */
    public function registerFormAction() {
    }

    /**
     * @throws Zend_Mail_Exception
     */
    public function registerAction() {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        $str_register_email = null;
        $str_register_first_name = null;
        $str_register_password = null;
        $str_register_name = null;

        if($this->getRequest()->isPost()) {
            $a_data = array();
            $obj_db_users = new Model_DbTable_Users();
            $b_all_valid = true;

            if(isset($a_params['register_email'])) {
                $str_register_email = base64_decode($a_params['register_email']);
            }
            if(isset($a_params['register_first_name'])) {
                $str_register_first_name = base64_decode($a_params['register_first_name']);
            }
            if(isset($a_params['register_password'])) {
                $str_register_password = base64_decode($a_params['register_password']);
            }
            if(isset($a_params['register_name'])) {
                $str_register_name = base64_decode($a_params['register_name']);
            }

            $obj_validate_email = new Zend_Validate_EmailAddress();
            $b_valid_email = $obj_validate_email->isValid($str_register_email);
            $b_email_exists = false;

            if($b_valid_email) {
                $b_email_exists = $this->checkEmailExists($str_register_email);
            }

            // valid und existiert noch nicht
            if (true === $b_valid_email
                && false === $b_email_exists
            ) {
                $a_data['user_email'] = $str_register_email;
                $a_data['user_login'] = $str_register_email;
            // valid aber existiert
            } else if (true === $b_valid_email
                && false === $b_email_exists
            ) {
                $b_all_valid = false;
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Diese E-Mail Adresse ist bereits registriert!";
                $a_messages[$i_count_messages]['result'] = false;
            // nicht valid
            } else if (false === $b_valid_email) {
                $b_all_valid = false;
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Bitte geben Sie eine gültige E-Mail Adresse ein!";
                $a_messages[$i_count_messages]['result'] = false;
            }

            if (strlen(trim($str_register_password)) >= 8) {
                $a_data['user_password'] = md5($str_register_password);
            } else {
                $b_all_valid = false;
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type'] = "fehler";
                $a_messages[$i_count_messages]['message'] = "Bitte geben Sie ein Passwort mit mindestens 8 Stellen ein!";
                $a_messages[$i_count_messages]['result'] = false;
            }

            if (strlen(trim($str_register_first_name)) > 0) {
                $a_data['user_first_name'] = $str_register_first_name;
            }

            if (strlen(trim($str_register_name)) > 0) {
                $a_data['user_last_name'] = $str_register_name;
            }

            if ( true === $b_all_valid) {
                $a_data['user_state_fk'] = 2;
                $a_data['user_right_group_fk'] = 2;

                $result = $obj_db_users->saveUser($a_data);

                if ($result) {
                    $i_count_messages = count($a_messages);
                    $a_messages[$i_count_messages]['type'] = "meldung";
                    $a_messages[$i_count_messages]['message'] = "Ihr Login wurde erfolgreich angelegt!";
                    $a_messages[$i_count_messages]['confirm_func'] = "CAD.removeLastObject();";
                    $a_messages[$i_count_messages]['result'] = true;

                    $str_user_name = $str_register_email;
                    if(strlen(trim($str_register_first_name)))
                    {
                        $str_user_name = $str_register_first_name . " ";
                    }
                    if(strlen(trim($str_register_name)))
                    {
                        $str_user_name .= $str_register_name;
                    }

                    $str_message = "Hallo " . $str_user_name . ",<br /><br />";
                    $str_message .= "Sie haben sich erfolgreich auf www.byte-artist.de ";
                    $str_message .= "registriert! Vielen Dank für Ihr Vertrauen!<br /><br />";
                    $str_message .= '<a href="http://www.byte-artist.de">Klicken Sie hier, um sich gleich anzumelden.</a><br /><br />';
                    $str_message .= 'Mit freundlichen Grüßen und einen angenehmen Tag<br />';
                    $str_message .= 'Ihr byte-artist.';

                    $obj_mail = new Zend_Mail("UTF-8");
                    $obj_mail->addTo($str_register_email, $str_user_name);
                    $obj_mail->setBodyHtml($str_message);
                    $obj_mail->setFrom("webservice@byte-artist.de", "Webservice des byte-artist.de");
                    $obj_mail->setSubject('Ihre Registrierung auf byte-artist.de');

                    $result = $obj_mail->send();
                } else {
                    $i_count_messages = count($a_messages);
                    $a_messages[$i_count_messages]['type'] = "fehler";
                    $a_messages[$i_count_messages]['message'] = "Beim Anlegen Ihres Login ist ein Fehler aufgetreten!";
                    $a_messages[$i_count_messages]['result'] = false;
                }
            }
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }

    /**
     * @param $str_email
     * @return bool
     */
    public function checkEmailExists($str_email) {
        $obj_db_users = new Model_DbTable_Users();
        $b_result = $obj_db_users->checkEmailExists($str_email);

        return $b_result;
    }

    /**
     * @throws Zend_Mail_Exception
     */
    public function passwordForgottenAction() {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        if (true === $this->getRequest()->isPost()
            && true === isset($a_params['password_forgotten_email'])
        ) {
            $str_email = base64_decode($a_params['password_forgotten_email']);

            $obj_tools = new CAD_Tools();
            $obj_valid_email = new Zend_Validate_EmailAddress();
            $obj_db_users = new Model_DbTable_Users();
            $str_new_password = $obj_tools->generatePassword();

            $b_email_valid = $obj_valid_email->isValid($str_email);
            if (true === $b_email_valid) {
                $a_user = $obj_db_users->findUserByEmail($str_email);
                if (false !== $a_user
                    && 0 < count($a_user)
                ) {
                    $str_user_name = $str_email;
                    if (true === isset($a_user['user_first_name'])
                        && 0 < strlen(trim($a_user['user_first_name']))
                    ) {
                        $str_user_name = $a_user['user_first_name'] . " ";
                    }
                    if (true === isset($a_user['user_last_name'])
                        && 0 <strlen(trim($a_user['user_last_name']))
                    ) {
                        $str_user_name .= $a_user['user_last_name'];
                    }

                    $str_message = "Hallo " . $str_user_name . ",<br /><br />";
                    $str_message .= "Sie haben ein neues Paswort für den Login-Bereich ";
                    $str_message .= "auf www.byte-artist.de angefordert.<br /><br />";
                    $str_message .= "Es wurde erfolgreich geändert und lautet <strong>" . $str_new_password . "</strong><br /><br />";
                    $str_message .= '<a href="http://www.byte-artist.de">Klicken Sie hier, um sich gleich anzumelden.</a><br /><br />';
                    $str_message .= 'Mit freundlichen Grüßen und einen angenehmen Tag<br />';
                    $str_message .= 'Ihr byte-artist.';

                    $obj_mail = new Zend_Mail("UTF-8");
                    $obj_mail->addTo($str_email, $str_user_name);
                    $obj_mail->setBodyHtml($str_message);
                    $obj_mail->setFrom("webservice@byte-artist.de", "Webservice des byte-artist.de");
                    $obj_mail->setSubject('Ihr neues Passwort für byte-artist.de');

                    $result = $obj_mail->send();

                    if ($result) {
                        $a_data = array();
                        $a_data['user_password'] = md5($str_new_password);

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
        } else {
            $i_count_messages = count($a_messages);
            $a_messages[$i_count_messages]['type'] = "fehler";
            $a_messages[$i_count_messages]['message'] = "Bitte geben Sie eine E-Mail Adresse an!";
            $a_messages[$i_count_messages]['result'] = false;
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }

    /**
     *
     */
    public function passwordForgottenFormAction() {
    }

    /**
     *
     */
    public function validateRegistrationAction() {
    }
}
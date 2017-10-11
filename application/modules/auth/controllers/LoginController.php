<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.06.15
 * Time: 23:39
 */
#namespace Auth;

require_once APPLICATION_PATH . '/controllers/AbstractController.php';

//use \AbstractController;
//use Zend_Auth;

class Auth_LoginController extends AbstractController
{
    public function indexAction()
    {
        $oZendAuth = Zend_Auth::getInstance();
        $obj_user_identity = $oZendAuth->getIdentity();
        $a_params = $this->getRequest()->getParams();

        $a_messages = array();

        if('guest' !== $obj_user_identity->user_right_group_name) {
            if (isset($a_params['ajax'])) {
                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type']= "meldung";
                $a_messages[$i_count_messages]['message'] = "Login erfolgreich!";
                $a_messages[$i_count_messages]['result'] = true;
            } else {
                echo "Login Erfolgreich!";
            }
        } elseif (true === isset($oZendAuth->b_logged_in)
            && $oZendAuth->b_logged_in == true
        ) {
            if (isset($a_params['ajax'])) {
                $a_messages = array();
                $a_fehler = "Login Fehlgeschlagen!<br /><br />";

                if(is_array($this->view->a_messages)) {
                    $a_fehler .= implode("<br />", $this->view->a_messages);
                }

                $i_count_messages = count($a_messages);
                $a_messages[$i_count_messages]['type']= "fehler";
                $a_messages[$i_count_messages]['message'] = $a_fehler;
                $a_messages[$i_count_messages]['result'] = true;
            } else {
                echo $this->render('login/fail-options.phtml');
            }
        } else {
            echo $this->view->render('login/form.phtml');
        }

        if (0 < count($a_messages)) {
            $this->view->assign('json_string', json_encode($a_messages));
        }
    }
}
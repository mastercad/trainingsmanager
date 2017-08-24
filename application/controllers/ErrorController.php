<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

/**
 * Class ErrorController
 */
class ErrorController extends AbstractController
{

    /**
     * error action
     *
     * @throws \Zend_Controller_Response_Exception
     */
    public function errorAction() 
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors
            || !$errors instanceof ArrayObject
        ) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
        case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            // 404 error -- controller or action not found
            $this->getResponse()->setHttpResponseCode(404);
            $priority = Zend_Log::NOTICE;
            $this->view->message = 'Page not found';
            break;
        default:
            // application error
            $this->getResponse()->setHttpResponseCode(500);
            $priority = Zend_Log::CRIT;
            $this->view->message = 'Application error';
            break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message . ' : ' . print_r($errors->exception, true), $priority, print_r($errors->exception, true));
            $log->log('Request Parameters : ' . print_r($errors->request->getParams(), true), $priority, print_r($errors->request->getParams(), true));
            $log->log('Parameters : ' . print_r($errors, true), $priority, print_r($errors, true));
        }
        
        // conditionally display exceptions
        if (true === $this->getInvokeArg('displayExceptions')) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request = $errors->request;
    }

    /**
     * no access action
     */
    public function noAccessAction()
    {
        $a_params = $this->getRequest()->getParams();

        //        echo "<pre>";
        //        var_dump($a_params);
        //        echo "</pre>";
    }

    /**
     * login fail action
     */
    public function loginFailAction()
    {
        $a_params = $this->getRequest()->getParams();

        echo "Login fehlgeschlagen!<br />";
    }

    /**
     * return log object
     *
     * @return bool|mixed
     *
     * @throws \Zend_Exception
     */
    private function getLog() 
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (true === $bootstrap->hasResource('Log')) {
            return $bootstrap->getResource('Log');
        } else if (Zend_Registry::get(ZEND_LOGGER)) {
            return Zend_Registry::get(ZEND_LOGGER);
        }
        return false;
    }


}


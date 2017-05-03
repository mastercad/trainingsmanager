<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.05.17
 * Time: 13:05
 */

class MessageHelper extends Zend_Controller_Action_Helper_Abstract {

    public function postDispatch() {
        if ($this->getRequest()->getParam('ajax')) {
            $messageEntity = Service_GlobalMessageHandler::getMessageEntity();
            if (empty($messageEntity->getState())) {
                $messageEntity->setState($this->getResponse()->getHttpResponseCode());
            }
            $messageEntity->setHtmlContent(base64_encode($this->getResponse()->getBody()));
            $this->getResponse()->clearBody();
            $this->getResponse()->setBody($messageEntity);
        }
    }
}
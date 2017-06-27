<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.05.17
 * Time: 13:05
 */

use Service\GlobalMessageHandler;

class MessageHelper extends Zend_Controller_Action_Helper_Abstract {

    public function postDispatch() {
        if ($this->getRequest()->getParam('ajax')) {
            $messageEntity = GlobalMessageHandler::getMessageEntity();
            if (empty($messageEntity->getState())) {
                $messageEntity->setState($this->getResponse()->getHttpResponseCode());
            } else {
                $state = $messageEntity->getState();
                if (1 == $state
                    || 200 == $state
                ) {
                    $state = 200;
                } else {
                    $state *= 100;
                }
                $messageEntity->setState($state);
            }
            $messageEntity->setHtmlContent(base64_encode($this->getResponse()->getBody()));
            $this->getResponse()->clearBody();
            $this->getResponse()->setBody($messageEntity);
        }
    }
}
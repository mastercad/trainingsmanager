<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.05.17
 * Time: 14:56
 */

namespace Service;

use Model\Entity\Message;

class GlobalMessageHandler {

    /** @var Message */
    static private $messageEntity = null;

    /** @var bool */
    static private $init = false;

    /**
     * @var GlobalMessageHandler
     */
    static private $instance = null;

    /**
     *
     */
    private function __construct() {
        static::init();
        static::$messageEntity = new Message();
    }

    /**
     *
     */
    private static function init() {
        if (!static::$init) {
            static::$init = true;
            static::$instance = new static();
        }
    }

    /**
     * @return GlobalMessageHandler
     */
    public static function getInstance() {
        static::init();
        return static::$instance;
    }

    /**
     * @return Message
     */
    public static function getMessageEntity() {
        static::init();
        return static::$messageEntity;
    }

    /**
     * @param Message $messageEntity
     */
    public static function setMessageEntity($messageEntity) {
        static::init();
        static::$messageEntity = $messageEntity;
    }

    /**
     * @param string $message
     *
     * @param int $state
     */
    public static function appendMessage($message, $state = Message::STATUS_OK) {
        $messageEntity = static::getMessageEntity();
        $currentMessages = $messageEntity->getMessage();

        if (0 < strlen(trim($currentMessages))) {
            $currentMessages .= '<br />';
        }
        $messageEntity->setMessage($currentMessages.$message);

        if ($state > $messageEntity->getState()) {
            $messageEntity->setState($state);
        }
    }
}
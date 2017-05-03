<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.05.17
 * Time: 14:56
 */

class Service_GlobalMessageHandler {

    /** @var Model_Entity_Message  */
    static private $messageEntity = null;

    static private $init = false;

    static private $instance = null;

    private function __construct() {
        static::init();
        static::$messageEntity = new Model_Entity_Message();
    }

    private static function init() {
        if (!static::$init) {
            static::$init = true;
            static::$instance = new static();
        }
    }

    public static function getInstance() {
        static::init();
        return static::$instance;
    }

    /**
     * @return \Model_Entity_Message
     */
    public static function getMessageEntity() {
        static::init();
        return static::$messageEntity;
    }

    /**
     * @param $messageEntity
     */
    public static function setMessageEntity($messageEntity) {
        static::init();
        static::$messageEntity = $messageEntity;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 10:06
 */

abstract class Model_Entity_AbstractEntity {

    protected $map = [];

    public function __construct($message = null) {
        if (!empty($message)) {
            $this->setData($message);
        }
    }

    protected function setData($message) {
        if (is_array($message)) {
            $this->mapArray($message);
        } else if (is_object($message)) {
            $this->mapObject($message);
        }
    }

    private function mapArray($message) {
        foreach ($this->map as $sourceKey => $destinationKey) {
            if (array_key_exists($sourceKey, $message)) {
                $this->setDynamic($this, 'set'.ucfirst($destinationKey), $message[$sourceKey]);
            }
        }
    }

    private function mapObject($message) {
        foreach ($this->map as $sourceKey => $destinationKey) {
            $getterName = 'get'.ucfirst($sourceKey);
            if (method_exists($message, $getterName)) {
                $this->setDynamic($this, 'set'.ucfirst($destinationKey), $this->getDynamic($this, $getterName));
            }
        }
    }

    protected function setDynamic($object, $setter, $value) {
        if (method_exists($object, $setter)) {
            return call_user_func([$object, $setter], $value);
        }
        throw new BadMethodCallException(get_class($object) . ' ha no method ' . $setter);
    }

    protected function getDynamic($object, $getter) {
        if (method_exists($object, $getter)) {
            return call_user_func([$object, $getter]);
        }
        throw new BadMethodCallException(get_class($object) . ' ha no method ' . $getter);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 10:06
 */

abstract class Model_Entity_AbstractEntity {

    protected $map = [];

    private $data = [];

    private $countSetVariables = 0;

    public function __construct($message = null) {
        if (!empty($message)) {
            $this->setData($message);
        }
    }

    /**
     * @return string
     */
    public function __toString() {
        $content = [];
        foreach ($this->data as $key => $value) {
            if ($value) {
                $content[$key] = $value;
            }
        }
        return json_encode($content);
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

    public function set($key, $value) {
        if (false == is_null($value)) {
            ++$this->countSetVariables;
        } else {
            --$this->countSetVariables;
        }
        $this->data[$key] = $value;
        return $this;
    }

    public function get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function isEmpty() {
        return $this->countSetVariables > 0;
    }
}
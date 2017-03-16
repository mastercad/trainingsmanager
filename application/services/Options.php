<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.03.17
 * Time: 22:00
 */

abstract class Service_Options {

    protected $hierarchy = [];

    protected $storage = null;

    protected $storageClassName;

    protected function getStorage() {
        return $this->storage;
    }

    protected function setStorage($storage) {
        $this->storage = $storage;
        return $this;
    }

    protected function useStorage() {
        if (is_null($this->storage)) {
            $this->storage = new $this->storageClassName();
        }
        return $this->getStorage();
    }
}
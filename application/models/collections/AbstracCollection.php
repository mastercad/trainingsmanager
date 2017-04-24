<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 10:06
 */

abstract class Model_Collection_AbstractCollection {

    private $data = null;

    public function __construct($messages) {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage(new Model_Entity_Message($message));
            }
        }
    }

    public function addMessage(Model_Entity_Message $message) {
        if (!is_array($this->data)) {
            $this->data = [];
        }
        $this->data[] = $message;


    }

    protected function getData() {
        return $this->data;
    }

    protected function setData($data) {
        $this->data = $data;
        return $this;
    }
}
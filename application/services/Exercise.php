<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.04.16
 * Time: 10:12
 */

class Service_Exercise extends Service_Abstract {

    /** @var Zend_Db_Table_Abstract */
    private $storage = null;

    public function generateExerciseOptionsContent($exerciseId) {

    }

    /**
     * @param Zend_Db_Table_Abstract $storage
     *
     * @return $this
     */
    private function setStorage($storage) {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return \Zend_Db_Table_Abstract
     */
    private function getStorage() {
        return $this->storage;
    }

    /**
     * @return Zend_Db_Table_Abstract
     */
    private function useStorage() {
        if (is_null($this->storage)) {
            $this->storage = new Model_DbTable_Exercises();
        }
        return $this->getStorage();
    }
}
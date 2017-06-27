<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.04.16
 * Time: 10:12
 */

namespace Service;

use Service\AbstractService;
use Model\DbTable\Exercises;




class Exercise extends AbstractService {

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
            $this->storage = new Exercises();
        }
        return $this->getStorage();
    }
}
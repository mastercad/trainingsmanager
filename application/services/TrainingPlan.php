<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 07.05.14
 * Time: 22:15
 */ 

require_once __DIR__ . '/../models/DbTable/Exercises.php';

class Service_TrainingPlan
{
    /** @var Application_Model_DbTable_Uebungen */
    private $_oExerciseStorage = NULL;

    public function __construct() {
        $this->setExerciseStorage(new Application_Model_DbTable_Exercise());
    }

    public function searchExercise($iExerciseId) {
        return $this->_oExerciseStorage->findExerciseById($iExerciseId);
    }
    /**
     * @param null $oUebungenStorage
     */
    public function setExerciseStorage(Application_Model_DbTable_Uebungen $oExerciseStorage) {
        $this->_oExerciseStorage = $oExerciseStorage;
        return $this;
    }

    /**
     * @return null
     */
    public function getExerciseStorage() {
        return $this->_oExerciseStorage;
    }
}

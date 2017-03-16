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
    /** @var Model_DbTable_Exercises */
    private $_oExerciseStorage = NULL;

    public function __construct() {
        $this->setExerciseStorage(new Model_DbTable_Exercises());
    }

    public function searchExercise($iExerciseId) {
        return $this->_oExerciseStorage->findExerciseById($iExerciseId);
    }
    /**
     * @param null $oUebungenStorage
     */
    public function setExerciseStorage(Model_DbTable_Exercises $oExerciseStorage) {
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

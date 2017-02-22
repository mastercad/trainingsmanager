<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

class Application_Model_DbTable_TrainingDiaries extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'trainingstagebuecher';
    /**
     * @var string
     */
    protected $_primary = 'trainingstagebuch_id';

    /**
     *
     */
    public function findActualTrainingDiary() {

    }

    /**
     * @param $iTrainingPlanExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingsDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId)
    {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('trainingstagebuch_trainingsplan_uebung_fk = ' . $iTrainingPlanExerciseId,
            'trainingstagebuch_eintrag_datum DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }
}

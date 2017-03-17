<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.14
 * Time: 16:15
 */

class Model_DbTable_TrainingsXExercise extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'training_x_exercise';
    /**
     * @var string
     */
    protected $_primary = 'training_x_exercise_id';

    /**
     * @param int $iTrainingPlanXExerciseId
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function getActualTrainingByExerciseId($iTrainingPlanXExerciseId)
    {
//        $this->getAdapter()->getProfiler()->setEnabled(TRUE);
        return $this->fetchRow('training_training_plan_x_exercise_fk = ' . $iTrainingPlanXExerciseId,
            'training_create_date DESC');

//        Zend_Debug::dump($this->getAdapter()->getProfiler()->getLastQueryProfile()->getQuery());
    }

    /**
     * @param int $TrainingPlanId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getLastTrainingExercise($TrainingPlanId)
    {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->join('trainingsplaene', 'trainingsplan_id = trainingstagebuch_uebung_trainingsplan_fk')
            ->join('trainingsplan_uebungen', 'trainingsplan_uebung_trainingsplan_fk = trainingstagebuch_uebung_trainingsplan_fk')
            ->join('trainingstagebuch_trainingsplaene', 'trainingstagebuch_trainingsplan_trainingsplan_fk = trainingstagebuch_uebung_trainingsplan_fk')
//            ->join('trainingsplaene', 'trainingsplan_id = trainingstagebuch_trainingsplaene_trainingsplan_fk')
            ->join('exercises', 'uebung_id = trainingsplan_uebung_fk')
            ->where('trainingstagebuch_trainingsplan_flag_abgeschlossen != 1')
            ->order('trainingsplan_uebung_order');

        return $this->fetchAll($oSelect);
    }
}

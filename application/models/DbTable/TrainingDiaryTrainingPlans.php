<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.14
 * Time: 16:15
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table_Abstract;

/**
 * Class TrainingDiaryTrainingPlans
 *
 * @package Model\DbTable
 */
class TrainingDiaryTrainingPlans extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name 	= 'trainingstagebuch_trainingsplaene';

    /**
     * @var string
     */
    protected $_primary = 'trainingstagebuch_trainingsplan_id';

    /**
     * find actual training diary by training plan exercise
     *
     * @param int $iTrainingPlanExerciseId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingDiaryByTrainingPlanExerciseId($iTrainingPlanExerciseId) {
        return $this->fetchRow('trainingstagebuch_trainingsplan_uebung_fk = ' . $iTrainingPlanExerciseId,
            'trainingstagebuch_eintrag_datum DESC');
    }

    /**
     * find last open training plan
     *
     * @param int $iTrainingDiaryTrainingPlanId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findLastOpenTrainingPlan($iTrainingDiaryTrainingPlanId) {
        $oSelect = $this->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(FALSE);

        $oSelect
            ->joinInner($this->considerTestUserForTableName('trainingsplaene'),
                'trainingsplan_id = trainingstagebuch_trainingsplan_trainingsplan_fk')
            ->joinLeft($this->considerTestUserForTableName('trainingsplan_uebungen'),
                'trainingsplan_uebung_trainingsplan_fk = trainingstagebuch_trainingsplan_trainingsplan_fk')
            ->joinLeft($this->considerTestUserForTableName('trainingstagebuch_uebungen'),
                'trainingstagebuch_uebung_trainingsplan_uebung_fk = trainingsplan_uebung_fk')
            ->joinLeft($this->considerTestUserForTableName('exercises'), 'uebung_id = trainingsplan_uebung_fk')
            ->where('trainingstagebuch_trainingsplan_flag_abgeschlossen != 1')
            ->where('trainingstagebuch_trainingsplan_trainingsplan_fk = ' . $iTrainingDiaryTrainingPlanId)
            ->order('trainingsplan_uebung_order');

        return $this->fetchAll($oSelect);
    }
}

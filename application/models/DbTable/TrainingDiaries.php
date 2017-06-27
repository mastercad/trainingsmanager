<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.14
 * Time: 08:54
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Nette\NotImplementedException;

class TrainingDiaries extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name 	= 'training_diaries';
    /**
     * @var string
     */
    protected $_primary = 'training_diary_id';

    /**
     * @inheritdoc
     */
    function findByPrimary($id) {
        throw new NotImplementedException('Function findByPrimary not implemented yet!');
    }

    /**
     * find actual training by training plan exercise
     *
     * @param int $iTrainingPlanExerciseId
     *
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findActualTrainingByTrainingPlanExerciseId($iTrainingPlanExerciseId)
    {
        return $this->fetchRow('training_training_plan_x_exercise_fk = ' . $iTrainingPlanExerciseId,
            'training_create_date DESC');
    }
}

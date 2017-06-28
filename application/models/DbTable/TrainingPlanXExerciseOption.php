<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Model\DbTable;

use Zend_Db_Table;
use Zend_Db_Table_Rowset_Abstract;

class TrainingPlanXExerciseOption extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name 	= 'training_plan_x_exercise_option';
    /**
     * @var string
     */
    protected $_primary = 'training_plan_x_exercise_option_id';

    /**
     * find training plan exercise options by training plan exercise and optional exercise option
     *
     * @param int $iTrainingPlanExerciseId
     * @param int $exerciseOptionId optional
     *
     * @return null|Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($iTrainingPlanExerciseId, $exerciseOptionId = null) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
            'training_plan_x_exercise_id = training_plan_x_exercise_option_training_plan_exercise_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_options'),
                'exercise_option_id = training_plan_x_exercise_option_exercise_option_fk')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_option'),
                'exercise_x_exercise_option_exercise_option_fk = training_plan_x_exercise_option_exercise_option_fk '.
                'AND exercise_x_exercise_option_exercise_fk = training_plan_x_exercise_exercise_fk')
            ->where('training_plan_x_exercise_option_training_plan_exercise_fk = ?', $iTrainingPlanExerciseId);

        if (!empty($exerciseOptionId)) {
            $oSelect->where('exercise_option_id = ?', $exerciseOptionId);
        }

        return $this->fetchAll($oSelect);
    }

    /**
     * find training plan exercise option by training diary exercise
     *
     * @param int $trainingPlanXExerciseId
     *
     * @return null|Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanExerciseOptionsByTrainingDiaryExerciseId($trainingPlanXExerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $oSelect
            ->joinInner($this->considerTestUserForTableName('training_plan_x_exercise'),
                'training_plan_x_exercise_id = ' . $trainingPlanXExerciseId)
            ->joinInner($this->considerTestUserForTableName('exercises'),
                'exercise_id = training_plan_x_exercise_exercise_fk')
            ->joinInner($this->considerTestUserForTableName('exercise_options'),
                'exercise_option_id = training_plan_x_exercise_option_exercise_option_fk')
            ->where('training_plan_x_exercise_option_training_plan_exercise_fk = ?', $trainingPlanXExerciseId);

        return $this->fetchAll($oSelect);
    }

    /**
     * find training plan exercise options by training plan exercise and exercise option
     *
     * @param int $iTrainingPlanExerciseId
     * @param int $exerciseOptionId
     *
     * @return null|Zend_Db_Table_Rowset_Abstract
     */
    public function findTrainingPlanExerciseOptionsByTrainingPlanExerciseIdAndExerciseOptionId($iTrainingPlanExerciseId, $exerciseOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->joinLeft($this->considerTestUserForTableName('exercise_options'),
            'exercise_option_id = training_plan_x_exercise_option_exercise_option_fk')
            ->where('training_plan_x_exercise_option_training_plan_exercise_fk = ?', $iTrainingPlanExerciseId)
            ->where('training_plan_x_exercise_option_exercise_option_fk = ?', $exerciseOptionId);

        return $this->fetchRow($oSelect);
    }

    /**
     * save training plan exercise option data
     *
     * @param array $aData
     *
     * @return mixed
     */
    public function saveTrainingPlanExerciseOption($aData) {
        return $this->insert($aData);
    }

    /**
     * update training plan exercise option data
     *
     * @param array $aData
     * @param int $iTrainingPlanExerciseOptionId
     *
     * @return int
     */
    public function updateTrainingPlanExerciseOption($aData, $iTrainingPlanExerciseOptionId) {
        return $this->update($aData, 'training_plan_x_exercise_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    /**
     * delete training plan exercise option
     *
     * @param int $iTrainingPlanExerciseOptionId
     *
     * @return int
     */
    public function deleteTrainingPlanExerciseOption($iTrainingPlanExerciseOptionId) {
        return $this->delete('training_plan_x_exercise_option_id = ' . $iTrainingPlanExerciseOptionId);
    }

    /**
     * delete training plan exercise options by training plan exercise
     *
     * @param int $trainingPlanXExerciseId
     */
    public function deleteTrainingPlanExerciseOptionsByTrainingPlanXExerciseId($trainingPlanXExerciseId) {
        $this->delete('training_plan_x_exercise_option_training_plan_exercise_fk = ' . $trainingPlanXExerciseId);
    }
}

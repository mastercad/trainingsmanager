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

use Zend_Db_Table_Row_Abstract;

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

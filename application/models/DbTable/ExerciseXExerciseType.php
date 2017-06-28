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

use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table;
use Exception;

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class ExerciseXExerciseType extends AbstractDbTable {

    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_exercise_type';

    /**
     * @var string
     */
    protected $_primary = 'exercise_x_exercise_type_id';

    /**
     * find exercise type for exercise
     *
     * @param int $exerciseId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExerciseTypeForExercise($exerciseId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinInner($this->considerTestUserForTableName('exercises'), 'exercise_id = exercise_x_exercise_type_exercise_fk')
                ->where('exercise_x_exercise_type_exercise_fk = ?', $exerciseId);

            return $this->fetchRow($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * find exercises without exercise type
     *
     * @return bool|null|\Zend_Db_Table_Row_Abstract
     */
    public function findExercisesWithoutExerciseTypes()
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        try {
            $select->from($this->considerTestUserForTableName('exercises'), '')
                ->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_type'), 'exercise_x_exercise_type_exercise_fk = exercise_id', '')
                ->where('exercise_x_exercise_type_id IS NULL')
                ->columns(['COUNT(exercise_id) AS exerciseCount']);

            return $this->fetchRow($select);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * save given exercise exercise type data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveExerciseXExerciseType($aData) {
        try {
            return $this->insert($aData);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * update exercise exercise type data by given exercise exercise type id
     *
     * @param array $aData
     * @param int $exerciseXExerciseTypeId
     *
     * @return bool|int
     */
    public function updateExerciseXExerciseType($aData, $exerciseXExerciseTypeId) {
        try {
            return $this->update($aData, "exercise_x_exercise_type_id = '" . $exerciseXExerciseTypeId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * delete exercise exercise type
     *
     * @param $exerciseXExerciseTypeId
     *
     * @return bool|int
     */
    public function deleteExerciseXExerciseType($exerciseXExerciseTypeId) {
        try {
            return $this->delete( "exercise_x_exercise_type_id = '" . $exerciseXExerciseTypeId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}

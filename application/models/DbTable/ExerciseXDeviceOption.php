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
class ExerciseXDeviceOption extends AbstractDbTable {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_device_option';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_device_option_id';

    /**
     * find device options for exercise
     *
     * @param int $exerciseId
     * @param null|int $deviceOptionId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionsForExercise($exerciseId, $deviceOptionId = null) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect
                ->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_x_device_option_exercise_fk')
                ->joinLeft($this->considerTestUserForTableName('device_options'), 'device_option_id = exercise_x_device_option_device_option_fk')
                ->where('exercise_x_device_option_exercise_fk = ?', $exerciseId);

            if (! empty($deviceOptionId)) {
                $oSelect->where('exercise_x_device_option_device_option_fk = ?', $deviceOptionId);
            }

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * find device option for exercise
     *
     * @param int $exerciseId
     * @param int $deviceOptionId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionForExercise($exerciseId, $deviceOptionId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect
                ->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_x_device_option_exercise_fk')
                ->joinLeft($this->considerTestUserForTableName('device_x_device_group'), 'device_x_device_group_device_fk = exercise_x_device_device_fk')
                ->joinLeft($this->considerTestUserForTableName('device_groups'), 'device_group_id = device_x_device_group_device_group_fk')
                ->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
                ->joinLeft($this->considerTestUserForTableName('device_options'), 'device_option_id = exercise_x_device_option_device_option_fk')
                ->joinLeft($this->considerTestUserForTableName('device_x_device_option'), 'device_x_device_option_device_option_fk = device_option_id')
                ->where('exercise_x_device_option_exercise_fk = ?', $exerciseId)
                ->where('exercise_x_device_option_device_option_fk = ?', $deviceOptionId);

            return $this->fetchRow($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * find exercises for device option
     *
     * @param $deviceOptionId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesForDeviceOption($deviceOptionId) {
        try {
            return $this->fetchAll("exercise_x_device_option_device_option_fk = '" . $deviceOptionId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * save exercise device option data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveExerciseXDeviceOption($aData) {
        try {
            return $this->insert($aData);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * update exercise device option data by given exercise device option id
     *
     * @param array $aData
     * @param int $exerciseXDeviceOptionId
     *
     * @return bool|int
     */
    public function updateExerciseXDeviceOption($aData, $exerciseXDeviceOptionId) {
        try {
            return $this->update($aData, "exercise_x_device_option_id = '" . $exerciseXDeviceOptionId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * delete exercise device option by given exercise device option id
     *
     * @param int $exerciseXDeviceOptionId
     *
     * @return bool|int
     */
    public function deleteExerciseXDeviceOption($exerciseXDeviceOptionId) {
        try {
            return $this->delete( "exercise_x_device_option_id = '" . $exerciseXDeviceOptionId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}

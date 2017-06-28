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
class ExerciseXDevice extends AbstractDbTable {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_device';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_device_id';

    /**
     * find all devices for exercise (current one row)
     *
     * @param int $exerciseId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceForExercise($exerciseId) {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $select->joinInner($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
                ->joinLeft($this->considerTestUserForTableName('device_x_device_group'), 'device_x_device_group_device_fk = exercise_x_device_device_fk')
                ->joinLeft($this->considerTestUserForTableName('device_groups'), 'device_group_id = device_x_device_group_device_group_fk')
                ->where('exercise_x_device_exercise_fk = ?', $exerciseId);

            return $this->fetchRow($select);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * find all devices, who have exercise
     *
     * @return bool|\Zend_Db_Table_Rowset_Abstract
     */
    public function findDevicesWithExercises()
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $select->joinInner($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
                ->joinLeft($this->considerTestUserForTableName('device_x_device_group'), 'device_x_device_group_device_fk = device_id')
                ->joinLeft($this->considerTestUserForTableName('device_groups'), 'device_group_id = device_x_device_group_device_group_fk')
                ->columns([
                    'COUNT(' . $this->considerTestUserForTableName('devices') . '.device_id) AS exerciseCount',
                    $this->considerTestUserForTableName('devices') . '.device_name',
                    $this->considerTestUserForTableName('devices') . '.device_id'])
                ->order('device_name')
                ->group($this->considerTestUserForTableName('devices') . '.device_id');

            return $this->fetchAll($select);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * find all exercises without devices
     *
     * @return null|\Zend_Db_Table_Row_Abstract
     *
     * @throws \Zend_Db_Select_Exception
     */
    public function findExercisesWithoutDevices()
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)->setIntegrityCheck(false);
        $select->from($this->considerTestUserForTableName('exercises'), '')
            ->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id'. '')
            ->where('exercise_x_device_id IS NULL')
            ->columns(['COUNT(exercise_id) AS exerciseCount'])
        ;

        return $this->fetchRow($select);
    }

    /**
     * find all exercises for given device
     *
     * @param int $deviceId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesForDevice($deviceId) {
        try {
            return $this->fetchAll("exercise_x_device_device_fk = '" . $deviceId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * save given exercise device data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveExerciseXDevice($aData) {
        try {
            return $this->insert($aData);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * update exercise device  with given data by given exercise device id
     *
     * @param array $aData
     * @param int $exerciseXDeviceId
     *
     * @return bool|int
     */
    public function updateExerciseXDevice($aData, $exerciseXDeviceId) {
        try {
            return $this->update($aData, "exercise_x_device_id = '" . $exerciseXDeviceId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * delete exercise device by given exercise device id
     *
     * @param int $exerciseXDeviceId
     *
     * @return bool|int
     */
    public function deleteExerciseXDevice($exerciseXDeviceId) {
        try {
            return $this->delete( "exercise_x_device_id = '" . $exerciseXDeviceId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}

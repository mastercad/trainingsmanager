<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class Model_DbTable_ExerciseXDevice extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_device';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_device_id';

    /**
     * @param $exerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceForExercise($exerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinLeft('device_x_device_group', 'device_x_device_group_device_fk = exercise_x_device_device_fk')
                ->joinLeft('device_groups', 'device_group_id = device_x_device_group_device_group_fk')
                ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
                ->joinLeft('device_x_device_option', 'device_x_device_option_device_fk = device_id')
                ->joinLeft('device_options', 'device_option_id = device_x_device_option_device_option_fk')
                ->joinLeft('exercise_x_device_option', 'exercise_x_device_option_exercise_fk = exercise_x_device_exercise_fk')
                ->where('exercise_x_device_exercise_fk = ?', $exerciseId);

            return $this->fetchRow($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * @param $deviceId
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
     * @param $aData
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
     * @param $aData
     * @param $exerciseXDeviceId
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
     * @param $exerciseXDeviceId
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

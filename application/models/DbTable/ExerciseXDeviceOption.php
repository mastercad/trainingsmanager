<?php

/**
 * Class Application_Model_DbTable_ExerciseMuscles
 */
class Model_DbTable_ExerciseXDeviceOption extends Model_DbTable_Abstract {
    /**
     * @var string
     */
    protected $_name 	= 'exercise_x_device_option';
    /**
     * @var string
     */
    protected $_primary = 'exercise_x_device_option_id';

    /**
     * @param $exerciseId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionsForExercise($exerciseId, $deviceOptionId = null) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect
                ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_x_device_option_exercise_fk')
                ->joinLeft('device_x_device_group', 'device_x_device_group_device_fk = exercise_x_device_device_fk')
                ->joinLeft('device_groups', 'device_group_id = device_x_device_group_device_group_fk')
                ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
                ->joinLeft('device_options', 'device_option_id = exercise_x_device_option_device_option_fk')
                ->joinLeft('device_x_device_option', 'device_x_device_option_device_option_fk = device_option_id')
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
     * @param $exerciseId
     * @param $deviceOptionId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionForExercise($exerciseId, $deviceOptionId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect
                ->joinLeft('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_x_device_option_exercise_fk')
                ->joinLeft('device_x_device_group', 'device_x_device_group_device_fk = exercise_x_device_device_fk')
                ->joinLeft('device_groups', 'device_group_id = device_x_device_group_device_group_fk')
                ->joinLeft('devices', 'device_id = exercise_x_device_device_fk')
                ->joinLeft('device_options', 'device_option_id = exercise_x_device_option_device_option_fk')
                ->joinLeft('device_x_device_option', 'device_x_device_option_device_option_fk = device_option_id')
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
     * @param $deviceOptionId
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
     * @param $aData
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
     * @param $aData
     * @param $exerciseXDeviceOptionId
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
     * @param $exerciseXDeviceOptionId
     * @return bool|int
     */
    public function deleteDeviceOption($exerciseXDeviceOptionId) {
        try {
            return $this->delete( "exercise_x_device_option_id = '" . $exerciseXDeviceOptionId . "'");
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}

<?php

/**
 * Class Application_Model_DbTable_Exercises
 */
class Model_DbTable_Exercises extends Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'exercises';

    /** @var string */
    protected $_primary = 'exercise_id';

    public function findByPrimary($exerciseId) {
        $select = $this->select(static::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = ' . $exerciseId)
            ->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
            ->where('exercise_id = ?', $exerciseId);

        return $this->fetchRow($select);
    }

    /**
     * search all available exercises
     *
     * @param exerciseType
     * @param device
     *
     * @return array|bool
     */
    public function findExercises($exerciseType = null, $device = null) {
        $select = $this->select(static::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        if (is_numeric($exerciseType)
            && 0 < $exerciseType
        ) {
            $exerciseType = (int) $exerciseType;
            $select->joinInner($this->considerTestUserForTableName('exercise_x_exercise_type'), 'exercise_x_exercise_type_exercise_fk = exercise_id');
            $select->where('exercise_x_exercise_type_exercise_type_fk = ?', $exerciseType);
        } else if ('WITHOUT' == $exerciseType) {
            $select->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_type'), 'exercise_x_exercise_type_exercise_fk = exercise_id');
            $select->where('exercise_x_exercise_type_id IS NULL');
        }
        if (is_numeric($device)
            && 0 < $device
        ) {
            $device = (int) $device;
            $select->joinInner($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id');
            $select->where('exercise_x_device_device_fk = ?', $device);
        } else if ('WITHOUT' == $device) {
            $select->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id');
            $select->where('exercise_x_device_id IS NULL');
        } else {
            $select->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_type'), 'exercise_x_exercise_type_exercise_fk = exercise_id');
            $select->joinLeft($this->considerTestUserForTableName('exercise_types'), 'exercise_type_id = exercise_x_exercise_type_exercise_type_fk');
            $select->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id');
            $select->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk');
        }

        return $this->fetchAll($select);
    }

    /**
     * search all matching exercises by name piece
     *
     * @param string $sExerciseNamePiece
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByName($sExerciseNamePiece) {
        return $this->fetchAll("exercise_name LIKE('" . $sExerciseNamePiece . "')", 'exercise_name');
    }

    /**
     * search exercise by id
     *
     * @param int $iExerciseId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findExerciseById($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinLeft($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id')
                ->joinLeft($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
                ->joinLeft($this->considerTestUserForTableName('device_x_device_group'), 'device_x_device_group_device_fk = device_id')
                ->joinLeft($this->considerTestUserForTableName('device_groups'), 'device_group_id = device_x_device_group_device_group_fk')
                ->joinLeft($this->considerTestUserForTableName('device_x_device_option'), 'device_x_device_option_device_fk = device_id')
                ->joinLeft($this->considerTestUserForTableName('device_options'), 'device_option_id = device_x_device_option_device_option_fk')
                ->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_option'), 'exercise_x_exercise_option_exercise_fk = exercise_id')
                ->joinLeft($this->considerTestUserForTableName('exercise_options'), 'exercise_option_id = exercise_x_exercise_option_exercise_option_fk')
                ->joinLeft($this->considerTestUserForTableName('exercise_x_exercise_type'), 'exercise_x_exercise_type_exercise_fk = exercise_id')
                ->joinLeft($this->considerTestUserForTableName('exercise_types'), 'exercise_type_id = exercise_x_exercise_type_exercise_type_fk')
                //                ->joinLeft('')
                ->where('exercise_id = ?', $iExerciseId);

            return $this->fetchRow($oSelect);
        } catch(Exception $oExceptions) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oExceptions->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * search exercise by id
     *
     * @param int $iExerciseId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findExerciseByTrainingPlanExerciseId($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinInner($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id')
                ->joinInner($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
                ->joinInner($this->considerTestUserForTableName('device_x_device_group'), 'device_x_device_group_device_fk = device_id')
                ->joinLeft($this->considerTestUserForTableName('device_x_option'), 'device_x_option_device_fk = device_id')
                //                ->joinLeft('')
                ->where('exercise_id = ?', $iExerciseId);

            return $this->fetchRow($oSelect);
        } catch(Exception $oExceptions) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oExceptions->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * search exercise by id
     *
     * @param int $iExerciseId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findExerciseByTrainingExerciseId($iExerciseId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinInner($this->considerTestUserForTableName('exercise_x_device'), 'exercise_x_device_exercise_fk = exercise_id')
                ->joinInner($this->considerTestUserForTableName('devices'), 'device_id = exercise_x_device_device_fk')
                ->joinInner($this->considerTestUserForTableName('device_x_device_group'), 'device_x_device_group_device_fk = device_id')
                ->joinLeft($this->considerTestUserForTableName('device_x_option'), 'device_x_option_device_fk = device_id')
                //                ->joinLeft('')
                ->where('exercise_id = ?', $iExerciseId);

            return $this->fetchRow($oSelect);
        } catch(Exception $oExceptions) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oExceptions->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * search all exercises for device by device id
     *
     * @deprecated this result should search over devices or exercise_x_device!
     *
     * @param int $iDeviceId
     *
     * @return array|bool
     */
//    public function findExercisesForDevice($iDeviceId) {
//        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
//            ->setIntegrityCheck(false);
//        try {
//            $oSelect->joinInner('exercise_x_device', 'exercise_x_device_exercise_fk = exercise_id')
//                ->joinInner('devices', 'device_id = exercise_x_device_device_fk')
//                ->where('device_id = ?', $iDeviceId);
//            return $this->fetchAll($oSelect);
//        } catch(Exception $oExceptions) {
//            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
//            echo "Meldung : " . $oExceptions->getMessage() . "<br />";
//            return false;
//        }
//    }

    /**
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveExercise($aData) {
		try {
            return $this->insert($aData);
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
		}
	}

    /**
     * update exercise by id
     *
     * @param array $aData
     * @param int $iExerciseId
     *
     * @return bool|int
     */
    public function updateExercise($aData, $iExerciseId) {
		try {
            return $this->update( $aData, "exercise_id = '" . $iExerciseId . "'");
		} catch(Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * delete exercise by id
     *
     * @param int $iExerciseId
     *
     * @return bool|int
     */
    public function deleteExercise($iExerciseId) {
        try {
            return $this->delete("exercise_id = '" . $iExerciseId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * delete all exercises by device id
     *
     * @param int $iDeviceId
     *
     * @return bool|int
     */
//    public function deleteExerciseByDeviceId($iDeviceId)
//    {
//        try {
//            return $this->delete("uebung_geraet_fk = '" . $iDeviceId . "'");
//        } catch(Exception $oException) {
//            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
//            echo "Meldung : " . $oException->getMessage() . "<br />";
//            return false;
//        }
//    }
}

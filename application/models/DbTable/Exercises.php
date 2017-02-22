<?php

/**
 * Class Application_Model_DbTable_Exercises
 */
class Application_Model_DbTable_Exercises extends Application_Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'uebungen';

    /** @var string */
    protected $_primary = 'uebung_id';

    /**
     * search all available exercises
     *
     * @return array|bool
     */
    public function findExercises() {
        return $this->fetchAll(null, 'uebung_name');
    }

    /**
     * search all matching exercises by name piece
     *
     * @param string $sExerciseNamePiece
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExercisesByName($sExerciseNamePiece) {
        return $this->fetchAll("uebung_name LIKE('" . $sExerciseNamePiece . "')", 'uebung_name');
    }

    /**
     * search exercise by id
     *
     * @param int $iExerciseId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findExerciseById($iExerciseId) {
		$oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
					   ->setIntegrityCheck(false);
		try {
            $oSelect->join('geraete', 'geraet_id = uebung_geraet_fk')
                ->where('uebung_id = ?', $iExerciseId);

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
     * @param int $iDeviceId
     *
     * @return array|bool
     */
    public function findExerciseForDevice($iDeviceId) {
        return $this->fetchAll("uebung_geraet_fk = '" . $iDeviceId . "'");
    }

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
            return $this->update( $aData, "uebung_id = '" . $iExerciseId . "'");
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
            return $this->delete("uebung_id = '" . $iExerciseId . "'");
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
    public function deleteExerciseByDeviceId($iDeviceId)
    {
        try {
            return $this->delete("uebung_geraet_fk = '" . $iDeviceId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}

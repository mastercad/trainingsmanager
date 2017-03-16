<?php

/**
 * Class Application_Model_DbTable_Muscles
 */
class Model_DbTable_Muscles extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'muscles';
    /**
     * @var string
     */
    protected $_primary = 'muscle_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMuscles() {
        return $this->fetchAll(null, 'muscle_name');
    }

    /**
     * @param string $muscleName
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesByName($muscleName) {
        return $this->fetchAll("muscle_name LIKE('" . $muscleName . "')", 'muscle_name');
    }

    /**
     * @param $iMuscleId
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findMuscle($iMuscleId) {
		try {
            return $this->fetchRow("muscle_id = '" . $iMuscleId . "'");
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $aData
     * @return bool|mixed
     */
    public function saveMuscle($aData) {
		try {
            return $this->insert($aData);
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $aData
     * @param $iMuscleId
     * @return bool|int
     */
    public function updateMuscle($aData, $iMuscleId) {
		try {
            return $this->update($aData, "muscle_id = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $iMuscleId
     * @return bool|int
     */
    public function deleteMuscle($iMuscleId) {
		try {
            return $this->delete("muscle_id = '" . $iMuscleId . "'");

        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}
}
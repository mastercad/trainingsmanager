<?php

/**
 * Class Application_Model_DbTable_MuscleGroups
 */
class Model_DbTable_MuscleGroups extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'muscle_groups';
    /**
     * @var string
     */
    protected $_primary = 'muscle_group_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMuscleGroups() {
        return $this->fetchAll(null, "muscle_group_name");
    }

    /**
     * @param $iMuscleGroupId
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroup($iMuscleGroupId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                                   ->setIntegrityCheck(false);
        try {
            $oSelect->where("muscle_group_id = '" . $iMuscleGroupId . "'");

            return $this->fetchRow($oSelect);
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}

    /**
     * @param $sMuscleGroupName
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroupsByName($sMuscleGroupName) {
        try {
            return $this->fetchAll("muscle_group_name LIKE( '" . $sMuscleGroupName . "')", 'muscle_group_name');
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
    public function saveMuscleGroup($aData) {
		try {
            return $this->insert($aData);
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $aData
     * @param $iMuscleGroupId
     * @return bool|int
     */
    public function updateMuscleGroup($aData, $iMuscleGroupId) {
		try {
            return $this->update($aData, "muscle_group_id = '" . $iMuscleGroupId . "'");
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * @param $iMuscleGroupId
     * @return bool|int
     */
    public function deleteMuscleGroup($iMuscleGroupId) {
		try {
            return $this->delete("muscle_group_id = '" . $iMuscleGroupId . "'");
		} catch( Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}
}

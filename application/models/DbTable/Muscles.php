<?php

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Exception;

/**
 * Class Application_Model_DbTable_Muscles
 */
class Muscles extends AbstractDbTable
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
     * find all muscles
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMuscles() {
        return $this->fetchAll(null, 'muscle_name');
    }

    /**
     * find muscle by name
     *
     * @param string $muscleName
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesByName($muscleName) {
        return $this->fetchAll("muscle_name LIKE('" . $muscleName . "')", 'muscle_name');
    }

    /**
     * find muscle
     *
     * @param int $iMuscleId
     *
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
     * find all muscles by muscle group
     *
     * @param int $muscleGroupId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMusclesByMuscleGroupId($muscleGroupId) {

        $select = $this->select(self::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        $select->joinInner($this->considerTestUserForTableName('muscle_x_muscle_group'),
            'muscle_x_muscle_group_muscle_fk = muscle_id AND muscle_x_muscle_group_muscle_group_fk = ' . $muscleGroupId);

        return $this->fetchAll($select);
    }

    /**
     * save muscle data
     *
     * @param array $aData
     *
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
     * update muscle data by given muscle id
     *
     * @param array $aData
     * @param int $iMuscleId
     *
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
     * delete muscle
     *
     * @param int $iMuscleId
     *
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
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
 * Class Application_Model_DbTable_MuscleGroupMuscles
 */
class MuscleXMuscleGroup extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name     = 'muscle_x_muscle_group';

    /**
     * @var string
     */
    protected $_primary = 'muscle_x_muscle_group_id';

    /**
     * find muscles by muscle group
     *
     * @param int $iMuscleGroupId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMusclesByMuscleGroupId($iMuscleGroupId) 
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->join($this->considerTestUserForTableName('muscles'), 'muscle_id = muscle_x_muscle_group_muscle_fk')
                ->where('muscle_x_muscle_group_muscle_group_fk = ?', $iMuscleGroupId);

            return $this->fetchAll($oSelect);
        } catch(Exception $oException) {
               echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
               echo "Meldung : " . $oException->getMessage() . "<br />";
               return false;
        }
    }

    /**
     * find muscle group by muscle
     *
     * @param int $iMuscleId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroupByMuscleId($iMuscleId) 
    {
        try {
            return $this->fetchRow("muscle_x_muscle_group_muscle_fk = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * save muscle muscle group data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveMuscleXMuscleGroup($aData) 
    {
        try {
            return $this->insert($aData);
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * update muscle group muscle data
     *
     * @param array $aData
     * @param int   $iMuscleGroupMuscleId
     *
     * @return bool|int
     */
    public function updateMuscleGroupMuscle($aData, $iMuscleGroupMuscleId) 
    {
        try {
            return $this->update($aData, "muscle_x_muscle_group_id = '" . $iMuscleGroupMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete muscle group muscle
     *
     * @param int $iMuscleGroupMuscleId
     * @param int $muscleGroupId
     *
     * @return bool|int
     */
    public function deleteMuscleGroupMuscle($iMuscleGroupMuscleId, $muscleGroupId)
    {
        try {
            return $this->delete(
                "muscle_x_muscle_group_muscle_fk = '" . $iMuscleGroupMuscleId .
                "' AND muscle_x_muscle_group_muscle_group_fk = '" . $muscleGroupId . "'"
            );
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete all muscle groups muscles by muscle group
     *
     * @param int $iMuscleGroupId
     *
     * @return bool|int
     */
    public function deleteAllMuscleGroupsMusclesByMuscleGroupId($iMuscleGroupId)
    {
        try {
            return $this->delete("muscle_x_muscle_group_muscle_group_fk = '" . $iMuscleGroupId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete all muscle groups by muscle
     *
     * @param int $iMuscleId
     *
     * @return bool|int
     */
    public function deleteAllMuscleGroupsMusclesByMuscleId($iMuscleId)
    {
        try {
            return $this->delete("muscle_x_muscle_group_muscle_fk = '" . $iMuscleId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete muscle group
     *
     * @param int $muscleGroupId
     *
     * @return bool|int
     */
    public function deleteMuscleGroupById($muscleGroupId) 
    {
        try {
            return $this->delete("muscle_x_muscle_group_id = '" . $muscleGroupId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
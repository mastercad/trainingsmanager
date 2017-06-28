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
 * Class Application_Model_DbTable_MuscleGroups
 */
class MuscleGroups extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name     = 'muscle_groups';
    /**
     * @var string
     */
    protected $_primary = 'muscle_group_id';

    /**
     * find all muscle groups
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllMuscleGroups() 
    {
        return $this->fetchAll(null, "muscle_group_name");
    }

    /**
     * find muscle group
     *
     * @param int $iMuscleGroupId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroup($iMuscleGroupId) 
    {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
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
     * find muscle group by name
     *
     * @param string $sMuscleGroupName
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findMuscleGroupsByName($sMuscleGroupName) 
    {
        try {
            return $this->fetchAll("muscle_group_name LIKE( '" . $sMuscleGroupName . "')", 'muscle_group_name');
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * save muscle group data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveMuscleGroup($aData) 
    {
        try {
            return $this->insert($aData);
        } catch( Exception $oException) {
               echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
               echo "Meldung : " . $oException->getMessage() . "<br />";
               return false;
        }
    }

    /**
     * update muscle group data by given muscle group id
     *
     * @param array $aData
     * @param int   $iMuscleGroupId
     *
     * @return bool|int
     */
    public function updateMuscleGroup($aData, $iMuscleGroupId) 
    {
        try {
            return $this->update($aData, "muscle_group_id = '" . $iMuscleGroupId . "'");
        } catch( Exception $oException) {
               echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
               echo "Meldung : " . $oException->getMessage() . "<br />";
               return false;
        }
    }

    /**
     * delte muscle group
     *
     * @param $iMuscleGroupId
     *
     * @return bool|int
     */
    public function deleteMuscleGroup($iMuscleGroupId) 
    {
        try {
            return $this->delete("muscle_group_id = '" . $iMuscleGroupId . "'");
        } catch( Exception $oException) {
               echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
               echo "Meldung : " . $oException->getMessage() . "<br />";
               return false;
        }
    }
}

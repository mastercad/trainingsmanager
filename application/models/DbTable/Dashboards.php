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

use Exception;
use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;

/**
 * Class Application_Model_DbTable_DeviceGroups
 */
class Dashboards extends AbstractDbTable
{
    /**
     * @var string 
     */
    protected $_name     = 'dashboards';

    /**
     * @var string 
     */
    protected $_primary = 'dashboard_id';

    /**
     * find all dashboards in database
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDashboards() 
    {
        return $this->fetchAll(null, "dashboard_name");
    }

    /**
     * find current active dashboard for given user
     *
     * @param int $userId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findActiveDashboardByUserId($userId) 
    {
        try {
            return $this->fetchRow("dashboard_flag_active = 1 AND dashboard_user_fk = '" . $userId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * insert the given data in dashboard table
     *
     * @param $aData
     *
     * @return bool|mixed
     */
    public function saveDashboard($aData) 
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
     * update the dashboard by given id with given data
     *
     * @param $aData
     * @param $dashboardId
     *
     * @return bool|int
     */
    public function updateDashboard($aData, $dashboardId) 
    {
        try {
            return $this->update($aData, "dashboard_id = '" . $dashboardId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete given dashboard
     *
     * @param $dashboardId
     *
     * @return bool|int
     */
    public function deleteDashboard($dashboardId) 
    {
        try {
            return $this->delete("dashboard_id = '" . $dashboardId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
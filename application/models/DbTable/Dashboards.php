<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Application_Model_DbTable_DeviceGroups
 */
class Model_DbTable_Dashboards extends Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'dashboards';

    /** @var string */
    protected $_primary = 'dashboard_id';

    function findByPrimary($id) {
        return $this->fetchRow('dashboard_id = ' . intval($id));
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDashboards() {
        return $this->fetchAll(null, "dashboard_name");
    }

    /**
     * @param $userId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findActiveDashboardByUserId($userId) {
        try {
            return $this->fetchRow("dashboard_flag_active = 1 AND dashboard_user_fk = '" . $userId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $aData
     *
     * @return bool|mixed
     */
    public function saveDashboard($aData) {
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
     * @param $dashboardId
     *
     * @return bool|int
     */
    public function updateDashboard($aData, $dashboardId) {
        try {
            return $this->update($aData, "dashboard_id = '" . $dashboardId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $dashboardId
     *
     * @return bool|int
     */
    public function deleteDashboard($dashboardId) {
        try {
            return $this->delete("dashboard_id = '" . $dashboardId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
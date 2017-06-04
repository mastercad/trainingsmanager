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
class Model_DbTable_DashboardXWidget extends Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'dashboard_x_widget';

    /** @var string */
    protected $_primary = 'dashboard_x_widget_id';

    function findByPrimary($id) {
        return $this->fetchRow('dashboard_x_widget_id = ' . intval($id));
    }

    /**
     * @param int $dashboardId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllWidgetsByDashboardId($dashboardId) {
        $select = $this->select(static::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinInner($this->considerTestUserForTableName('dashboards'), 'dashboard_id = dashboard_x_widget_dashboard_fk')
            ->joinInner($this->considerTestUserForTableName('widgets'), 'widget_id = dashboard_x_widget_widget_fk')
            ->where('dashboard_x_widget_dashboard_fk = ?', $dashboardId)
            ->order('dashboard_x_widget_order');

        return $this->fetchAll($select);
    }

    /**
     * @param $aData
     *
     * @return bool|mixed
     */
    public function saveDashboardXWidget($aData) {
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
     * @param $dashboardXWidgetId
     *
     * @return bool|int
     */
    public function updateDashboardXWidget($aData, $dashboardXWidgetId) {
        try {
            return $this->update($aData, "dashboard_x_widget_id = '" . $dashboardXWidgetId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $dashboardXWidgetId
     *
     * @return bool|int
     */
    public function deleteDashboard($dashboardXWidgetId) {
        try {
            return $this->delete("dashboard_x_widget_id = '" . $dashboardXWidgetId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

namespace Model\DbTable;

use Zend_Db_Table_Rowset_Abstract;
use Exception;

/**
 * Class Application_Model_DbTable_DeviceGroups
 */
class DashboardXWidget extends AbstractDbTable
{
    /** @var string */
    protected $_name 	= 'dashboard_x_widget';

    /** @var string */
    protected $_primary = 'dashboard_x_widget_id';

    /**
     * find all widgets by given dashboard
     *
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
     * insert given data in dashboard_x_widget
     *
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
     * update given dashboard with given data
     *
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
     * delete given dashboard from table
     *
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
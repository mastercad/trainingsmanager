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
class Model_DbTable_Widgets extends Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'widgets';

    /** @var string */
    protected $_primary = 'widget_id';

    function findByPrimary($id) {
        return $this->fetchRow('widget_id = ' . intval($id));
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllWidgets() {
        return $this->fetchAll(null, "widget_name");
    }

    /**
     * @param $aData
     *
     * @return bool|mixed
     */
    public function saveWidgets($aData) {
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
     * @param $widgetId
     *
     * @return bool|int
     */
    public function updateWidget($aData, $widgetId) {
        try {
            return $this->update($aData, "widget_id = '" . $widgetId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $widgetId
     *
     * @return bool|int
     */
    public function deleteDashboard($widgetId) {
        try {
            return $this->delete("widget_id = '" . $widgetId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
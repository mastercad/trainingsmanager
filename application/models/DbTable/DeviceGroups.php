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
class Application_Model_DbTable_DeviceGroups extends Application_Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'geraetegruppen';

    /** @var string */
    protected $_primary = 'geraetegruppe_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDeviceGroups() {
        return $this->fetchAll(null, "geraetegruppe_name");
    }

    /**
     * @param $iDeviceGroupId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findDeviceGroup($iDeviceGroupId) {
        try {
            return $this->fetchRow("geraetegruppe_id = '" . $iDeviceGroupId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $sDeviceGroupName
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceGroupByName($sDeviceGroupName) {
        try {
            return $this->fetchAll("geraetegruppe_name LIKE( '" . $sDeviceGroupName . "')", 'geraetegruppe_name');
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
    public function saveDeviceGroup($aData) {
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
     * @param $iDeviceGroupId
     *
     * @return bool|int
     */
    public function updateDeviceGroup($aData, $iDeviceGroupId) {
        try {
            return $this->update($aData, "geraetegruppe_id = '" . $iDeviceGroupId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $iDeviceGroupId
     *
     * @return bool|int
     */
    public function deleteDeviceGroup($iDeviceGroupId) {
        try {
            return $this->delete("geraetegruppe_id = '" . $iDeviceGroupId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Application_Model_DbTable_Devices
 */
class Application_Model_DbTable_Devices extends Application_Model_DbTable_Abstract {

    /** @var string */
    protected $_name 	= 'geraete';

    /** @var string */
    protected $_primary = 'geraet_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDevices() {
        return $this->fetchAll(null, 'geraet_name');
    }

    /**
     * @param $sDeviceName
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceByName($sDeviceName) {
        return $this->fetchAll("geraet_name LIKE('" . $sDeviceName . "')", "geraet_name");
    }

    /**
     * @param $sDeviceName
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceAndDeviceGroupByName($sDeviceName) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->join('geraetegruppe_geraete', 'geraetegruppe_geraet_geraet_fk = geraet_id')
            ->join('geraetegruppen', 'geraetegruppe_id = geraetegruppe_geraet_geraetegruppe_fk')
            ->order(array('geraetegruppe_name', 'geraet_name'))
            ->where("geraet_name LIKE('" . $sDeviceName . "')");
        
        return $this->fetchAll($oSelect);
    }

    /**
     * @param $iDeviceId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findDeviceById($iDeviceId) {
        try {
            return $this->fetchRow("geraet_id = '" . $iDeviceId . "'");
        } catch(Exception $oException) {
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
    public function saveDevice($aData) {
        try {
            return $this->insert($aData);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $aData
     * @param $iDeviceId
     *
     * @return bool|int
     */
    public function updateDevice($aData, $iDeviceId)
    {
        try {
            return $this->update($aData, "geraet_id = '" . $iDeviceId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $iDeviceId
     *
     * @return bool|int
     */
    public function deleteDevice($iDeviceId)
    {
        try {
            return $this->delete("geraet_id = '" . $iDeviceId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}

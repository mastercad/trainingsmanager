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
class Model_DbTable_Devices extends Model_DbTable_Abstract {

    /** @var string */
    protected $_name 	= 'devices';

    /** @var string */
    protected $_primary = 'device_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDevices() {
        return $this->fetchAll(null, 'device_name');
    }

    /**
     * @param $sDeviceName
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceByName($sDeviceName) {
        return $this->fetchAll("device_name LIKE('" . $sDeviceName . "')", "device_name");
    }

    /**
     * @param $sDeviceName
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceAndDeviceGroupByName($sDeviceName) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);

        $oSelect->joinLeft('device_x_device_group', 'device_x_device_group_device_fk = device_id')
            ->joinLeft('device_group', 'device_group_id = device_x_device_group_device_group_fk')
            ->order(array('device_group_name', 'device_name'))
            ->where("device_name LIKE('" . $sDeviceName . "')");
        
        return $this->fetchAll($oSelect);
    }

    /**
     * @param $iDeviceId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findDeviceById($iDeviceId) {
        try {
            $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                ->setIntegrityCheck(false);

            $oSelect->where('device_id = ?', $iDeviceId);

            return $this->fetchRow($oSelect);
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
            return $this->update($aData, "device_id = '" . $iDeviceId . "'");
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
            return $this->delete("device_id = '" . $iDeviceId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}

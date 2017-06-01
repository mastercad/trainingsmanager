<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 21:06
 * To change this template use File | Settings | File Templates.
 */

/**
 * Class Application_Model_DbTable_DeviceGroupDevices
 */
class Model_DbTable_DeviceXDeviceGroup extends Model_DbTable_Abstract
{
    /** @var string */
    protected $_name 	= 'device_x_device_group';

    /** @var string */
    protected $_primary = 'device_x_device_group_id';

    function findByPrimary($id) {
        // TODO: Implement findByPrimary() method.
    }

    /**
     * @param $iDeviceGroupId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDevicesByDeviceGroupId($iDeviceGroupId) {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinInner($this->considerTestUserForTableName('devices'), 'device_id = device_x_device_group_device_fk')
                ->where('device_x_device_group_device_group_fk = ?', $iDeviceGroupId);

            return $this->fetchAll($oSelect);
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
    public function findAllDeviceGroupsWithDevices() {
        $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->joinInner($this->considerTestUserForTableName('devices'), 'device_id = device_x_device_group_device_fk')
                ->joinInner($this->considerTestUserForTableName('device_groups'), 'device_groups_id = device_x_device_group_device_group_fk');

            return $this->fetchAll($oSelect);
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $iDeviceId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceGroupsForDevice($iDeviceId) {
        try {
            return $this->fetchAll("device_x_device_group_device_fk = '" . $iDeviceId . "'");
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
    public function saveDeviceGroupDevice($aData) {
        try {
            return $this->insert($aData);
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $aData
     * @param $iDeviceGroupDeviceId
     *
     * @return bool|int
     */
    public function updateDeviceGroupDevice($aData, $iDeviceGroupDeviceId) {
        try {
            return $this->update($aData, "device_x_device_group_id = '" . $iDeviceGroupDeviceId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $iDeviceGroupDeviceId
     *
     * @return bool|int
     */
    public function deleteDeviceFromDeviceGroupDevices($iDeviceGroupDeviceId) {
        try {
            return $this->delete("device_x_device_group_id = '" . $iDeviceGroupDeviceId . "'");
        } catch( Exception $oException)
        {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param $iDeviceGroupDeviceId
     *
     * @return bool|int
     */
    public function deleteAllDeviceGroupDevicesByDeviceGroupId($iDeviceGroupDeviceId) {
        try {
            return $this->delete("device_x_device_group_device_group_fk = '" . $iDeviceGroupDeviceId . "'");
        } catch(Exception $oException) {
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
    public function deleteAllDeviceGroupDevicesByDeviceId($iDeviceId)
    {
        try {
            return $this->delete("device_x_device_group_device_fk = '" . $iDeviceId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}

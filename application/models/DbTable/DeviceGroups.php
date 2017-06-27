<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

namespace Model\DbTable;

use Nette\NotImplementedException;
use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table_Row_Abstract;
use Exception;

/**
 * Class Application_Model_DbTable_DeviceGroups
 */
class DeviceGroups extends AbstractDbTable
{
    /** @var string */
    protected $_name 	= 'device_groups';

    /** @var string */
    protected $_primary = 'device_group_id';

    /**
     * @inheritdoc
     */
    function findByPrimary($id) {
        throw new NotImplementedException('Function findByPrimary not implemented yet!');
    }

    /**
     * find all device groups
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDeviceGroups() {
        return $this->fetchAll(null, "device_group_name");
    }

    /**
     * find device group by given id
     *
     * @param $iDeviceGroupId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findDeviceGroup($iDeviceGroupId) {
        try {
            return $this->fetchRow("device_group_id = '" . $iDeviceGroupId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * find device group by name
     *
     * @param $sDeviceGroupName
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceGroupByName($sDeviceGroupName) {
        try {
            return $this->fetchAll("device_group_name LIKE( '" . $sDeviceGroupName . "')", 'device_group_name');
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * save given data in database
     *
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
     * update given device group by given id with given data
     *
     * @param $aData
     * @param $iDeviceGroupId
     *
     * @return bool|int
     */
    public function updateDeviceGroup($aData, $iDeviceGroupId) {
        try {
            return $this->update($aData, "device_group_id = '" . $iDeviceGroupId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete given device group
     *
     * @param $iDeviceGroupId
     *
     * @return bool|int
     */
    public function deleteDeviceGroup($iDeviceGroupId) {
        try {
            return $this->delete("device_group_id = '" . $iDeviceGroupId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
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

use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table;
use Exception;

/**
 * Class Application_Model_DbTable_DeviceGroupDevices
 */
class DeviceXDeviceGroup extends AbstractDbTable
{
    /** @var string */
    protected $_name 	= 'device_x_device_group';

    /** @var string */
    protected $_primary = 'device_x_device_group_id';

    /**
     * @param $iDeviceGroupId
     *
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findDevicesByDeviceGroupId($iDeviceGroupId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
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
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
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

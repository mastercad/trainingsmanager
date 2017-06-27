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
use Zend_Db_Table;
use Exception;

/**
 * Class Application_Model_DbTable_Devices
 */
class DeviceXDeviceOption extends AbstractDbTable {

    /** @var string */
    protected $_name 	= 'device_x_device_option';

    /** @var string */
    protected $_primary = 'device_x_device_option_id';

    /**
     * @param $deviceId
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDeviceXDeviceOptionsByDeviceId($deviceId) {

        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        try {
            $oSelect->joinInner($this->considerTestUserForTableName('devices'), 'device_id = ' . $deviceId)
                ->joinInner($this->considerTestUserForTableName('device_options'), 'device_option_id = device_x_device_option_device_option_fk')
                ->where('device_x_device_option_device_fk = ?', $deviceId);

            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";

            return false;
        }
    }

    public function findDeviceOption($deviceOptionId, $deviceId) {

        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
            ->setIntegrityCheck(false);
        try {
            $oSelect->joinInner($this->considerTestUserForTableName('devices'), 'device_id = device_x_device_option_device_fk')
                ->joinInner($this->considerTestUserForTableName('device_options'), 'device_option_id = device_x_device_option_device_option_fk')
                ->where('device_x_device_option_device_option_fk = "?"', $deviceOptionId)
                ->where('device_x_device_option_device_fk = "?"', $deviceId);

            return $this->fetchRow($oSelect);
        } catch (Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";

            return false;
        }
    }

    public function saveDeviceXDeviceOption($data) {
        return $this->insert($data);
    }

    public function updateDeviceXDeviceOption($data, $deviceXDeviceOptionId) {
        return $this->update($data, 'device_x_device_option_id = "' . $deviceXDeviceOptionId . '"');
    }

    public function deleteDeviceXDeviceOption($deviceXDeviceOptionId) {
        return $this->delete('device_x_device_option_id = "' . $deviceXDeviceOptionId . '"');
    }
}

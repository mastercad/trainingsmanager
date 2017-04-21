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
class Model_DbTable_DeviceOptions extends Model_DbTable_Abstract implements Interface_OptionsStorageInterface {

    /** @var string */
    protected $_name 	= 'device_options';

    /** @var string */
    protected $_primary = 'device_option_id';

    /**
     * @inheritdoc
     */
    public function findAllOptions() {
        return $this->fetchAll(null, 'device_option_name');
    }

    /**
     * @inheritdoc
     */
    public function findOptionById($deviceOptionId) {
        return $this->fetchRow('device_option_id = "' . $deviceOptionId . '"');
    }

    public function findDeviceOptionsByDeviceId($deviceId) {
        try {
            $oSelect = $this->select(ZEND_DB_TABLE::SELECT_WITH_FROM_PART)
                ->setIntegrityCheck(false);

            $oSelect->joinInner('device_x_device_option', 'device_x_device_option_device_option_fk = device_option_id AND device_x_device_option_device_fk = ' . $deviceId)
                ->where('device_x_device_option_device_fk = ?', $deviceId);

            return $this->fetchAll($oSelect);
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function updateOption($data, $optionId) {
        return $this->update($data, 'device_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function deleteOption($optionId) {
        return $this->delete('device_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function insertOption($data) {
        return $this->insert($data);
    }
}

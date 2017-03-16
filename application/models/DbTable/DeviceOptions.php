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
class Model_DbTable_DeviceOptions extends Model_DbTable_Abstract {

    /** @var string */
    protected $_name 	= 'device_options';

    /** @var string */
    protected $_primary = 'device_option_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllDeviceOptions() {
        return $this->fetchAll(null, 'device_option_name');
    }

    public function findDeviceOption($deviceOptionId) {
        return $this->fetchRow('device_option_id = "' . $deviceOptionId . '"');
    }
}

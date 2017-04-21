<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

require_once(APPLICATION_PATH . '/controllers/OptionsController.php');

class DeviceOptionsController extends OptionsController {

    protected $map = [
        'option_id' => 'device_option_id',
        'option_name' => 'device_option_name',
        'option_value' => 'device_option_default_value',
    ];

    protected function useOptionsStorage()
    {
        return new Model_DbTable_DeviceOptions();
    }
}

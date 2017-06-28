<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

require_once APPLICATION_PATH . '/controllers/OptionsController.php';

use \Model\DbTable\DeviceOptions;

/**
 * Class DeviceOptionsController
 */
class DeviceOptionsController extends OptionsController
{

    protected $map = [
        'option_id' => 'device_option_id',
        'option_name' => 'device_option_name',
        'option_value' => 'device_option_default_value',
    ];

    /**
     * return device options storage
     *
     * @return \Model\DbTable\DeviceOptions
     */
    protected function useOptionsStorage()
    {
        return new DeviceOptions();
    }
}

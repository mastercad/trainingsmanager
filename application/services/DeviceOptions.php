<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.03.17
 * Time: 22:03
 */

namespace Service;

/**
 * Class DeviceOptions
 *
 * @package Service
 */
class DeviceOptions extends Options
{
    protected $hierarchy = [
        'device_x_device_option',
        'exercise_x_device_option',
        'training_plan_x_exercise_device_option',
    ];
}

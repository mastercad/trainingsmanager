<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.03.17
 * Time: 22:03
 */

namespace Service;

/**
 * Class ExerciseOptions
 *
 * @package Service
 */
class ExerciseOptions extends Options
{
    protected $hierarchy = [
        'exercise_x_exercise_option',
        'training_plan_x_exercise_exercise_option',
    ];
}

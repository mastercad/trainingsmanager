<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

require_once(APPLICATION_PATH . '/controllers/OptionsController.php');

class ExerciseOptionsController extends OptionsController {

    protected $map = [
        'option_id' => 'exercise_option_id',
        'option_name' => 'exercise_option_name',
        'option_value' => 'exercise_option_default_value',
    ];

    protected function useOptionsStorage()
    {
        return new Model_DbTable_ExerciseOptions();
    }
}

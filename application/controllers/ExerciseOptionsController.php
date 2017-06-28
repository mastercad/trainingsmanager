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

use \Model\DbTable\ExerciseOptions;

/**
 * Class ExerciseOptionsController
 */
class ExerciseOptionsController extends OptionsController
{

    protected $map = [
        'option_id' => 'exercise_option_id',
        'option_name' => 'exercise_option_name',
        'option_value' => 'exercise_option_default_value',
    ];

    protected function useOptionsStorage()
    {
        return new ExerciseOptions();
    }
}

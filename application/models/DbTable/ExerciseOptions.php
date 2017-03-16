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
class Model_DbTable_ExerciseOptions extends Model_DbTable_Abstract {

    /** @var string */
    protected $_name 	= 'exercise_options';

    /** @var string */
    protected $_primary = 'exercise_option_id';

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllExerciseOptions() {
        return $this->fetchAll(null, 'exercise_option_name');
    }

    public function findExerciseOption($exerciseOptionId) {
        return $this->fetchRow('exercise_option_id = "' . $exerciseOptionId . '"');
    }
}

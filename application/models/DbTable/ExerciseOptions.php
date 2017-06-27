<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 25.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

namespace Model\DbTable;

use Interfaces\OptionsStorageInterface;
use Nette\NotImplementedException;

/**
 * Class Application_Model_DbTable_Devices
 */
class ExerciseOptions extends AbstractDbTable implements OptionsStorageInterface {

    /** @var string */
    protected $_name 	= 'exercise_options';

    /** @var string */
    protected $_primary = 'exercise_option_id';

    /**
     * @inheritdoc
     */
    function findByPrimary($id) {
        throw new NotImplementedException('Function findByPrimary not implemented yet!');
    }

    /**
     * @inheritdoc
     */
    public function findAllOptions() {
        return $this->fetchAll(null, 'exercise_option_name');
    }

    /**
     * @inheritdoc
     */
    public function findOptionById($exerciseOptionId) {
        return $this->fetchRow('exercise_option_id = "' . $exerciseOptionId . '"');
    }

    /**
     * @inheritdoc
     */
    public function updateOption($data, $optionId) {
        return $this->update($data, 'exercise_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function deleteOption($optionId) {
        return $this->delete('exercise_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function insertOption($data) {
        return $this->insert($data);
    }
}

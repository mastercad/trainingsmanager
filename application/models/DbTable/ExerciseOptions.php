<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Model\DbTable;

use Interfaces\OptionsStorageInterface;

/**
 * Class Application_Model_DbTable_Devices
 */
class ExerciseOptions extends AbstractDbTable implements OptionsStorageInterface
{

    /**
     * @var string
     */
    protected $_name     = 'exercise_options';

    /**
     * @var string
     */
    protected $_primary = 'exercise_option_id';

    /**
     * @inheritdoc
     */
    public function findAllOptions() 
    {
        return $this->fetchAll(null, 'exercise_option_name');
    }

    /**
     * @inheritdoc
     */
    public function findOptionById($exerciseOptionId) 
    {
        return $this->fetchRow('exercise_option_id = "' . $exerciseOptionId . '"');
    }

    /**
     * @inheritdoc
     */
    public function updateOption($data, $optionId) 
    {
        return $this->update($data, 'exercise_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function deleteOption($optionId) 
    {
        return $this->delete('exercise_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function insertOption($data) 
    {
        return $this->insert($data);
    }
}

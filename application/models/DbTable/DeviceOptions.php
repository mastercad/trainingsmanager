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
use Zend_Db_Table;
use Exception;

/**
 * Class Application_Model_DbTable_Devices
 */
class DeviceOptions extends AbstractDbTable implements OptionsStorageInterface
{

    /**
     * @var string 
     */
    protected $_name     = 'device_options';

    /**
     * @var string 
     */
    protected $_primary = 'device_option_id';

    /**
     * @inheritdoc
     */
    public function findAllOptions() 
    {
        return $this->fetchAll(null, 'device_option_name');
    }

    /**
     * @inheritdoc
     */
    public function findOptionById($deviceOptionId) 
    {
        return $this->fetchRow('device_option_id = "' . $deviceOptionId . '"');
    }

    /**
     * find device options by given device id
     *
     * @param $deviceId
     *
     * @return bool|\Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionsByDeviceId($deviceId) 
    {
        try {
            $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

            $oSelect->joinInner(
                $this->considerTestUserForTableName('device_x_device_option'),
                'device_x_device_option_device_option_fk = device_option_id AND device_x_device_option_device_fk = ' . $deviceId
            )
                ->where('device_x_device_option_device_fk = ?', $deviceId);

            return $this->fetchAll($oSelect);
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * finds all device options, bind on exercise by device
     *
     * @param $exerciseId
     *
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findDeviceOptionsByExerciseId($exerciseId) 
    {
        $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);

        $select->joinInner(
            $this->considerTestUserForTableName('exercise_x_device'),
            'exercise_x_device_exercise_fk = ' . $exerciseId
        )
            ->joinInner(
                $this->considerTestUserForTableName('device_x_device_option'),
                'device_x_device_option_device_fk = exercise_x_device_device_fk'
            )
            ->joinLeft(
                $this->considerTestUserForTableName('exercise_x_device_option'),
                'exercise_x_device_option_device_option_fk = device_option_id AND exercise_x_device_option_exercise_fk = ' . $exerciseId
            )
            ->where('device_option_id = device_x_device_option_device_option_fk');

        return $this->fetchAll($select);
    }

    /**
     * @inheritdoc
     */
    public function updateOption($data, $optionId) 
    {
        return $this->update($data, 'device_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function deleteOption($optionId) 
    {
        return $this->delete('device_option_id = ' . $optionId);
    }

    /**
     * @inheritdoc
     */
    public function insertOption($data) 
    {
        return $this->insert($data);
    }
}

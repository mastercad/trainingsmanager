<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:18
 */

namespace Service\Generator\View;

use Service\Generator\View\Options;
use Model\DbTable\DeviceOptions as ModelDbTableDeviceOptions;
use Model\DbTable\TrainingDiaryXDeviceOption;
use Model\DbTable\DeviceXDeviceOption;
use Model\DbTable\TrainingPlanXDeviceOption;
use Model\DbTable\ExerciseXDeviceOption;
use Zend_Db_Table_Row;
use Zend_Registry;




class DeviceOptions extends Options {

    protected $optionType = 'device';

    protected $optionValuePriorities = [
        'training_diary_x_device_option_device_option_value',
        'training_plan_x_device_option_device_option_value',
        'exercise_x_device_option_device_option_value',
        'device_x_device_option_device_option_value'
    ];

    /**
     * @return string
     *
     * @throws \Zend_View_Exception
     */
    public function generate() {
        $deviceOptionsContent = '';
        $this->setOptionClassName('device-option');
        $deviceOptionsCollection = $this->collectOptions();

        /**
         * @var int $deviceOptionId
         * @var array $deviceOption
         */
        foreach ($deviceOptionsCollection as $deviceOptionId => $deviceOption) {
            $trainingPlanDeviceOptionId = (isset($deviceOption['training_plan_x_device_option_id']) ? $deviceOption['training_plan_x_device_option_id'] : null);
            $trainingDiaryDeviceOptionId = (isset($deviceOption['training_diary_x_device_option_id']) ? $deviceOption['training_diary_x_device_option_id'] : null);
            $this->setOptionId($deviceOptionId);
            $this->setDeviceId($deviceOption['device_id']);

            if (array_key_exists('training_plan_x_device_option_device_option_value', $deviceOption)) {
                $this->setBaseOptionValue($deviceOption['training_plan_x_device_option_device_option_value']);
            }
            $this->setSelectedOptionValue($this->extractOptionValue($deviceOption));
            $this->setAdditionalElementAttributes('data-training-plan-device-option-id="' . $trainingPlanDeviceOptionId . '" data-training-diary-device-option-id="' . $trainingDiaryDeviceOptionId . '"');

            /** TODO überprüfen, was hier sinnvoller, weil unique ist! diese ID KANN in device und exercise vorkommen! */
            $this->setInputFieldUniqueId($trainingPlanDeviceOptionId);
            $this->setOptionName($deviceOption['device_option_name']);
            $this->setOptionValue($deviceOption['device_x_device_option_device_option_value']);
            $deviceOptionsContent .= $this->generateOptionInputContent();
        }

        if (0 == strlen(trim($deviceOptionsContent))
            && $this->isForceGenerateEmptyInput()
        ) {
            $deviceOptionDb = new ModelDbTableDeviceOptions();
            $deviceOption = $deviceOptionDb->findOptionById($this->getOptionId());
            $this->setOptionName($deviceOption['device_option_name']);
            $this->setOptionValue($deviceOption['device_option_default_value']);
            $deviceOptionsContent = $this->generateOptionInputContent();
        }
        return $deviceOptionsContent;
    }

    /**
     * @return array
     */
    protected function collectOptions() {
        $trainingDiaryXDeviceOptionDb = new TrainingDiaryXDeviceOption();
        $deviceXDeviceOptionDb = new DeviceXDeviceOption();
        $deviceOptionsDb = new ModelDbTableDeviceOptions();
        $trainingDiaryXDeviceOptionCollection = [];

        if (!empty($this->getTrainingDiaryXTrainingPlanExerciseId())) {
            $trainingDiaryXDeviceOptionCollection = $trainingDiaryXDeviceOptionDb->findDeviceOptionsByTrainingDiaryTrainingPlanExerciseId(
                $this->getTrainingDiaryXTrainingPlanExerciseId());
        }

        $trainingPlanXDeviceOptionDb = new TrainingPlanXDeviceOption();

        $trainingPlanXDeviceOptionCollection = [];

        if (!empty($this->getTrainingPlanXExerciseId())) {
            $trainingPlanXDeviceOptionCollection = $trainingPlanXDeviceOptionDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId(
                $this->getTrainingPlanXExerciseId(), $this->getOptionId());
        }
        $collectedDeviceOptions = [];

        $exerciseXDeviceOptionDb = new ExerciseXDeviceOption();

        if (empty($this->getOptionId())
            && !empty($this->getDeviceId())
        ) {

            $deviceXDeviceOptionCollection = $deviceXDeviceOptionDb->findAllDeviceXDeviceOptionsByDeviceId(
                $this->getDeviceId());

            foreach ($deviceXDeviceOptionCollection as $deviceOption) {
                $collectedDeviceOptions[$deviceOption->offsetGet('device_option_id')] = $deviceOption->toArray();
            }
        } else {
            $deviceXDeviceOptionDb = new DeviceXDeviceOption();
            $deviceXDeviceOption = $deviceXDeviceOptionDb->findDeviceOption($this->getOptionId(), $this->getDeviceId());

            if ($deviceXDeviceOption instanceof Zend_Db_Table_Row) {
                $collectedDeviceOptions[$deviceXDeviceOption->offsetGet('device_option_id')] = $deviceXDeviceOption->toArray();
            }
        }

        if (!empty($this->getExerciseId())
            && empty($this->getOptionId())
        ) {
            $exerciseXDeviceOptionCollection = $exerciseXDeviceOptionDb->findDeviceOptionsForExercise(
                $this->getExerciseId(), $this->getOptionId());

            foreach ($exerciseXDeviceOptionCollection as $deviceOption) {
                $deviceOptionId = $deviceOption->offsetGet('device_option_id');
                if (! array_key_exists($deviceOptionId, $collectedDeviceOptions)) {
                    $collectedDeviceOptions[$deviceOptionId] = $deviceOption->toArray();
                } else {
                    $collectedDeviceOptions[$deviceOptionId] = array_merge($collectedDeviceOptions[$deviceOptionId],
                        $deviceOption->toArray());
                }
            }

            $deviceOptions = $deviceOptionsDb->findDeviceOptionsByExerciseId($this->getExerciseId());

            foreach ($deviceOptions as $deviceOption) {
                $deviceOptionId = $deviceOption->offsetGet('device_option_id');
                if (! array_key_exists($deviceOptionId, $collectedDeviceOptions)) {
                    $collectedDeviceOptions[$deviceOptionId] = $deviceOption->toArray();
                } else {
                    $collectedDeviceOptions[$deviceOptionId] = array_merge($collectedDeviceOptions[$deviceOptionId],
                        $deviceOption->toArray());
                }
            }
        }

        foreach ($trainingPlanXDeviceOptionCollection as $deviceOption) {
            $deviceOptionId = $deviceOption->offsetGet('device_option_id');
            if (! array_key_exists($deviceOptionId, $collectedDeviceOptions)) {
                $collectedDeviceOptions[$deviceOptionId] = $deviceOption->toArray();
            } else {
                $collectedDeviceOptions[$deviceOptionId] = array_merge($collectedDeviceOptions[$deviceOptionId],
                    $deviceOption->toArray());
            }
        }
        foreach ($trainingDiaryXDeviceOptionCollection as $deviceOption) {
            $deviceOptionId = $deviceOption->offsetGet('device_option_id');
            if (! array_key_exists($deviceOptionId, $collectedDeviceOptions)) {
                $collectedDeviceOptions[$deviceOptionId] = $deviceOption->toArray();
            } else {
                $collectedDeviceOptions[$deviceOptionId] = array_merge($collectedDeviceOptions[$deviceOptionId],
                    $deviceOption->toArray());
            }
        }

        if (!empty($this->getOptionId())
            && empty($collectedDeviceOptions)
        ) {
            $deviceOption = $deviceOptionsDb->findOptionById($this->getOptionId());
            $collectedDeviceOptions[$this->getOptionId()] = $deviceOption->toArray();
        }

        return $collectedDeviceOptions;
    }

    /**
     * @return string
     */
    public function generateDeviceOptionsSelectContent() {
        $deviceOptionsContent = '';
        $deviceOptionsDb = new ModelDbTableDeviceOptions();
        $deviceOptionsCollection = $deviceOptionsDb->findAllOptions();
//        $this->getView()->assign('optionDeleteShow', $this->isShowDelete());
        $this->getView()->assign('optionDeleteShow', false);
        $this->getView()->assign('selectId', '');

        foreach ($deviceOptionsCollection as $deviceOption) {
            $this->getView()->assign('optionClassName', 'device-option-select');
            $this->getView()->assign('optionValue', $deviceOption->offsetGet('device_option_id'));
            $this->getView()->assign('optionText', $deviceOption->offsetGet('device_option_name'));
            $this->getView()->assign('optionLabelText', Zend_Registry::get('Zend_Translate')->translate('label_device_options') . ':');
            $this->getView()->assign('optionSelectText', Zend_Registry::get('Zend_Translate')->translate('label_please_select') . '');
            $deviceOptionsContent .= $this->getView()->render('loops/option.phtml');
        }

//        $this->getView()->assign('optionLabelText', Zend_Registry::get('Zend_Translate')->translate('label_please_select'));
        $this->getView()->assign('optionsContent', $deviceOptionsContent);

        return $this->getView()->render('globals/select.phtml');
    }
}
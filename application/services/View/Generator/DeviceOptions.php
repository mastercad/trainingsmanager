<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:18
 */

class Service_View_Generator_DeviceOptions extends Service_View_Generator_Options {

    protected $optionType = 'device';

    protected $optionValuePriorities = [
//        'training_plan_x_device_option_device_option_value',
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

        foreach ($deviceOptionsCollection as $deviceOptionId => $deviceOption) {
            $trainingPlanDeviceOptionId = (isset($deviceOption['training_plan_x_device_option_id']) ? $deviceOption['training_plan_x_device_option_id'] : null);
            $this->setOptionId($deviceOptionId);
            $this->setDeviceId($deviceOption['device_id']);
            $this->setSelectedOptionValue($deviceOption['training_plan_x_device_option_device_option_value']);
            $this->setAdditionalElementAttributes('data-training-plan-device-option-id="' . $trainingPlanDeviceOptionId . '"');
            /** TODO überprüfen, was hier sinnvoller, weil unique ist! diese ID KANN in device und exercise vorkommen! */
            $this->setInputFieldUniqueId($trainingPlanDeviceOptionId);
            $this->setOptionName($deviceOption['device_option_name']);
            $this->setOptionValue($this->extractOptionValue($deviceOption));
            $deviceOptionsContent .= $this->generateOptionInputContent();
        }

        if (0 == strlen(trim($deviceOptionsContent))
            && $this->isForceGenerateEmptyInput()
        ) {
            $deviceOptionDb = new Model_DbTable_DeviceOptions();
            $deviceOption = $deviceOptionDb->findDeviceOption($this->getOptionId());
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
        $trainingPlanXDeviceOptionDb = new Model_DbTable_TrainingPlanXDeviceOption();

        $trainingPlanXDeviceOptionCollection = [];

        if (!empty($this->getTrainingPlanXExerciseId())) {
            $trainingPlanXDeviceOptionCollection = $trainingPlanXDeviceOptionDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId(
                $this->getTrainingPlanXExerciseId(), $this->getOptionId());
        }
        $collectedDeviceOptions = [];

        if ($this->isForceGenerateEmptyInput()) {
            $exerciseXDeviceOptionDb = new Model_DbTable_ExerciseXDeviceOption();

            if (empty($this->getOptionId())) {
                $deviceXDeviceOptionDb = new Model_DbTable_DeviceXDeviceOption();

                $deviceXDeviceOptionCollection = $deviceXDeviceOptionDb->findAllDeviceXDeviceOptionsByDeviceId(
                    $this->getDeviceId());

                foreach ($deviceXDeviceOptionCollection as $deviceOption) {
                    $collectedDeviceOptions[$deviceOption->offsetGet('device_option_id')] = $deviceOption->toArray();
                }
            }

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

        return $collectedDeviceOptions;
    }

    /**
     * @return string
     */
    public function generateDeviceOptionsSelectContent() {
        $deviceOptionsContent = '';
        $deviceOptionsDb = new Model_DbTable_DeviceOptions();
        $deviceOptionsCollection = $deviceOptionsDb->findAllDeviceOptions();
        $this->getView()->assign('optionDeleteShow', $this->isShowDelete());

        foreach ($deviceOptionsCollection as $deviceOption) {
            $this->getView()->assign('optionClassName', 'device-option-select');
            $this->getView()->assign('optionValue', $deviceOption->offsetGet('device_option_id'));
            $this->getView()->assign('optionText', $deviceOption->offsetGet('device_option_name'));
            $deviceOptionsContent .= $this->getView()->render('loops/option.phtml');
        }

        $this->getView()->assign('optionsContent', $deviceOptionsContent);

        return $this->getView()->render('globals/select.phtml');
    }
}
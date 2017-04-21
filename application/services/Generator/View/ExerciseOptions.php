<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:19
 */

class Service_Generator_View_ExerciseOptions extends Service_Generator_View_Options {

    protected $optionType = 'exercise';

    protected $optionValuePriorities = [
//        'training_plan_x_exercise_option_exercise_option_value',
        'exercise_x_exercise_option_exercise_option_value',
    ];

    /**
     * @return string
     */
    public function generate() {
        $exerciseOptionsContent = '';
        $this->setOptionClassName('exercise-option');
        $exerciseOptionCollection = $this->collectOptions();

        foreach ($exerciseOptionCollection as $exerciseOptionId => $exerciseOption) {
            $trainingPlanExerciseId = isset($exerciseOption['training_plan_x_exercise_option_id']) ? $exerciseOption['training_plan_x_exercise_option_id'] : null;
            $trainingDiaryExerciseOptionId = isset($exerciseOption['training_diary_x_exercise_option_id']) ? $exerciseOption['training_diary_x_exercise_option_id'] : null;

            $this->setOptionId($exerciseOptionId);
            $this->setBaseOptionValue($exerciseOption['training_plan_x_exercise_option_exercise_option_value']);
            $this->setSelectedOptionValue($exerciseOption['training_diary_x_exercise_option_exercise_option_value'] ? $exerciseOption['training_diary_x_exercise_option_exercise_option_value'] : $exerciseOption['training_plan_x_exercise_option_exercise_option_value']);
            $this->setInputFieldUniqueId($trainingPlanExerciseId);
            $this->extractOptionValue($exerciseOption);
            $this->setOptionName($exerciseOption['exercise_option_name']);
            $this->setOptionId($exerciseOption['exercise_option_id']);
            $this->setOptionValue($this->extractOptionValue($exerciseOption));
            $this->setAdditionalElementAttributes('data-training-plan-exercise-option-id="' . $trainingPlanExerciseId . '" data-training-diary-exercise-option-id="' . $trainingDiaryExerciseOptionId . '"');

            $exerciseOptionsContent .= $this->generateOptionInputContent();
        }

        if (0 == strlen(trim($exerciseOptionsContent))
            && $this->isForceGenerateEmptyInput()
        ) {
            $exerciseOptionDb = new Model_DbTable_ExerciseOptions();
            $exerciseOption = $exerciseOptionDb->findOptionById($this->getOptionId());
            $this->setOptionName($exerciseOption['exercise_option_name']);
            $this->setOptionValue($exerciseOption['exercise_option_default_value']);
            $exerciseOptionsContent = $this->generateOptionInputContent();
        }
        return $exerciseOptionsContent;
    }

    /**
     * @return array
     */
    protected function collectOptions() {
        $trainingPlanXExerciseOptionDb = new Model_DbTable_TrainingPlanXExerciseOption();
        $trainingPlanXExerciseOptionCollection = [];

        if (! empty($this->getTrainingPlanXExerciseId())) {
            $trainingPlanXExerciseOptionCollection = $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($this->getTrainingPlanXExerciseId(), $this->getOptionId());
        }
        $collectedExerciseOptions = [];

        $exerciseXExerciseOptionDb = new Model_DbTable_ExerciseXExerciseOption();
        $exerciseXExerciseOptionCollection = $exerciseXExerciseOptionDb->findExerciseOptionsForExercise($this->getExerciseId());

        foreach ($exerciseXExerciseOptionCollection as $exerciseOption) {
            $exerciseOptionId = $exerciseOption->offsetGet('exercise_option_id');
            if (! array_key_exists($exerciseOptionId, $collectedExerciseOptions)) {
                $collectedExerciseOptions[$exerciseOptionId] = $exerciseOption->toArray();
            } else {
                $collectedExerciseOptions[$exerciseOptionId] = array_merge($collectedExerciseOptions[$exerciseOptionId],
                    $exerciseOption->toArray());
            }
        }

        foreach ($trainingPlanXExerciseOptionCollection as $exerciseOption) {
            $exerciseOptionId = $exerciseOption->offsetGet('exercise_option_id');
            if (! array_key_exists($exerciseOptionId, $collectedExerciseOptions)) {
                $collectedExerciseOptions[$exerciseOptionId] = $exerciseOption->toArray();
            } else {
                $collectedExerciseOptions[$exerciseOptionId] = array_merge($collectedExerciseOptions[$exerciseOptionId],
                    $exerciseOption->toArray());
            }
        }
        return $collectedExerciseOptions;
    }

    /**
     * @return string
     */
    public function generateExerciseOptionsSelectContent() {
        $exerciseOptionsContent = '';
        $exerciseOptionsDb = new Model_DbTable_ExerciseOptions();
        $exerciseOptionsCollection = $exerciseOptionsDb->findAllOptions();
//        $this->getView()->assign('optionDeleteShow', $this->isShowDelete());
        $this->getView()->assign('optionDeleteShow', false);

        foreach ($exerciseOptionsCollection as $exerciseOption) {
            $this->getView()->assign('optionClassName', 'exercise-option-select');
            $this->getView()->assign('optionLabelText', Zend_Registry::get('Zend_Translate')->translate('label_exercise_options') . ':');
            $this->getView()->assign('optionSelectText', Zend_Registry::get('Zend_Translate')->translate('label_please_select') . '');
            $this->getView()->assign('optionValue', $exerciseOption->offsetGet('exercise_option_id'));
            $this->getView()->assign('optionText', $exerciseOption->offsetGet('exercise_option_name'));
            $exerciseOptionsContent .= $this->getView()->render('loops/option.phtml');
        }

        $this->getView()->assign('optionsContent', $exerciseOptionsContent);

        return $this->getView()->render('globals/select.phtml');
    }

}
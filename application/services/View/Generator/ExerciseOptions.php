<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:19
 */

class Service_View_Generator_ExerciseOptions extends Service_View_Generator_Options {

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

            $this->setOptionId($exerciseOptionId);
            $this->setSelectedOptionValue($exerciseOption['training_plan_x_exercise_option_exercise_option_value']);
            $this->setInputFieldUniqueId($trainingPlanExerciseId);
            $this->extractOptionValue($exerciseOption);
            $this->setOptionName($exerciseOption['exercise_option_name']);
            $this->setOptionId($exerciseOption['exercise_option_id']);
            $this->setOptionValue($this->extractOptionValue($exerciseOption));
            $this->setOptionClassName('exercise-option');
            $this->setAdditionalElementAttributes('data-training-plan-exercise-option-id="' . $trainingPlanExerciseId . '"');

            $exerciseOptionsContent .= $this->generateOptionInputContent();
        }

        if (0 == strlen(trim($exerciseOptionsContent))
            && $this->isForceGenerateEmptyInput()
        ) {
            $exerciseOptionDb = new Model_DbTable_ExerciseOptions();
            $exerciseOption = $exerciseOptionDb->findExerciseOption($this->getOptionId());
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
            $trainingPlanXExerciseOptionCollection = $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($this->getTrainingPlanXExerciseId());
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
        $exerciseOptionsCollection = $exerciseOptionsDb->findAllExerciseOptions();
        $this->getView()->assign('optionDeleteShow', $this->isShowDelete());

        foreach ($exerciseOptionsCollection as $exerciseOption) {
            $this->getView()->assign('optionClassName', 'exercise-option-select');
            $this->getView()->assign('optionValue', $exerciseOption->offsetGet('exercise_option_id'));
            $this->getView()->assign('optionText', $exerciseOption->offsetGet('exercise_option_name'));
            $exerciseOptionsContent .= $this->getView()->render('loops/option.phtml');
        }

        $this->getView()->assign('optionsContent', $exerciseOptionsContent);

        return $this->getView()->render('globals/select.phtml');
    }

}
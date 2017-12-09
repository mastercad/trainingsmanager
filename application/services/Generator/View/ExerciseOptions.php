<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:19
 */

namespace Service\Generator\View;

use Model\DbTable\ExerciseOptions as ModelDbTableExerciseOptions;
use Model\DbTable\TrainingPlanXExerciseOption;
use Model\DbTable\ExerciseXExerciseOption;
use Zend_Registry;

/**
 * Class ExerciseOptions
 *
 * @package Service\Generator\View
 */
class ExerciseOptions extends Options
{
    private $options = [];

    /**
     * @var string
     */
    protected $optionType = 'exercise';

    /**
     * @var array
     */
    protected $optionValuePriorities = [
        'training_diary_x_exercise_option_exercise_option_value',
        'training_plan_x_exercise_option_exercise_option_value',
        'exercise_x_exercise_option_exercise_option_value',
        'exercise_option_default_value',
    ];

    /**
     * @return string
     */
    public function generate()
    {
        $exerciseOptionsContent = '';
        $this->setOptionClassName('exercise-option');
        $exerciseOptionCollection = $this->collectOptions();

        $this->options = [
            'isIntervalExercise' => false,
            'restingPhase' => null,
            'trainingPhase' => null,
            'exerciseUp' => null,
            'exerciseHold' => null,
            'exerciseDown' => null,
            'trainingRates' => null,
            'repeats' => null,
            'restingInterval' => null,
            'trainingInterval' => null,
        ];

        foreach ($exerciseOptionCollection as $exerciseOptionId => $exerciseOption) {
            if ('Phasen' != $exerciseOption['exercise_option_name']) {
                $trainingPlanExerciseId = isset($exerciseOption['training_plan_x_exercise_option_id']) ?
                    $exerciseOption['training_plan_x_exercise_option_id'] :
                    null;

                $trainingDiaryExerciseOptionId = isset($exerciseOption['training_diary_x_exercise_option_id']) ?
                    $exerciseOption['training_diary_x_exercise_option_id'] :
                    null;

                $this->setOptionId($exerciseOptionId);
                $this->setBaseOptionValue($exerciseOption['training_plan_x_exercise_option_exercise_option_value']);
                $this->setSelectedOptionValue($this->extractOptionValue($exerciseOption));
                $this->setInputFieldUniqueId($trainingPlanExerciseId);
                $this->extractOptionValue($exerciseOption);
                $this->setOptionName($exerciseOption['exercise_option_name']);
                $this->setOptionId($exerciseOption['exercise_option_id']);
                $this->setOptionValue($this->extractOptionValue($exerciseOption));
                $this->setAdditionalElementAttributes(
                    'data-training-plan-exercise-option-id="' . $trainingPlanExerciseId .
                    '" data-training-diary-exercise-option-id="' . $trainingDiaryExerciseOptionId . '"'
                );

                $exerciseOptionsContent .= $this->generateOptionInputContent();
                $this->persistCurrentOptionValue();
            } else {
                $this->generatePhaseContent($exerciseOption);
            }
        }

        if (0 == strlen(trim($exerciseOptionsContent))
            && $this->isForceGenerateEmptyInput()
        ) {
            $exerciseOptionDb = new ModelDbTableExerciseOptions();
            $exerciseOption = $exerciseOptionDb->findOptionById($this->getOptionId());
            $this->setOptionName($exerciseOption['exercise_option_name']);
            $this->setOptionValue($exerciseOption['exercise_option_default_value']);
            $exerciseOptionsContent = $this->generateOptionInputContent();
        }
        $this->setDefaultRateRestTime();

        return $exerciseOptionsContent . $this->generateExerciseOptionsJson();
    }

    private function setDefaultRateRestTime()
    {
        if (empty($this->options['rateRestingPhase'])) {
            $repeats = $this->options['repeats'];

            if (12 <= $repeats) {
                $this->options['rateRestingPhase'] = 60;
            } else if (6 <= $repeats
                && 12 > $repeats
            ) {
                $this->options['rateRestingPhase'] = 120;
            } else if (6 > $repeats) {
                $this->options['rateRestingPhase'] = 180;
            }
        }
        return $this;
    }

    private function persistCurrentOptionValue()
    {
        switch (strtoupper($this->getOptionName())) {
            case 'DAUER':
                $this->options['trainingPhase'] = $this->getOptionValue();
                break;
            case 'SäTZE':
                $this->options['trainingRates'] = $this->getOptionValue();
                break;
            case 'WIEDERHOLUNGEN':
                $this->options['repeats'] = $this->getOptionValue();
                break;
            case 'PAUSE':
                $this->options['restingPhase'] = $this->getOptionValue();
                break;
            case 'SATZPAUSE':
            case 'SATZ_PAUSE':
            case 'SATZ PAUSE':
                $this->options['rateRestingPhase'] = $this->getOptionValue();
                break;
        }
        return $this;
    }

    /**
     *
     */
    private function generatePhaseContent($exerciseOption) {
        $optionValue = $this->extractOptionValue($exerciseOption);
        if (false !== strpos($optionValue, '|')) {
            $values = explode('|', $optionValue);

            if (2 == count($values)) {
                $this->options['exerciseUp'] = $values[0];
                $this->options['exerciseDown'] = $values[1];
            } else if (3 <= count($values)) {
                $this->options['exerciseUp'] = $values[0];
                $this->options['exerciseHold'] = $values[1];
                $this->options['exerciseDown'] = $values[2];
            }
        } else {
            $this->options['trainingPhase'] = $optionValue;
        }
        return $this;
    }

    private function generateExerciseOptionsJson()
    {
        $json = json_encode($this->options);
        return '<input type="hidden" class="exercise-option-json" value="'.base64_encode($json).'" />';
    }

    /**
     * @return array
     */
    protected function collectOptions()
    {
        $trainingPlanXExerciseOptionDb = new TrainingPlanXExerciseOption();
        $trainingPlanXExerciseOptionCollection = [];
        $trainingPlanXExerciseId = $this->getTrainingPlanXExerciseId();
        if (! empty($trainingPlanXExerciseId)) {
            $trainingPlanXExerciseOptionCollection =
                $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId(
                    $this->getTrainingPlanXExerciseId(),
                    $this->getOptionId()
                );
        }
        $collectedExerciseOptions = [];

        $exerciseXExerciseOptionDb = new ExerciseXExerciseOption();
        $exerciseXExerciseOptionCollection =
            $exerciseXExerciseOptionDb->findExerciseOptionsForExercise($this->getExerciseId());

        foreach ($exerciseXExerciseOptionCollection as $exerciseOption) {
            $exerciseOptionId = $exerciseOption->offsetGet('exercise_option_id');
            if (! array_key_exists($exerciseOptionId, $collectedExerciseOptions)) {
                $collectedExerciseOptions[$exerciseOptionId] = $exerciseOption->toArray();
            } else {
                $collectedExerciseOptions[$exerciseOptionId] = array_merge(
                    $collectedExerciseOptions[$exerciseOptionId],
                    $exerciseOption->toArray()
                );
            }
        }

        foreach ($trainingPlanXExerciseOptionCollection as $exerciseOption) {
            $exerciseOptionId = $exerciseOption->offsetGet('exercise_option_id');
            if (! array_key_exists($exerciseOptionId, $collectedExerciseOptions)) {
                $collectedExerciseOptions[$exerciseOptionId] = $exerciseOption->toArray();
            } else {
                $collectedExerciseOptions[$exerciseOptionId] = array_merge(
                    $collectedExerciseOptions[$exerciseOptionId],
                    $exerciseOption->toArray()
                );
            }
        }
        return $collectedExerciseOptions;
    }

    /**
     * @return string
     */
    public function generateExerciseOptionsSelectContent()
    {
        $exerciseOptionsContent = '';
        $exerciseOptionsDb = new ModelDbTableExerciseOptions();
        $exerciseOptionsCollection = $exerciseOptionsDb->findAllOptions();
        $this->getView()->assign('optionDeleteShow', false);

        foreach ($exerciseOptionsCollection as $exerciseOption) {
            $this->getView()->assign('optionClassName', 'exercise-option-select');
            $this->getView()->assign(
                'optionLabelText',
                Zend_Registry::get('Zend_Translate')->translate('label_exercise_options') . ':'
            );
            $this->getView()->assign(
                'optionSelectText',
                Zend_Registry::get('Zend_Translate')->translate('label_please_select') . ''
            );
            $this->getView()->assign('optionValue', $exerciseOption->offsetGet('exercise_option_id'));
            $this->getView()->assign('optionText', $exerciseOption->offsetGet('exercise_option_name'));
            $exerciseOptionsContent .= $this->getView()->render('loops/option.phtml');
        }

        $this->getView()->assign('optionsContent', $exerciseOptionsContent);

        return $this->getView()->render('globals/select.phtml');
    }
}

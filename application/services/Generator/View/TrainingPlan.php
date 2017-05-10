<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 24.03.17
 * Time: 19:30
 */

class Service_Generator_View_TrainingPlan extends Service_Generator_View_GeneratorAbstract {

    private $usedMuscles = [];
    private $muscleUsageMin = 0;
    private $muscleUsageMax = 0;
    private $trainingPlanTabHeaderContent = '';

    /**
     * @param int $trainingPlanId
     * @param int $trainingPlanLayoutId
     *
     * @return string
     */
    public function generateTrainingPlanContent($trainingPlanId, $trainingPlanLayoutId) {
        if (2 == $trainingPlanLayoutId) {
            return $this->generateSplitTrainingPlanContent($trainingPlanId);
        } else {
            return $this->generateNormalTrainingPlanContent($trainingPlanId);
        }
    }

    /**
     * @param $trainingPlanId
     *
     * @return string
     */
    private function generateSplitTrainingPlanContent($trainingPlanId) {
        $trainingPlanContent = '';
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $childrenTrainingPlanCollection = $trainingPlansDb->findChildTrainingPlans($trainingPlanId);

        foreach ($childrenTrainingPlanCollection as $childTrainingPlan) {
            $trainingPlanContent .= $this->generateNormalTrainingPlanContent($childTrainingPlan->offsetGet('training_plan_id'));
        }

        return $trainingPlanContent;
    }

    /*
     *
     * @return string
     */
    private function generateNormalTrainingPlanContent($trainingPlanId) {
        $this->usedMuscles = [];
        $this->muscleUsageMin = 0;
        $this->muscleUsageMax = 0;

        $trainingXTrainingPlanExerciseDb = new Model_DbTable_TrainingPlanXExercise();
        $exercisesInTrainingPlanCollection = $trainingXTrainingPlanExerciseDb->findExercisesByTrainingPlanId($trainingPlanId);
        $exercisesContent = '';
        foreach ($exercisesInTrainingPlanCollection as $exercise) {
            $exercisesContent .= $this->generateExerciseForTrainingPlanContent($exercise);
        }
        $this->getView()->assign('trainingPlanId', $trainingPlanId);
        $this->getView()->assign('musclesForExerciseContent', $this->generateUsedMusclesForTrainingPlanContent());
        $this->getView()->assign('exercisesContent', $exercisesContent);

        return $this->getView()->render('loops/training-plan.phtml');
    }

    /**
     * @param $trainingPlanExercise
     *
     * @return string
     */
    private function generateExerciseForTrainingPlanContent($trainingPlanExercise) {

        $trainingPlanXDeviceOptionDb = new Model_DbTable_TrainingPlanXDeviceOption();
        $deviceOptionCollection = $trainingPlanXDeviceOptionDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId($trainingPlanExercise->training_plan_x_exercise_id);
        $deviceOptionsContent = '';

        $optionLabelText = $this->translate('label_please_select');
        foreach ($deviceOptionCollection as $deviceOption) {
            $deviceOptionsContent .= $deviceOption->offsetGet('device_option_name') . ' : ' . $deviceOption->offsetGet('training_plan_x_device_option_device_option_value') . '<br />';
        }

        $trainingPlanXExerciseOptionDb = new Model_DbTable_TrainingPlanXExerciseOption();
        $exerciseOptionCollection = $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($trainingPlanExercise->training_plan_x_exercise_id);
        $exerciseOptionsContent = '';

        foreach ($exerciseOptionCollection as $exerciseOption) {
            $exerciseOptionsContent .= $exerciseOption->offsetGet('exercise_option_name') . ' : ' . $exerciseOption->offsetGet('training_plan_x_exercise_option_exercise_option_value') . '<br />';
        }
        $this->getView()->assign('optionLabelText', $optionLabelText);
        $this->getView()->assign('deviceOptionsContent', $deviceOptionsContent);
        $this->getView()->assign('exerciseOptionsContent', $exerciseOptionsContent);
        $this->collectMusclesForExerciseContent($trainingPlanExercise);
        $this->getView()->assign($trainingPlanExercise->toArray());

        return $this->getView()->render('loops/training-plan-exercise.phtml');
    }

    /**
     * @param $trainingPlanExercise
     *
     * @return $this
     */
    private function collectMusclesForExerciseContent($trainingPlanExercise) {
        $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
        $usedMusclesInExercise = $exerciseXMuscleDb->findMusclesForExercise($trainingPlanExercise->training_plan_x_exercise_exercise_fk);

        foreach ($usedMusclesInExercise as $usedMuscleInExercise) {
            if (false == array_key_exists($usedMuscleInExercise->muscle_name, $this->usedMuscles)) {
                $this->usedMuscles[$usedMuscleInExercise->muscle_name] = 0;
            }
            $this->usedMuscles[$usedMuscleInExercise->muscle_name] += $usedMuscleInExercise->exercise_x_muscle_muscle_use;
            if (null == $this->muscleUsageMin
                || $this->muscleUsageMin > $this->usedMuscles[$usedMuscleInExercise->muscle_name]
            ) {
                $this->muscleUsageMin = $this->usedMuscles[$usedMuscleInExercise->muscle_name];
            }
            if (null == $this->muscleUsageMax
                || $this->muscleUsageMax < $this->usedMuscles[$usedMuscleInExercise->muscle_name]
            ) {
                $this->muscleUsageMax = $this->usedMuscles[$usedMuscleInExercise->muscle_name];
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    private function generateUsedMusclesForTrainingPlanContent() {
        $factor = $this->muscleUsageMax / 5; // 5 sind die maximale anzahl der sterne
        $muscleUseContent = '';

        foreach ($this->usedMuscles as $muscleName => $muscleUse) {
            $usagePosX = -100;
            if ($muscleUse > 0) {
                $realMuscleUsage = $muscleUse / $factor;
                $usagePosX = -100 + ($realMuscleUsage * 20);
            }
            $this->getView()->assign('muscleName', $muscleName);
            $this->getView()->assign('usagePosX', $usagePosX);
            $this->getView()->assign('muscleUse', $muscleUse);
            $this->getView()->assign('muscleUsePercentage', number_format(($muscleUse / $this->muscleUsageMax) * 100, 2));
            $muscleUseContent .= $this->getView()->render('loops/muscles-for-exercise.phtml');
        }

        return $muscleUseContent;
    }

    public function generateTrainingPlanForEditContent($trainingPlanId) {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingPlanCollection = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($trainingPlanId);
        $trainingPlanContent = '';
        $this->trainingPlanTabHeaderContent = '';
        $count = 1;

        foreach ($trainingPlanCollection as $trainingPlan) {
            // es ist nur ein trainingsplan, oder es gibt mehrere, dann den parent nicht rendern
            if (($trainingPlan->offsetGet('training_plan_parent_fk')
                    && 0 < count($trainingPlanCollection))
                || (! $trainingPlan->offsetGet('training_plan_parent_fk')
                    && 1 == count($trainingPlanCollection))
            ) {
                if (1 === $count) {
                    $this->getView()->assign('classActive', 'active');
                } else {
                    $this->getView()->assign('classActive', '');
                }
                $this->trainingPlanTabHeaderContent .= $this->generateSplitTrainingPlanHeaderRow($trainingPlan, $count);
                $this->getView()->assign('exercisesContent',
                    $this->generateTrainingPlanExerciseForEditContent($trainingPlan->offsetGet('training_plan_id')));
                $this->getView()->assign('trainingPlanId', $trainingPlan->offsetGet('training_plan_id'));
                $this->getView()->assign('trainingPlanName', $trainingPlan->offsetGet('training_plan_name'));
                $trainingPlanContent .= $this->getView()->render('loops/training-plan-edit.phtml');
                ++$count;
            }
        }

        return $trainingPlanContent;
    }

    private function generateSplitTrainingPlanHeaderRow(Zend_Db_Table_Row_Abstract $trainingPlan, $count) {
        $name = trim($trainingPlan->offsetGet('training_plan_name'));
        if (0 == strlen($name)) {
            $name = $this->translate('label_day') . '' . $count;
        }
        $this->getView()->assign('name', $name);
        return $this->getView()->render('loops/training-plan-split-header-row.phtml');
    }

    private function generateTrainingPlanExerciseForEditContent($trainingPlanId) {
        $trainingPlanXExercisesDb = new Model_DbTable_TrainingPlanXExercise();

        $trainingPlanXExerciseCollection =
            $trainingPlanXExercisesDb->findExercisesByTrainingPlanId($trainingPlanId);

        $exercisesContent = '';

        foreach ($trainingPlanXExerciseCollection as $trainingPlanExercise) {
            $this->getView()->assign($trainingPlanExercise->toArray());

            $exerciseId = $trainingPlanExercise->offsetGet('exercise_id');
            $trainingPlanExerciseId = $trainingPlanExercise->offsetGet('training_plan_x_exercise_id');

            $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->getView());
            $exerciseOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
            $exerciseOptionsService->setExerciseId($exerciseId);
            $exerciseOptionsService->setAllowEdit(true);
            $exerciseOptionsService->setShowDelete(true);

            $this->getView()->assign('exerciseOptionsContent', $exerciseOptionsService->generate());

            $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->getView());
            $deviceOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
            $deviceOptionsService->setExerciseId($exerciseId);
            $deviceOptionsService->setAllowEdit(true);
            $deviceOptionsService->setShowDelete(true);

            $this->getView()->assign('deviceOptionsContent', $deviceOptionsService->generate());

            $exercisesContent .= $this->getView()->render('loops/training-plan-exercise-edit.phtml');
        }

        return $exercisesContent;
    }

    /**
     * @return string
     */
    public function getTrainingPlanTabHeaderContent() {
        return $this->trainingPlanTabHeaderContent;
    }

    /**
     * @param string $trainingPlanTabHeaderContent
     */
    public function setTrainingPlanTabHeaderContent($trainingPlanTabHeaderContent) {
        $this->trainingPlanTabHeaderContent = $trainingPlanTabHeaderContent;
    }


}
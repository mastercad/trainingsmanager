<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 24.03.17
 * Time: 19:30
 */

namespace Service\Generator\View;

use Zend_Db_Table_Rowset_Abstract;
use Zend_Db_Table_Row_Abstract;
use Model\DbTable\TrainingPlanXExercise;
use Service\Generator\Thumbnail;
use Model\DbTable\ExerciseXMuscle;
use Model\DbTable\TrainingPlans as ModelDbTableTrainingPlans;
use Auth\Model\Resource\TrainingPlans as AuthModelResourceTrainingPlans;
use Auth\Model\Role\Member;
use Zend_Registry;

/**
 * Class TrainingPlan
 *
 * @package Service\Generator\View
 */
class TrainingPlan extends GeneratorAbstract
{

    private $usedMuscles = [];
    private $muscleUsageMin = 0;
    private $muscleUsageMax = 0;
    private $trainingPlanTabHeaderContent = '';

    /**
     * @param \Zend_Db_Table_Rowset
     *
     * @return string
     */
    public function generateTrainingPlanContent(Zend_Db_Table_Rowset_Abstract $trainingPlan)
    {
        $content = '';
        $trainingPlanId = null;

        // split plan
        if (1 < count($trainingPlan)) {
            $content = $this->generateSplitTrainingPlanContent($trainingPlan);
            $trainingPlanId = $trainingPlan->offsetGet(0)->offsetGet('training_plan_id');
        } elseif (1 === count($trainingPlan)) {
            $trainingPlan = $trainingPlan->current();
            if ($trainingPlan instanceof Zend_Db_Table_Row_Abstract) {
                $this->getView()->assign('currentTrainingPlan', $this->generateCurrentTrainingPlanItem($trainingPlan));
                $content = $this->generateSingleTrainingPlanContent($trainingPlan);
                $trainingPlanId = $trainingPlan->offsetGet('training_plan_id');
            }
        }
        $this->getView()->assign('trainingPlanOptionsContent', $this->generateDetailOptionsContent($trainingPlanId));

        return $content;
    }

    private function generateSplitTrainingPlanContent(Zend_Db_Table_Rowset_Abstract $trainingPlanCollection)
    {
        $content = '';
        $headerContent = '';
        $active = 'active';
        $count = 1;
        foreach ($trainingPlanCollection as $trainingPlan) {
            // parent außer acht lassen
            if (0 < $trainingPlan->offsetGet('training_plan_parent_fk')) {
                $this->getView()->assign('classActive', $active);
                $headerContent .= $this->generateSplitTrainingPlanHeaderRow($trainingPlan, $count);
                $content .= $this->generateSingleTrainingPlanContent($trainingPlan);
                ++$count;
                $active = '';
            } else {
                $this->getView()->assign('currentTrainingPlan', $this->generateCurrentTrainingPlanItem($trainingPlan));
            }
        }
        $this->getView()->assign('trainingPlansHeaderContent', $headerContent);
        $this->getView()->assign('trainingPlansExercisesContent', $content);
        return $this->getView()->render('loops/training-plan-split-container.phtml');
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $trainingPlan
     *
     * @return string
     */
    private function generateCurrentTrainingPlanItem(Zend_Db_Table_Row_Abstract $trainingPlan)
    {
        $trainingPlanName = $this->translate('label_training_plan');
        if ($trainingPlan->offsetGet('training_plan_name')) {
            $trainingPlanName = $trainingPlan->offsetGet('training_plan_name');
        }
        $trainingPlanName .=  ' ' . $this->translate('of') . ' : ';
        $content = '<span class="item selected" data-id="'.$trainingPlan->offsetGet('training_plan_id').'">' .
            $trainingPlanName . ' ' . date('Y-m-d', strtotime($trainingPlan->offsetGet('training_plan_create_date'))) .
            '</a>';

        return $content;
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $trainingPlan
     *
     * @return string
     * @throws \Zend_View_Exception
     */
    private function generateSingleTrainingPlanContent(Zend_Db_Table_Row_Abstract $trainingPlan) 
    {
        $content = '';
        $this->usedMuscles = [];
        $this->muscleUsageMin = 0;
        $this->muscleUsageMax = 0;

        if (0 == $trainingPlan->offsetGet('training_plan_active')) {
            $this->getView()->assign('archiveTrainingPlanLoaded', true);
        }
        $trainingPlanXExerciseDb = new TrainingPlanXExercise();
        $exercises = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId(
            $trainingPlan->offsetGet('training_plan_id')
        );

        foreach ($exercises as $exercise) {
            $content .= $this->generateExerciseContent($exercise);
        }

        //        $this->getView()->assign('musclesForExerciseContent', $this->generateUsedMusclesForExerciseContent());
        $this->getView()->assign('exercisesContent', $content);

        $this->getView()->assign(
            'startTrainingPlanLink',
            '/training-diaries/start/id/' . $trainingPlan->offsetGet('training_plan_id')
        );
        $this->getView()->assign('trainingPlanActive', $trainingPlan->offsetGet('training_plan_active'));
        $this->getView()->assign('musclesForTrainingPlanContent', $this->generateUsedMusclesForTrainingPlanContent());
        return $this->getView()->render('loops/training-plan-split-exercise-row.phtml');
    }

    /**
     * @param Zend_Db_Table_Row_Abstract $exercise
     *
     * @return string
     * @throws \Zend_View_Exception
     */
    private function generateExerciseContent(Zend_Db_Table_Row_Abstract $exercise) 
    {
        $this->getView()->assign($exercise->toArray());
        $this->getView()->assign('previewSource', $this->generatePreviewSource($exercise, 1024, 768));
        $this->getView()->assign('exercise_description', $exercise->offsetGet('exercise_description'));
        $this->getView()->assign('deviceOptionsContent', $this->generateDeviceOptionsContent($exercise));
        $this->getView()->assign('exerciseOptionsContent', $this->generateExerciseOptionsContent($exercise));
        $this->collectMusclesForExerciseContent($exercise);

        return $this->getView()->render('loops/training-plan-exercise.phtml');
    }

    private function generatePreviewSource($exercise, $width, $height) 
    {
        $previewPicture = '/images/content/dynamisch/exercises/' . $exercise->offsetGet('exercise_id') . '/' .
            $exercise->offsetGet('exercise_preview_picture');
        $previewPictureSource = '/images/content/statisch/grafiken/kein_bild.png';

        if (is_file(getcwd() . '/' . $previewPicture)
            && is_readable(getcwd() . '/' . $previewPicture)
        ) {
            $thumbnailService = new Thumbnail();
            $thumbnailService->setThumbWidth($width);
            $thumbnailService->setThumbHeight($height);
            $thumbnailService->setSourceFilePathName(getcwd() . '/' . $previewPicture);
            $previewPictureSource = $thumbnailService->generateImageString();
        }
        return $previewPictureSource;
    }

    /**
     * @param $exercise
     *
     * @return string
     */
    public function generateExerciseOptionsContent($exercise)
    {

        $exerciseOptionsService = new ExerciseOptions($this->getView());
        $exerciseOptionsService->setShowTrainingProgress(true);
        $exerciseOptionsService->setAllowEdit(false);
        $exerciseOptionsService->setExerciseId($exercise['exercise_id']);
        $exerciseOptionsService->setTrainingPlanXExerciseId($exercise['training_plan_x_exercise_id']);

        return $exerciseOptionsService->generate();
    }

    /**
     * @param $exercise
     *
     * @return string
     */
    public function generateDeviceOptionsContent($exercise)
    {
        $deviceOptionsService = new DeviceOptions($this->getView());
        $deviceOptionsService->setShowTrainingProgress(true);
        $deviceOptionsService->setAllowEdit(false);
        $deviceOptionsService->setExerciseId($exercise['exercise_id']);
        $deviceOptionsService->setTrainingPlanXExerciseId($exercise['training_plan_x_exercise_id']);

        return $deviceOptionsService->generate();
    }

    /**
     * @param $trainingPlanExercise
     *
     * @return $this
     */
    private function collectMusclesForExerciseContent($trainingPlanExercise)
    {
        $exerciseXMuscleDb = new ExerciseXMuscle();
        $usedMusclesInExercise = $exerciseXMuscleDb->findMusclesForExercise(
            $trainingPlanExercise->training_plan_x_exercise_exercise_fk
        );

        foreach ($usedMusclesInExercise as $usedMuscleInExercise) {
            if (false == array_key_exists($usedMuscleInExercise->muscle_name, $this->usedMuscles)) {
                $this->usedMuscles[$usedMuscleInExercise->muscle_name] = 0;
            }
            $this->usedMuscles[$usedMuscleInExercise->muscle_name] +=
                $usedMuscleInExercise->exercise_x_muscle_muscle_use;
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
    private function generateUsedMusclesForTrainingPlanContent()
    {
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
            $this->getView()->assign(
                'muscleUsePercentage',
                number_format(($muscleUse / $this->muscleUsageMax) * 100, 2)
            );
            $muscleUseContent .= $this->getView()->render('loops/muscles-for-exercise.phtml');
        }

        return $muscleUseContent;
    }

    /**
     * @param $trainingPlanId
     *
     * @return string
     * @throws \Zend_View_Exception
     */
    public function generateTrainingPlanForEditContent($trainingPlanId)
    {
        $trainingPlansDb = new ModelDbTableTrainingPlans();
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
                $this->getView()->assign(
                    'trainingPlanOptionsContent',
                    $this->generateTrainingPlanOptionsContent($trainingPlan)
                );
                $this->trainingPlanTabHeaderContent .= $this->generateSplitTrainingPlanHeaderRow($trainingPlan, $count);
                $this->getView()->assign(
                    'exercisesContent',
                    $this->generateTrainingPlanExerciseForEditContent($trainingPlan->offsetGet('training_plan_id'))
                );
                $this->getView()->assign('trainingPlanId', $trainingPlan->offsetGet('training_plan_id'));
                $this->getView()->assign('trainingPlanUserId', $trainingPlan->offsetGet('training_plan_user_fk'));
                $this->getView()->assign('trainingPlanName', $trainingPlan->offsetGet('training_plan_name'));
                $trainingPlanContent .= $this->getView()->render('loops/training-plan-edit.phtml');
                ++$count;
            }
        }

        return $trainingPlanContent;
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $trainingPlan
     * @param                             $count
     *
     * @return string
     * @throws \Zend_View_Exception
     */
    private function generateSplitTrainingPlanHeaderRow(Zend_Db_Table_Row_Abstract $trainingPlan, $count)
    {
        $name = trim($trainingPlan->offsetGet('training_plan_name'));
        if (0 == strlen($name)) {
            $name = $this->translate('label_day') . '' . $count;
        }
        $this->getView()->assign('name', $name);
        return $this->getView()->render('loops/training-plan-split-header-row.phtml');
    }

    /**
     * generate trainingPlan options (delete|edit)
     *
     * @param \Zend_Db_Table_Row_Abstract $trainingPlan
     *
     * @return string
     */
    private function generateTrainingPlanOptionsContent(Zend_Db_Table_Row_Abstract $trainingPlan)
    {
        $id = $trainingPlan->offsetGet('training_plan_id');
        $resource = new AuthModelResourceTrainingPlans($trainingPlan);
        $role = new Member();
        $resourceName = 'default:training-plans';

        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource(
            $role->getRole(),
            $resourceName,
            'delete'
        );
        $content = '';

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'delete')) {
            $content .= '<div class="glyphicon glyphicon-trash delete-button" data-id="' . $id . '"></div>';
        }
        return $content;
    }

    /**
     * @param $trainingPlanId
     *
     * @return string
     * @throws \Zend_View_Exception
     */
    private function generateTrainingPlanExerciseForEditContent($trainingPlanId)
    {
        $trainingPlanXExercisesDb = new TrainingPlanXExercise();

        $trainingPlanXExerciseCollection =
            $trainingPlanXExercisesDb->findExercisesByTrainingPlanId($trainingPlanId);

        $exercisesContent = '';

        foreach ($trainingPlanXExerciseCollection as $trainingPlanExercise) {
            $this->getView()->assign($trainingPlanExercise->toArray());

            $exerciseId = $trainingPlanExercise->offsetGet('exercise_id');
            $trainingPlanExerciseId = $trainingPlanExercise->offsetGet('training_plan_x_exercise_id');

            $exerciseOptionsService = new ExerciseOptions($this->getView());
            $exerciseOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
            $exerciseOptionsService->setExerciseId($exerciseId);
            $exerciseOptionsService->setAllowEdit(true);
            $exerciseOptionsService->setShowDelete(true);

            $this->getView()->assign('exerciseOptionsContent', $exerciseOptionsService->generate());

            $deviceOptionsService = new DeviceOptions($this->getView());
            $deviceOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
            $deviceOptionsService->setExerciseId($exerciseId);
            $deviceOptionsService->setAllowEdit(true);
            $deviceOptionsService->setShowDelete(true);
            $deviceOptionsService->setConvertDropDownValues(true);

            $this->getView()->assign('previewSource', $this->generatePreviewSource($trainingPlanExercise, 800, 600));
            $this->getView()->assign('deviceOptionsContent', $deviceOptionsService->generate());

            $exercisesContent .= $this->getView()->render('loops/training-plan-exercise-edit.phtml');
        }

        return $exercisesContent;
    }

    /**
     * @return string
     */
    public function getTrainingPlanTabHeaderContent()
    {
        return $this->trainingPlanTabHeaderContent;
    }

    /**
     * @param string $trainingPlanTabHeaderContent
     */
    public function setTrainingPlanTabHeaderContent($trainingPlanTabHeaderContent)
    {
        $this->trainingPlanTabHeaderContent = $trainingPlanTabHeaderContent;
    }
}

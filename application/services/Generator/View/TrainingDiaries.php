<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 24.03.17
 * Time: 19:31
 */

namespace Service\Generator\View;

use Model\DbTable\ExerciseXMuscle;
use QRcode;

require_once APPLICATION_PATH . "/../library/qrlib/qrlib.php";

/**
 * Class TrainingDiaries
 *
 * @package Service\Generator\View
 */
class TrainingDiaries extends GeneratorAbstract
{

    private $exercisesCount = 0;

    public function start()
    {
    }

    /**
     * generates trainingPlan overview, if is the trainingplan not started already
     */
    private function generateIntro()
    {
    }

    /**
     * generates training diary content for current exercise
     *
     * @param \Zend_Db_Table_Row_Abstract $trainingDiaryExercise
     *
     * @return string
     */
    public function generateExerciseContent($trainingDiaryExercise)
    {
        static $count = 1;
        static $bFirstActiveSet = false;

        $usedMusclesCollection = array();
        $usedMuscleMinScore = null;
        $usedMuscleMaxScore = null;
        $exerciseXMuscleDb = new ExerciseXMuscle();
        $usedMusclesByExercise = $exerciseXMuscleDb->findMusclesForExercise(
            $trainingDiaryExercise->training_plan_x_exercise_exercise_fk
        );

        foreach ($usedMusclesByExercise as $usedMuscleByExercise) {
            if (false == array_key_exists($usedMuscleByExercise->muscle_name, $usedMusclesCollection)) {
                $usedMusclesCollection[$usedMuscleByExercise->muscle_name] = 0;
            }
            $muscleUsageScore = intval($usedMuscleByExercise->exercise_x_muscle_muscle_use);
            $usedMusclesCollection[$usedMuscleByExercise->muscle_name] += $muscleUsageScore;

            if (null == $usedMuscleMinScore
                || $usedMuscleMinScore > $usedMusclesCollection[$usedMuscleByExercise->muscle_name]
            ) {
                $usedMuscleMinScore = $usedMusclesCollection[$usedMuscleByExercise->muscle_name];
            }
            if (null == $usedMuscleMaxScore
                || $usedMuscleMaxScore < $usedMusclesCollection[$usedMuscleByExercise->muscle_name]
            ) {
                $usedMuscleMaxScore = $usedMusclesCollection[$usedMuscleByExercise->muscle_name];
            }
        }
        $this->_iMinBeanspruchterMuskel = $usedMuscleMinScore;
        $this->_iMaxBeanspruchterMuskel = $usedMuscleMaxScore;
        $this->_aBeanspruchteMuskeln = $usedMusclesCollection;
        $this->getView()->assign('iMinBeanspruchterMuskel', $usedMuscleMinScore);
        $this->getView()->assign('iMaxBeanspruchterMuskel', $usedMuscleMaxScore);
        $this->getView()->assign('aBeanspruchteMuskeln', $usedMusclesCollection);
        $this->getView()->assign(
            'exerciseOptionsContent',
            $this->generateExerciseOptionsContent($trainingDiaryExercise)
        );
        $this->getView()->assign('deviceOptionsContent', $this->generateDeviceOptionsContent($trainingDiaryExercise));
        $this->getView()->assign(
            'trainingDiaryXTrainingPlanId',
            $trainingDiaryExercise->offsetGet('training_diary_x_training_plan_id')
        );
        $this->getView()->assign(
            'trainingDiaryXTrainingPlanExerciseId',
            $trainingDiaryExercise->offsetGet('training_diary_x_training_plan_exercise_id')
        );
        $this->getView()->assign(
            'trainingPlanXExerciseId',
            $trainingDiaryExercise->offsetGet('training_plan_x_exercise_id')
        );
        $this->getView()->assign('exerciseSum', $this->getExercisesCount());
        $this->getView()->assign(
            'exerciseFinished',
            $trainingDiaryExercise->offsetGet('training_diary_x_training_plan_exercise_flag_finished')
        );
        $this->getView()->assign($trainingDiaryExercise->toArray());

        $sUrl = 'http://trainingsmanager.org/training-diaries/show-exercise/id/' .
            $trainingDiaryExercise->offsetGet('training_diary_x_training_plan_training_diary_fk');

        $qrCodeFileName = APPLICATION_PATH . '/../data/tmp/'.time();

        QRcode::png($sUrl, $qrCodeFileName, QR_ECLEVEL_H);
        $qrCode = 'data:image/png;base64,'.base64_encode(file_get_contents($qrCodeFileName));

        unlink($qrCodeFileName);

        $this->getView()->assign('qrCode', $qrCode);

        if (!$trainingDiaryExercise->offsetGet('training_diary_x_training_plan_exercise_flag_finished')
            && !$bFirstActiveSet
        ) {
            $this->getView()->assign('exerciseActive', 'active');
            $bFirstActiveSet = true;
        } else {
            $this->getView()->assign('exerciseActive', '');
        }

        $this->getView()->assign('currentExerciseNumber', $count);
        ++$count;

        return $this->getView()->render('loops/training-diary-exercise.phtml');
    }

    /**
     * @param $trainingDiaryXExercise
     *
     * @return string
     */
    public function generateExerciseOptionsContent($trainingDiaryXExercise)
    {

        $exerciseOptionsService = new ExerciseOptions($this->getView());
        $exerciseOptionsService->setShowTrainingProgress(true);
        $exerciseOptionsService->setAllowEdit(true);
        // bereits ein value durch eine übung gesetzt
        if (array_key_exists('training_diary_x_exercise_option_exercise_option_value', $trainingDiaryXExercise)) {
            $exerciseOptionsService->setSelectedOptionValue(
                $trainingDiaryXExercise['training_diary_x_exercise_option_exercise_option_value']
            );
        }
        $exerciseOptionsService->setExerciseId($trainingDiaryXExercise['exercise_id']);
        $exerciseOptionsService->setTrainingPlanXExerciseId($trainingDiaryXExercise['training_plan_x_exercise_id']);
        $exerciseOptionsService->setExerciseFinished(
            $trainingDiaryXExercise['training_diary_x_training_plan_exercise_flag_finished']
        );

        return $exerciseOptionsService->generate();
    }

    /**
     * @param $trainingDiaryXExercise
     *
     * @return string
     */
    public function generateDeviceOptionsContent($trainingDiaryXExercise)
    {
        $deviceOptionsService = new DeviceOptions($this->getView());
        $deviceOptionsService->setShowTrainingProgress(true);
        $deviceOptionsService->setAllowEdit(true);

        $deviceOptionsService->setExerciseId($trainingDiaryXExercise['exercise_id']);
        $deviceOptionsService->setTrainingDiaryXTrainingPlanExerciseId(
            $trainingDiaryXExercise['training_diary_x_training_plan_exercise_id']
        );
        $deviceOptionsService->setTrainingPlanXExerciseId($trainingDiaryXExercise['training_plan_x_exercise_id']);
        $deviceOptionsService->setExerciseFinished(
            $trainingDiaryXExercise['training_diary_x_training_plan_exercise_flag_finished']
        );

        return $deviceOptionsService->generate();
    }

    /**
     * @return int
     */
    public function getExercisesCount()
    {
        return $this->exercisesCount;
    }

    /**
     * @param int $exercisesCount
     */
    public function setExercisesCount($exercisesCount)
    {
        $this->exercisesCount = $exercisesCount;
    }
}

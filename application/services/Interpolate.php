<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 03.06.17
 * Time: 12:29
 */

namespace Service;

use Model\DbTable\TrainingPlans;
use Zend_Db_Table_Row_Abstract;
use Model\DbTable\TrainingPlanXExercise;
use Model\DbTable\TrainingPlanXExerciseOption;
use Model\DbTable\TrainingPlanXDeviceOption;
use Model\DbTable\TrainingDiaries;
use Model\DbTable\TrainingDiaryXTrainingPlan;
use Model\DbTable\TrainingDiaryXTrainingPlanExercise;
use Model\DbTable\TrainingDiaryXExerciseOption;
use Model\DbTable\TrainingDiaryXDeviceOption;
use Zend_Db_Table_Rowset_Abstract;
use Zend_Auth;




class Interpolate
{

    private $optionsAllowedToIncrease = [
        'devices' => [
            'GEWICHT' => 0.25,
        ],
        'exercises' => [
            'SÄTZE' => 0.1,
            'WIEDERHOLUNGEN' => 0.2
        ],
    ];

    /**
     * @param $userId
     *
     * @return $this
     */
    public function trainingDiary($userId) 
    {

        $trainingPlansDb = new TrainingPlans();
        $currentTrainingPlan = $trainingPlansDb->findActiveTrainingPlanByUserId($userId);
        $processedExerciseOptions = [];
        $processedDeviceOptions = [];
        $startDate = date('Y-m-d');
        $daysToNewTrainingPlan = mt_rand(12, 24);

        if ($currentTrainingPlan instanceof Zend_Db_Table_Row_Abstract) {
            $trainingPlanXExerciseDb = new TrainingPlanXExercise();
            $trainingPlanXExerciseOptionDb = new TrainingPlanXExerciseOption();
            $trainingPlanXDeviceOptionDb = new TrainingPlanXDeviceOption();
            $trainingDiariesDb = new TrainingDiaries();
            $trainingDiaryXTrainingPlanDb = new TrainingDiaryXTrainingPlan();
            $trainingDiaryXTrainingPlanExerciseDb = new TrainingDiaryXTrainingPlanExercise();
            $trainingDiaryXExerciseOptionDb = new TrainingDiaryXExerciseOption();
            $trainingDiaryXDeviceOptionDb = new TrainingDiaryXDeviceOption();

            $trainingPlanId = $currentTrainingPlan->offsetGet('training_plan_id');
            $trainingPlanXExercises = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlanId);
            $additionalDayCount = 0;

            if ($trainingPlanXExercises instanceof Zend_Db_Table_Rowset_Abstract) {
                for ($trainingDays = 0; $trainingDays < $daysToNewTrainingPlan; ++$trainingDays) {
                    $data = [
                        'training_diary_comment' => 'Interpolated on ' . $startDate,
                        'training_diary_create_date' => date('Y-m-d H:i:s'),
                        'training_diary_create_user_fk' => $this->findCurrentUserId()
                    ];
                    $currentTrainingDiaryId = $trainingDiariesDb->insert($data);
                    $data = [
                        'training_diary_x_training_plan_training_plan_fk' => $trainingPlanId,
                        'training_diary_x_training_plan_flag_finished' => 1,
                        'training_diary_x_training_plan_training_diary_fk' => $currentTrainingDiaryId,
                        'training_diary_x_training_plan_create_date' => date('Y-m-d H:i:s'),
                        'training_diary_x_training_plan_create_user_fk' => $this->findCurrentUserId()
                    ];
                    $currentTrainingDiaryXTrainingPlanId = $trainingDiaryXTrainingPlanDb->insert($data);
                    foreach ($trainingPlanXExercises as $trainingPlanXExercise) {
                        $currentTrainingPlanXExerciseId = $trainingPlanXExercise->offsetGet('training_plan_x_exercise_id');
                        $currentDate = date('Y-m-d', strtotime($startDate . ' ' . $additionalDayCount . 'DAYS'));

                        $trainingPlanXExerciseId = $trainingPlanXExercise->offsetGet('training_plan_x_exercise_id');
                        $data = [
                            'training_diary_x_training_plan_exercise_t_p_x_e_fk' => $currentTrainingPlanXExerciseId,
                            'training_diary_x_training_plan_exercise_comment' => 'Interpolated on ' . $startDate,
                            'training_diary_x_training_plan_exercise_flag_finished' => 1,
                            'training_diary_x_training_plan_exercise_training_diary_fk' => $currentTrainingDiaryId,
                            'training_diary_x_training_plan_exercise_t_d_x_t_p_fk' => $currentTrainingDiaryXTrainingPlanId,
                            'training_diary_x_training_plan_exercise_create_date' => $currentDate,
                            'training_diary_x_training_plan_exercise_create_user_fk' => $this->findCurrentUserId()
                        ];

                        $trainingDiaryXTrainingPlanExerciseId = $trainingDiaryXTrainingPlanExerciseDb->insert($data);
                        $exerciseOptions = $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($trainingPlanXExerciseId);

                        foreach ($exerciseOptions as $exerciseOption) {
                            $optionName = mb_strtoupper($exerciseOption->offsetGet('exercise_option_name'));
                            $optionHash = $trainingPlanXExerciseId . '_' . $optionName;
                            if (array_key_exists($optionName, $this->optionsAllowedToIncrease['exercises'])) {
                                if (!array_key_exists($optionHash, $processedExerciseOptions)) {
                                    $processedExerciseOptions[$optionHash] = $exerciseOption->offsetGet('training_plan_x_exercise_option_exercise_option_value');
                                } else {
                                    $processedExerciseOptions[$optionHash] += (number_format(mt_rand(-1, 5) * $this->optionsAllowedToIncrease['exercises'][$optionName], 1, '.', ''));
                                }
                                $data = [
                                    'training_diary_x_exercise_option_exercise_option_fk' => $exerciseOption->offsetGet('exercise_option_id'),
                                    'training_diary_x_exercise_option_exercise_option_value' => $processedExerciseOptions[$optionHash],
                                    'training_diary_x_exercise_option_t_d_x_t_p_e_fk' => $trainingDiaryXTrainingPlanExerciseId,
                                    'training_diary_x_exercise_option_create_date' => $currentDate,
                                    'training_diary_x_exercise_option_create_user_fk' => $this->findCurrentUserId(),
                                ];
                                $trainingDiaryXExerciseOptionDb->insert($data);
                            }
                        }
                        $deviceOptions = $trainingPlanXDeviceOptionDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId($trainingPlanXExerciseId);

                        foreach ($deviceOptions as $deviceOption) {
                            $optionName = mb_strtoupper($deviceOption->offsetGet('device_option_name'));
                            $optionHash = $trainingPlanXExerciseId . '_' . $optionName;
                            if (array_key_exists($optionName, $this->optionsAllowedToIncrease['devices'])) {
                                if (!array_key_exists($optionHash, $processedDeviceOptions)) {
                                    $processedDeviceOptions[$optionHash] = $deviceOption->offsetGet('training_plan_x_device_option_device_option_value');
                                } else {
                                    $processedDeviceOptions[$optionHash] += (number_format(mt_rand(-1, 5) * $this->optionsAllowedToIncrease['devices'][$optionName], 1, '.', ''));
                                }
                                $data = [
                                    'training_diary_x_device_option_device_option_fk' => $deviceOption->offsetGet('device_option_id'),
                                    'training_diary_x_device_option_device_option_value' => $processedDeviceOptions[$optionHash],
                                    'training_diary_x_device_option_t_d_x_t_p_e_fk' => $trainingDiaryXTrainingPlanExerciseId,
                                    'training_diary_x_device_option_create_date' => $currentDate,
                                    'training_diary_x_device_option_create_user_fk' => $this->findCurrentUserId(),
                                ];
                                $trainingDiaryXDeviceOptionDb->insert($data);
                            }
                        }
                    }
                    $additionalDayCount += mt_rand(2, 4);
                }
            }
        }
        return $this;
    }

    private function findCurrentUserId() 
    {
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            return $user->user_id;
        }
        return false;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 07.05.14
 * Time: 22:15
 */

require_once __DIR__ . '/../models/DbTable/Exercises.php';

class Service_TrainingPlan {

    /** @var Model_DbTable_Exercises */
    private $_oExerciseStorage = null;

    /**
     * wenn trainingPlanCollection keine exercises enthält und eine trainingPlanId => parent eines splits
     * wenn trainingPlanCollection exercises enthält und eine trainingPlanParentId übergeben wurde => children eines
     * splits wenn trainingPlanCollection exercises enthält und keinen TrainingPlanParentId => einzelner trianingsplan
     * ohne split
     *
     * @param      $trainingPlan
     * @param      $trainingPlanUserId
     * @param null $trainingPlantParentId
     *
     * @return $this
     */
    public function saveTrainingPlan($trainingPlan, $trainingPlanUserId, $trainingPlantParentId = null) {
        static $trainingPlanCount = 0;
        ++$trainingPlanCount;

        $trainingPlanDb = new Model_DbTable_TrainingPlans();
        $trainingPlanId = intval($trainingPlan['trainingPlanId']);
        $trainingPlanName = trim($trainingPlan['trainingPlanName']);

        $userId = $this->findCurrentUserId();

        /** new TrainingPlan */
        if (empty($trainingPlanId)) {
            $trainingPlanLayoutFk = 1;
            // expect, that training plan without exercises means, its a parent training plan,
            // also the empty trainingPlanParentId its a sign for the first trainingPlan to save
            if (!$trainingPlantParentId
                && !array_key_exists('exercises', $trainingPlan)
            ) {
                $trainingPlanLayoutFk = 2;
            }
            $data = [
                'training_plan_name' => $trainingPlanName,
                'training_plan_order' => $trainingPlanCount,
                'training_plan_training_plan_layout_fk' => $trainingPlanLayoutFk,
                'training_plan_user_fk' => $trainingPlanUserId,
                'training_plan_active' => 1,
                'training_plan_parent_fk' => $trainingPlantParentId,
                'training_plan_create_date' => date('Y-m-d H:i:s'),
                'training_plan_create_user_fk' => $userId,
            ];
            $trainingPlanId = $trainingPlanDb->insert($data);
        }

        if (array_key_exists('trainingPlanId', $trainingPlan)
            && ! array_key_exists('exercises', $trainingPlan)
        ) {
            if (1 === count($trainingPlan)) {
                $trainingPlansDb = new Model_DbTable_TrainingPlans();
                $trainingPlansDb->delete('training_plan_id = ' . $trainingPlanId);
            } else {
                foreach ($trainingPlan as $currentTrainingPlan) {
                    if (is_array($currentTrainingPlan)) {
                        $this->saveTrainingPlan($currentTrainingPlan, $trainingPlanUserId, $trainingPlanId);
                    }
                }
            }
        } else {
            $currentTrainingPlan = $trainingPlanDb->findTrainingPlan($trainingPlanId);
            $currentTrainingPlanName = $currentTrainingPlan->offsetGet('training_plan_name');
            $currentTrainingPlanOrder = $currentTrainingPlan->offsetGet('training_plan_order');

            if ($trainingPlanName != $currentTrainingPlanName
                || $currentTrainingPlanOrder != $trainingPlanCount
            ) {
                $data = [
                    'training_plan_name' => $trainingPlanName,
                    'training_plan_order' => $trainingPlanCount,
                    'training_plan_update_date' => date('Y-m-d H:i:s'),
                    'training_plan_update_user_fk' => $userId
                ];

                $trainingPlanDb->update($data, "training_plan_id = '" . $trainingPlanId . "'");
            }

            $currentTrainingPlanXExerciseCollection = $this->collectCurrentExercisesByTrainingPlan($trainingPlanId);

            foreach ($trainingPlan['exercises'] as $exercise) {
                $currentTrainingPlanXExerciseCollection = $this->saveExercise(
                    $exercise, $trainingPlanId, $currentTrainingPlanXExerciseCollection);
            }

            $this->removeWasteExercisesFromDatabase($currentTrainingPlanXExerciseCollection);
        }
        return $this;
    }

    private function removeWasteExercisesFromDatabase($currentTrainingPlanXExerciseCollection) {

        $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
        $trainingPlanXExerciseOptionDb = new Model_DbTable_TrainingPlanXExerciseOption();
        $trainingPlanXDeviceOptionDb = new Model_DbTable_TrainingPlanXDeviceOption();

        /** delete all old exercises in DB */
        foreach ($currentTrainingPlanXExerciseCollection as $exerciseId => $currentTrainingPlanXExercise) {
            $currentTrainingPlanExerciseId = $currentTrainingPlanXExercise['exercise']->offsetGet('training_plan_x_exercise_id');

            $currentExerciseOptionCollection = $currentTrainingPlanXExercise['exerciseOptions'];
            foreach ($currentExerciseOptionCollection as $currentExerciseOptionId => $currentExerciseOption) {
                $trainingPlanXExerciseOptionDb->deleteTrainingPlanExerciseOption($currentExerciseOption['trainingPlanXExerciseOptionId']);
            }

            $currentDeviceOptionCollection = $currentTrainingPlanXExercise['deviceOptions'];
            foreach ($currentDeviceOptionCollection as $currentDeviceOptionId => $currentDeviceOption) {
                $trainingPlanXDeviceOptionDb->deleteTrainingPlanDeviceOption($currentDeviceOption['trainingPlanXDeviceOptionId']);
            }
            $trainingPlanXExerciseDb->delete("training_plan_x_exercise_id = '" . $currentTrainingPlanExerciseId . "'");
        }
        return $this;
    }

    private function saveExercise($exercise, $trainingPlanId, $currentTrainingPlanXExerciseCollection) {

        $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();

        $trainingPlanXExerciseId = $exercise['trainingPlanExerciseId'];
        $exerciseComment = $exercise['exerciseComment'];
        $exerciseId = $exercise['exerciseId'];

        $exerciseCount = 0;
        $userId = 1;

        // new trainingPlanXExercise
        if (empty($trainingPlanXExerciseId)) {
            $data = [
                'training_plan_x_exercise_exercise_fk' => $exerciseId,
                'training_plan_x_exercise_exercise_order' => $exerciseCount,
                'training_plan_x_exercise_training_plan_fk' => $trainingPlanId,
                'training_plan_x_exercise_create_date' => date('Y-m-d H:i:s'),
                'training_plan_x_exercise_create_user_fk' => $userId
            ];
            $trainingPlanXExerciseId = $trainingPlanXExerciseDb->saveTrainingPlanExercise($data);
            // check if must update
        } else {
            $exerciseInDb = $currentTrainingPlanXExerciseCollection[$exerciseId]['exercise'];
            if ($exerciseCount != $exerciseInDb['training_plan_x_exercise_exercise_order']
                || $trainingPlanId != $exerciseInDb['training_plan_x_exercise_training_plan_fk']
                || $exerciseComment != $exerciseInDb['training_plan_x_exercise_comment']
            ) {
                $data = [
                    'training_plan_x_exercise_exercise_order' => $exerciseCount,
                    'training_plan_x_exercise_training_plan_fk' => $trainingPlanId,
                    'training_plan_x_exercise_comment' => $exerciseComment,
                    'training_plan_x_exercise_update_date' => date('Y-m-d H:i:s'),
                    'training_plan_x_exercise_update_user_fk' => $userId
                ];
                $trainingPlanXExerciseDb->updateTrainingPlanExercise($data, $trainingPlanXExerciseId);
            }
        }

        if (isset($exercise['exerciseOptions'])
            && is_array($exercise['exerciseOptions'])
        ) {
            $currentTrainingPlanXExerciseCollection = $this->processExerciseOptions($exercise['exerciseOptions'], $exerciseId, $trainingPlanXExerciseId, $currentTrainingPlanXExerciseCollection);
        }

        if (isset($exercise['deviceOptions'])
            && is_array($exercise['deviceOptions'])
        ) {
            $currentTrainingPlanXExerciseCollection = $this->processDeviceOptions($exercise['deviceOptions'], $exerciseId, $trainingPlanXExerciseId, $currentTrainingPlanXExerciseCollection);
        }
        unset($currentTrainingPlanXExerciseCollection[$exercise['exerciseId']]);
        ++$exerciseCount;

        return $currentTrainingPlanXExerciseCollection;
    }

    private function processExerciseOptions($exerciseOptions, $exerciseId, $trainingPlanXExerciseId, $currentTrainingPlanXExerciseCollection) {

        $userId = 1;
        $trainingPlanXExerciseOptionDb = new Model_DbTable_TrainingPlanXExerciseOption();

        foreach ($exerciseOptions as $exerciseOption) {
            $exerciseOptionId = $exerciseOption['exerciseOptionId'];

            // wenn exerciseOption bereits in der DB
            if (array_key_exists('exerciseOptions', $currentTrainingPlanXExerciseCollection[$exerciseId])
                && array_key_exists($exerciseOptionId,
                    $currentTrainingPlanXExerciseCollection[$exerciseId]['exerciseOptions'])
            ) {
                $trainingPlanExerciseOptionInDb = $currentTrainingPlanXExerciseCollection[$exerciseId]['exerciseOptions'][$exerciseOptionId];
                // wenn value der übergabe leer ist => löschen
                if ((empty($exerciseOption['exerciseOptionValue'])
                        || -1 == $exerciseOption['exerciseOptionValue'])
                    && isset($exerciseOption['trainingPlanXExerciseOptionId'])
                ) {
                    $trainingPlanXExerciseOptionDb->deleteTrainingPlanExerciseOption($exerciseOption['trainingPlanXExerciseOptionId']);
                } else if ($exerciseOption['exerciseOptionValue'] != $trainingPlanExerciseOptionInDb['training_plan_x_exercise_option_exercise_option_value']) {
                    $data = [
                        'training_plan_x_exercise_option_exercise_option_value' => $exerciseOption['exerciseOptionValue'],
                        'training_plan_x_exercise_option_update_date' => date('Y-m-d H:i:s'),
                        'training_plan_x_exercise_option_update_user_fk' => $userId,
                    ];
                    $trainingPlanXExerciseOptionDb->updateTrainingPlanExerciseOption($data,
                        $exerciseOption['trainingPlanXExerciseOptionId']);
                }
            } else if (! empty($exerciseOption['exerciseOptionValue'])) {
                $data = [
                    'training_plan_x_exercise_option_training_plan_exercise_fk' => $trainingPlanXExerciseId,
                    'training_plan_x_exercise_option_exercise_option_fk' => $exerciseOption['exerciseOptionId'],
                    'training_plan_x_exercise_option_exercise_option_value' => $exerciseOption['exerciseOptionValue'],
                    'training_plan_x_exercise_option_create_date' => date('Y-m-d H:i:s'),
                    'training_plan_x_exercise_option_create_user_fk' => $userId,
                ];
                $trainingPlanExerciseOptionId = $trainingPlanXExerciseOptionDb->saveTrainingPlanExerciseOption($data);
            }
            unset($currentTrainingPlanXExerciseCollection[$exerciseId]['exerciseOptions'][$exerciseOptionId]);
        }

        return $currentTrainingPlanXExerciseCollection;
    }

    private function processDeviceOptions($deviceOptions, $exerciseId, $trainingPlanXExerciseId, $currentTrainingPlanXExerciseCollection) {

        $userId = 1;
        $trainingPlanXDeviceOptionDb = new Model_DbTable_TrainingPlanXDeviceOption();

        foreach ($deviceOptions as $deviceOption) {
            $deviceOptionId = $deviceOption['deviceOptionId'];

            // wenn deviceOption bereits in der DB
            if (array_key_exists('deviceOptions', $currentTrainingPlanXExerciseCollection[$exerciseId])
                && array_key_exists($deviceOptionId,
                    $currentTrainingPlanXExerciseCollection[$exerciseId]['deviceOptions'])
            ) {
                $trainingPlanDeviceOptionInDb = $currentTrainingPlanXExerciseCollection[$exerciseId]['deviceOptions'][$deviceOptionId];
                // wenn value der übergabe leer ist => löschen
                if ((empty($deviceOption['deviceOptionValue'])
                        || -1 == $deviceOption['deviceOptionValue'])
                    && 0 < $deviceOption['trainingPlanXDeviceOptionId']
                ) {
                    $trainingPlanXDeviceOptionDb->deleteTrainingPlanDeviceOption($deviceOption['trainingPlanXDeviceOptionId']);
                } else if ($deviceOption['deviceOptionValue'] != $trainingPlanDeviceOptionInDb['training_plan_x_device_option_device_option_value']) {
                    $data = [
                        'training_plan_x_device_option_device_option_value' => $deviceOption['deviceOptionValue'],
                        'training_plan_x_device_option_update_date' => date('Y-m-d H:i:s'),
                        'training_plan_x_device_option_update_user_fk' => $userId,
                    ];
                    $trainingPlanXDeviceOptionDb->updateTrainingPlanDeviceOption($data,
                        $deviceOption['trainingPlanXDeviceOptionId']);
                }
            } else if (! empty($deviceOption['deviceOptionValue'])) {
                $data = [
                    'training_plan_x_device_option_training_plan_exercise_fk' => $trainingPlanXExerciseId,
                    'training_plan_x_device_option_device_option_fk' => $deviceOption['deviceOptionId'],
                    'training_plan_x_device_option_device_option_value' => $deviceOption['deviceOptionValue'],
                    'training_plan_x_device_option_create_date' => date('Y-m-d H:i:s'),
                    'training_plan_x_device_option_create_user_fk' => $userId,
                ];
                $trainingPlanDeviceOptionId = $trainingPlanXDeviceOptionDb->saveTrainingPlanDeviceOption($data);
            }
            unset($currentTrainingPlanXExerciseCollection[$exerciseId]['deviceOptions'][$deviceOptionId]);
        }

        return $currentTrainingPlanXExerciseCollection;
    }

    private function collectCurrentExercisesByTrainingPlan($trainingPlanId) {

        $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
        $trainingPlanXExerciseOptionDb = new Model_DbTable_TrainingPlanXExerciseOption();
        $trainingPlanXDeviceOptionDb = new Model_DbTable_TrainingPlanXDeviceOption();

        $currentTrainingPlanXExercisesDb = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlanId);
        $currentTrainingPlanXExerciseCollection = [];

        /** iterate all trainingPlanXExercises in DB */
        foreach ($currentTrainingPlanXExercisesDb as $currentTrainingPlanExercise) {
            $currentTrainingPlanExerciseId = $currentTrainingPlanExercise->offsetGet('training_plan_x_exercise_id');
            $currentExerciseId = $currentTrainingPlanExercise->offsetGet('exercise_id');
            $currentTrainingPlanXExerciseCollection[$currentExerciseId] = [];
            $currentTrainingPlanXExerciseCollection[$currentExerciseId]['exercise'] = $currentTrainingPlanExercise;
            $currentExerciseOptionCollection = $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId($currentTrainingPlanExerciseId);

            /** iterate all trainingPlanExerciseOptions in Db*/
            foreach ($currentExerciseOptionCollection as $currentExerciseOption) {
                $currentExerciseOptionId = $currentExerciseOption->offsetGet('training_plan_x_exercise_option_exercise_option_fk');
                $currentTrainingPlanXExerciseCollection[$currentExerciseId]['exerciseOptions'][$currentExerciseOptionId] = $currentExerciseOption;
            }

            $currentDeviceOptionCollection = $trainingPlanXDeviceOptionDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId($currentTrainingPlanExerciseId);
            /** iterate all trainingPlanExerciseOptions in Db*/
            foreach ($currentDeviceOptionCollection as $currentDeviceOption) {
                $currentDeviceOptionId = $currentDeviceOption->offsetGet('training_plan_x_device_option_device_option_fk');
                $currentTrainingPlanXExerciseCollection[$currentExerciseId]['deviceOptions'][$currentDeviceOptionId] = $currentDeviceOption;
            }
        }

        return $currentTrainingPlanXExerciseCollection;
    }

    public function createBaseTrainingPlan($iUserId) {
        $oTrainingsplanLayouts = new Model_DbTable_TrainingPlanLayouts();
        $oTrainingsplanLayout = $oTrainingsplanLayouts->findTrainingPlanLayoutByName('Normal');
        $iTrainingsplanLayoutId = $oTrainingsplanLayout->training_plan_layout_id;

        if (is_numeric($iTrainingsplanLayoutId)
            && 0 < $iTrainingsplanLayoutId
        ) {
            $this->deactivateAllCurrentTrainingPlans($iUserId);

            $aData = array(
                'training_plan_training_plan_layout_fk' => $iTrainingsplanLayoutId,
                'training_plan_user_fk' => $iUserId,
                'training_plan_active' => 1,
                'training_plan_order' => 1,
                'training_plan_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_create_date' => date('Y-m-d H:i:s'),
            );
            $iTrainingsplanId = $this->createTrainingPlan($aData);

            return $iTrainingsplanId;
        } else {
            echo "Es konnte kein Layout für Normale Trainingspläne gefunden werden!";
        }
        return false;
    }

    /**
     * @param $userId
     *
     * @return int
     */
    private function deactivateAllCurrentTrainingPlans($userId) {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingPlansDb->update(['training_plan_active' => 0], 'training_plan_user_fk = "' . $userId . '"');
    }

    public function createSplitTrainingPlan($iUserId) {
        $oTrainingsplanLayouts = new Model_DbTable_TrainingPlanLayouts();
        $oTrainingsplanLayout = $oTrainingsplanLayouts->findTrainingPlanLayoutByName('Split');
        $iTrainingsplanLayoutId = $oTrainingsplanLayout->training_plan_layout_id;

        if (is_numeric($iTrainingsplanLayoutId)
            && 0 < $iTrainingsplanLayoutId
        ) {
            $aData = array(
                'training_plan_training_plan_layout_fk' => $iTrainingsplanLayoutId,
                'training_plan_active' => 1,
                'training_plan_user_fk' => $iUserId,
                'training_plan_order' => 1,
                'training_plan_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_create_date' => date('Y-m-d H:i:s')
            );
            $iTrainingsplanParentId = $this->createTrainingPlan($aData);

            if (is_numeric($iTrainingsplanParentId)
                && 0 < $iTrainingsplanParentId
            ) {
                $oTrainingsplanLayout = $oTrainingsplanLayouts->findTrainingPlanLayoutByName('Normal');
                $iTrainingsplanLayoutId = $oTrainingsplanLayout->training_plan_layout_id;
                $aData = array(
                    'training_plan_training_plan_layout_fk' => $iTrainingsplanLayoutId,
                    'training_plan_parent_fk' => $iTrainingsplanParentId,
                    'training_plan_user_fk' => $iUserId,
                    'training_plan_order' => 2,
                    'training_plan_active' => 1,
                    'training_plan_create_user_fk' => $this->findCurrentUserId(),
                    'training_plan_create_date' => date('Y-m-d H:i:s')
                );
                $iTrainingsplanId = $this->createTrainingPlan($aData);
            }

            return $iTrainingsplanParentId;
        } else {
            echo "Es konnte kein Layout für Split Trainingspläne gefunden werden!";

            return false;
        }
    }

    public function createTrainingPlan($aData) {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();

        $trainingPlanId = $trainingPlansDb->insert($aData);

        return $trainingPlanId;
    }

    /**
     * search current active training plan
     */
    public function searchCurrentTrainingPlan($userId) {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingDiaryXTrainingPlanDb = new Model_DbTable_TrainingDiaryXTrainingPlan();
        $currentOpenTrainingDiary = $trainingDiaryXTrainingPlanDb->findLastOpenTrainingPlanByUserId($userId);

        /** current open training diary entry found! */
        if ($currentOpenTrainingDiary instanceof Zend_Db_Table_Row_Abstract) {
            return $currentOpenTrainingDiary;
        }

        $lastFinishedTrainingDiary = $trainingDiaryXTrainingPlanDb->findLastFinishedActiveTrainingPlanByUserId($userId);

        // last finished training diary of the current active training plan exists
        if ($lastFinishedTrainingDiary instanceof Zend_Db_Table_Row_Abstract) {
            // single training plan -> not have to search other
            if (1 == $lastFinishedTrainingDiary->offsetGet('training_plan_training_plan_layout_fk')
                && empty($lastFinishedTrainingDiary->offsetGet('training_plan_parent_fk'))
            ) {
                return $lastFinishedTrainingDiary;
            }
            $nextTrainingPlan = $trainingPlansDb->findNextActiveTrainingPlan(
                $userId,
                $lastFinishedTrainingDiary->offsetGet('training_plan_order')
            );

            if (!$nextTrainingPlan instanceof Zend_Db_Table_Row_Abstract) {
                return $trainingPlansDb->findActiveTrainingPlanByUserId($userId);
            }
            return $nextTrainingPlan;
        }

        // no training diary entry found for the current active training plan, get first entry of current active training plan
        return $trainingPlansDb->findActiveTrainingPlanByUserId($userId);
    }

    protected function findCurrentUserId() {
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            return $user->user_id;
        }
        return false;
    }
}

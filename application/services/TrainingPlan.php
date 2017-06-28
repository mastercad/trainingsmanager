<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 07.05.14
 * Time: 22:15
 */

namespace Service;

use Model\DbTable\Exercises;
use Model\DbTable\TrainingPlans;
use Model\DbTable\TrainingPlanXExercise;
use Model\DbTable\TrainingPlanXExerciseOption;
use Model\DbTable\TrainingPlanXDeviceOption;
use Zend_Db_Table_Row_Abstract;
use Model\DbTable\TrainingPlanLayouts;
use Model\DbTable\TrainingDiaryXTrainingPlan;
use Zend_Auth;

require_once __DIR__ . '/../models/DbTable/Exercises.php';

/**
 * Class TrainingPlan
 *
 * @package Service
 */
class TrainingPlan
{

    /**
     * @var Exercises
     */
    private $oExerciseStorage = null;

    /**
     * wenn trainingPlanCollection keine exercises enthält und eine trainingPlanId => parent eines splits
     * wenn trainingPlanCollection exercises enthält und eine trainingPlanParentId übergeben wurde => children eines
     * splits wenn trainingPlanCollection exercises enthält und keinen TrainingPlanParentId => einzelner trianingsplan
     * ohne split
     *
     * @param $trainingPlan
     * @param $trainingPlanUserId
     * @param null               $trainingPlantParentId
     *
     * @return $this
     */
    public function saveTrainingPlan($trainingPlan, $trainingPlanUserId, $trainingPlantParentId = null)
    {
        static $trainingPlanCount = 0;
        ++$trainingPlanCount;
        $trainingPlanShouldDeleted = array_key_exists('deleted', $trainingPlan) && 1 == $trainingPlan['deleted'];
        $trainingPlanDb = new TrainingPlans();
        $trainingPlanId = intval($trainingPlan['trainingPlanId']);
        $trainingPlanName = trim($trainingPlan['trainingPlanName']);
        $trainingPlanType = (array_key_exists('type', $trainingPlan) && 'parent' == $trainingPlan['type']) ?
            'parent' :
            'normal';

        if ('parent' != $trainingPlanType
            && (!array_key_exists('exercises', $trainingPlan)
            || 0 == count($trainingPlan['exercises']))
        ) {
            $trainingPlanShouldDeleted = true;
        }

        $userId = $this->findCurrentUserId();

        /**
         * new TrainingPlan
         */
        if (!$trainingPlanShouldDeleted
            && empty($trainingPlanId)
        ) {
            $trainingPlanLayoutFk = 1;
            // expect, that training plan without exercises means, its a parent training plan,
            // also the empty trainingPlanParentId its a sign for the first trainingPlan to save
            if (! $trainingPlantParentId
                && array_key_exists('type', $trainingPlan)
                && 'parent' == $trainingPlan['type']
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

        if ($trainingPlanShouldDeleted) {
            if (!empty($trainingPlanId)) {
                $this->deleteTrainingPlan($trainingPlanId);
            }
            // its a parent training plan => save the child training plans
        } elseif ('parent' == $trainingPlanType) {
            foreach ($trainingPlan['trainingPlans'] as $count => $currentTrainingPlan) {
                $this->saveTrainingPlan($currentTrainingPlan, $trainingPlanUserId, $trainingPlanId);
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

            $resetExerciseOrderCount = true;
            foreach ($trainingPlan['exercises'] as $exercise) {
                $currentTrainingPlanXExerciseCollection = $this->saveExercise(
                    $exercise,
                    $trainingPlanId,
                    $currentTrainingPlanXExerciseCollection,
                    $resetExerciseOrderCount
                );
                $resetExerciseOrderCount = false;
            }

            $this->removeWasteExercisesFromDatabase($currentTrainingPlanXExerciseCollection);
        }
        return $this;
    }

    /**
     * deletes trainingPlan, trainingPlanExercises, trainingPlanExerciseOptions and trainingPlanDeviceOptions
     *
     * @param $trainingPlanId
     *
     * @return int
     */
    public function deleteTrainingPlan($trainingPlanId)
    {
        $trainingPlansDb = new TrainingPlans();
        $trainingPlanXExerciseDb = new TrainingPlanXExercise();

        $trainingPlanExercisesCollection = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlanId);
        foreach ($trainingPlanExercisesCollection as $trainingPlanXExercise) {
            $this->deleteTrainingPlanExercise($trainingPlanXExercise->offsetGet('training_plan_x_exercise_id'));
        }

        return $trainingPlansDb->delete('training_plan_id = ' . $trainingPlanId);
    }

    /**
     * @param $trainingPlanXExerciseId
     */
    public function deleteTrainingPlanExercise($trainingPlanXExerciseId)
    {
        $trainingPlanXExerciseDb = new TrainingPlanXExercise();
        $trainingPlanXExerciseOptionDb = new TrainingPlanXExerciseOption();
        $trainingPlanXDeviceOptionDb = new TrainingPlanXDeviceOption();

        $trainingPlanXExerciseOptionDb->deleteTrainingPlanExerciseOptionsByTrainingPlanXExerciseId(
            $trainingPlanXExerciseId
        );
        $trainingPlanXDeviceOptionDb->deleteTrainingPlanDeviceOptionsByTrainingPlanXExerciseId(
            $trainingPlanXExerciseId
        );
        $trainingPlanXExerciseDb->delete('training_plan_x_exercise_id = ' . $trainingPlanXExerciseId);
    }

    /**
     * @param $currentTrainingPlanXExerciseCollection
     *
     * @return $this
     */
    private function removeWasteExercisesFromDatabase($currentTrainingPlanXExerciseCollection)
    {
        /**
         * delete all old exercises in DB
         */
        if (is_array($currentTrainingPlanXExerciseCollection)) {
            foreach ($currentTrainingPlanXExerciseCollection as $exerciseId => $currentTrainingPlanXExercise) {
                $currentTrainingPlanExerciseId =
                    $currentTrainingPlanXExercise['exercise']->offsetGet('training_plan_x_exercise_id');
                $this->deleteTrainingPlanExercise($currentTrainingPlanExerciseId);
            }
        }
        return $this;
    }

    /**
     * @param      $exercise
     * @param      $trainingPlanId
     * @param      $currentTrainingPlanXExerciseCollection
     * @param bool $resetExerciseOrderCount
     *
     * @return mixed
     */
    private function saveExercise(
        $exercise,
        $trainingPlanId,
        $currentTrainingPlanXExerciseCollection,
        $resetExerciseOrderCount = false
    ) {

        $trainingPlanXExerciseDb = new TrainingPlanXExercise();

        $trainingPlanXExerciseId = $exercise['trainingPlanExerciseId'];
        $exerciseId = $exercise['exerciseId'];

        // fallback wenn beim erstellen ohne reload nochmals gespeichert wird
        if (empty($trainingPlanXExerciseId)) {
            $trainingPlanExercise = $trainingPlanXExerciseDb->findExerciseByParentTrainingPlanIdAndExerciseId(
                $trainingPlanId,
                $exerciseId
            );
            if ($trainingPlanExercise instanceof Zend_Db_Table_Row_Abstract) {
                $trainingPlanXExerciseId = $trainingPlanExercise->offsetGet('training_plan_x_exercise_id');
            }
        }

        $exerciseRemark = base64_decode($exercise['exerciseRemark']);
        $exerciseShouldDeleted = (array_key_exists('deleted', $exercise) && $exercise['deleted']) ? true : false;
        static $exerciseCount = 0;

        if ($resetExerciseOrderCount) {
            $exerciseCount = 0;
        }
        $userId = $this->findCurrentUserId();

        // delete
        if (!empty($trainingPlanXExerciseId)
            && $exerciseShouldDeleted
        ) {
            $this->deleteTrainingPlanExercise($trainingPlanXExerciseId);
            // new trainingPlanXExercise
        } elseif (empty($trainingPlanXExerciseId)
            && !$exerciseShouldDeleted
        ) {
            $data = [
                'training_plan_x_exercise_exercise_fk' => $exerciseId,
                'training_plan_x_exercise_exercise_order' => $exerciseCount,
                'training_plan_x_exercise_training_plan_fk' => $trainingPlanId,
                'training_plan_x_exercise_create_date' => date('Y-m-d H:i:s'),
                'training_plan_x_exercise_create_user_fk' => $userId
            ];
            $trainingPlanXExerciseId = $trainingPlanXExerciseDb->saveTrainingPlanExercise($data);
            // check if must update
        } elseif (!$exerciseShouldDeleted) {
            $exerciseInDb = $currentTrainingPlanXExerciseCollection[$exerciseId]['exercise'];
            if ($exerciseCount != $exerciseInDb['training_plan_x_exercise_exercise_order']
                || $trainingPlanId != $exerciseInDb['training_plan_x_exercise_training_plan_fk']
                || $exerciseRemark != $exerciseInDb['training_plan_x_exercise_remark']
            ) {
                $data = [
                    'training_plan_x_exercise_exercise_order' => $exerciseCount,
                    'training_plan_x_exercise_training_plan_fk' => $trainingPlanId,
                    'training_plan_x_exercise_remark' => $exerciseRemark,
                    'training_plan_x_exercise_update_date' => date('Y-m-d H:i:s'),
                    'training_plan_x_exercise_update_user_fk' => $userId
                ];
                $trainingPlanXExerciseDb->updateTrainingPlanExercise($data, $trainingPlanXExerciseId);
            }
        }

        if ($exerciseId
            && !$exerciseShouldDeleted
            && isset($exercise['exerciseOptions'])
            && is_array($exercise['exerciseOptions'])
        ) {
            $currentTrainingPlanXExerciseCollection = $this->processExerciseOptions(
                $exercise['exerciseOptions'],
                $exerciseId,
                $trainingPlanXExerciseId,
                $currentTrainingPlanXExerciseCollection
            );
        }

        if ($exerciseId
            && !$exerciseShouldDeleted
            && isset($exercise['deviceOptions'])
            && is_array($exercise['deviceOptions'])
        ) {
            $currentTrainingPlanXExerciseCollection = $this->processDeviceOptions(
                $exercise['deviceOptions'],
                $exerciseId,
                $trainingPlanXExerciseId,
                $currentTrainingPlanXExerciseCollection
            );
        }
        unset($currentTrainingPlanXExerciseCollection[$exercise['exerciseId']]);
        ++$exerciseCount;

        return $currentTrainingPlanXExerciseCollection;
    }

    /**
     * @param $exerciseOptions
     * @param $exerciseId
     * @param $trainingPlanXExerciseId
     * @param $currentTrainingPlanXExerciseCollection
     *
     * @return mixed
     */
    private function processExerciseOptions(
        $exerciseOptions,
        $exerciseId,
        $trainingPlanXExerciseId,
        $currentTrainingPlanXExerciseCollection
    ) {

        $userId = $this->findCurrentUserId();
        $trainingPlanXExerciseOptionDb = new TrainingPlanXExerciseOption();

        foreach ($exerciseOptions as $exerciseOption) {
            $exerciseOptionId = $exerciseOption['exerciseOptionId'];

            // wenn exerciseOption bereits in der DB
            if (array_key_exists('exerciseOptions', $currentTrainingPlanXExerciseCollection[$exerciseId])
                && array_key_exists(
                    $exerciseOptionId,
                    $currentTrainingPlanXExerciseCollection[$exerciseId]['exerciseOptions']
                )
            ) {
                $trainingPlanExerciseOptionInDb =
                    $currentTrainingPlanXExerciseCollection[$exerciseId]['exerciseOptions'][$exerciseOptionId];
                // wenn value der übergabe leer ist => löschen
                if ((empty($exerciseOption['exerciseOptionValue'])
                    || -1 == $exerciseOption['exerciseOptionValue'])
                    && isset($exerciseOption['trainingPlanXExerciseOptionId'])
                ) {
                    $trainingPlanXExerciseOptionDb->deleteTrainingPlanExerciseOption(
                        $exerciseOption['trainingPlanXExerciseOptionId']
                    );
                } elseif ($exerciseOption['exerciseOptionValue'] !=
                    $trainingPlanExerciseOptionInDb['training_plan_x_exercise_option_exercise_option_value']
                ) {
                    $data = [
                        'training_plan_x_exercise_option_exercise_option_value' =>
                            $exerciseOption['exerciseOptionValue'],
                        'training_plan_x_exercise_option_update_date' => date('Y-m-d H:i:s'),
                        'training_plan_x_exercise_option_update_user_fk' => $userId,
                    ];
                    $trainingPlanXExerciseOptionDb->updateTrainingPlanExerciseOption(
                        $data,
                        $exerciseOption['trainingPlanXExerciseOptionId']
                    );
                }
            } elseif (! empty($exerciseOption['exerciseOptionValue'])) {
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

    /**
     * @param $deviceOptions
     * @param $exerciseId
     * @param $trainingPlanXExerciseId
     * @param $currentTrainingPlanXExerciseCollection
     *
     * @return mixed
     */
    private function processDeviceOptions(
        $deviceOptions,
        $exerciseId,
        $trainingPlanXExerciseId,
        $currentTrainingPlanXExerciseCollection
    ) {

        $userId = $this->findCurrentUserId();
        $trainingPlanXDeviceOptionDb = new TrainingPlanXDeviceOption();

        foreach ($deviceOptions as $deviceOption) {
            $deviceOptionId = $deviceOption['deviceOptionId'];

            // wenn deviceOption bereits in der DB
            if (array_key_exists('deviceOptions', $currentTrainingPlanXExerciseCollection[$exerciseId])
                && array_key_exists(
                    $deviceOptionId,
                    $currentTrainingPlanXExerciseCollection[$exerciseId]['deviceOptions']
                )
            ) {
                $trainingPlanDeviceOptionInDb =
                    $currentTrainingPlanXExerciseCollection[$exerciseId]['deviceOptions'][$deviceOptionId];
                // wenn value der übergabe leer ist => löschen
                if ((empty($deviceOption['deviceOptionValue'])
                    || -1 == $deviceOption['deviceOptionValue'])
                    && 0 < $deviceOption['trainingPlanXDeviceOptionId']
                ) {
                    $trainingPlanXDeviceOptionDb->deleteTrainingPlanDeviceOption(
                        $deviceOption['trainingPlanXDeviceOptionId']
                    );
                } elseif ($deviceOption['deviceOptionValue'] !=
                    $trainingPlanDeviceOptionInDb['training_plan_x_device_option_device_option_value']
                ) {
                    $data = [
                        'training_plan_x_device_option_device_option_value' => $deviceOption['deviceOptionValue'],
                        'training_plan_x_device_option_update_date' => date('Y-m-d H:i:s'),
                        'training_plan_x_device_option_update_user_fk' => $userId,
                    ];
                    $trainingPlanXDeviceOptionDb->updateTrainingPlanDeviceOption(
                        $data,
                        $deviceOption['trainingPlanXDeviceOptionId']
                    );
                }
            } elseif (! empty($deviceOption['deviceOptionValue'])) {
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

    /**
     * @param $trainingPlanId
     *
     * @return array
     */
    private function collectCurrentExercisesByTrainingPlan($trainingPlanId)
    {

        $trainingPlanXExerciseDb = new TrainingPlanXExercise();
        $trainingPlanXExerciseOptionDb = new TrainingPlanXExerciseOption();
        $trainingPlanXDeviceOptionDb = new TrainingPlanXDeviceOption();

        $currentTrainingPlanXExercisesInDb = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlanId);
        $currentTrainingPlanXExerciseCollection = [];

        /**
         * iterate all trainingPlanXExercises in DB
         */
        foreach ($currentTrainingPlanXExercisesInDb as $currentTrainingPlanExercise) {
            $currentTrainingPlanExerciseId = $currentTrainingPlanExercise->offsetGet('training_plan_x_exercise_id');
            $currentExerciseId = $currentTrainingPlanExercise->offsetGet('exercise_id');
            $currentTrainingPlanXExerciseCollection[$currentExerciseId] = [];
            $currentTrainingPlanXExerciseCollection[$currentExerciseId]['exercise'] = $currentTrainingPlanExercise;
            $currentExerciseOptionCollection =
                $trainingPlanXExerciseOptionDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId(
                    $currentTrainingPlanExerciseId
                );

            /**
             * iterate all trainingPlanExerciseOptions in Db
             */
            foreach ($currentExerciseOptionCollection as $currentExerciseOption) {
                $currentExerciseOptionId = $currentExerciseOption->offsetGet(
                    'training_plan_x_exercise_option_exercise_option_fk'
                );
                $currentTrainingPlanXExerciseCollection[$currentExerciseId]
                    ['exerciseOptions'][$currentExerciseOptionId] = $currentExerciseOption;
            }

            $currentDeviceOptionCollection =
                $trainingPlanXDeviceOptionDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId(
                    $currentTrainingPlanExerciseId
                );
            /**
             * iterate all trainingPlanExerciseOptions in Db
             */
            foreach ($currentDeviceOptionCollection as $currentDeviceOption) {
                $currentDeviceOptionId = $currentDeviceOption->offsetGet(
                    'training_plan_x_device_option_device_option_fk'
                );
                $currentTrainingPlanXExerciseCollection[$currentExerciseId]['deviceOptions']
                    [$currentDeviceOptionId] = $currentDeviceOption;
            }
        }

        return $currentTrainingPlanXExerciseCollection;
    }

    /**
     * @param $iUserId
     *
     * @return bool|mixed
     */
    public function createBaseTrainingPlan($iUserId)
    {
        $oTrainingsplanLayouts = new TrainingPlanLayouts();
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
    private function deactivateAllCurrentTrainingPlans($userId)
    {
        $trainingPlansDb = new TrainingPlans();
        $trainingPlansDb->update(['training_plan_active' => 0], 'training_plan_user_fk = "' . $userId . '"');
    }

    /**
     * @param $iUserId
     *
     * @return bool|mixed
     */
    public function createSplitTrainingPlan($iUserId)
    {
        $oTrainingsplanLayouts = new TrainingPlanLayouts();
        $oTrainingsplanLayout = $oTrainingsplanLayouts->findTrainingPlanLayoutByName('Split');
        $iTrainingsplanLayoutId = $oTrainingsplanLayout->training_plan_layout_id;

        if (is_numeric($iTrainingsplanLayoutId)
            && 0 < $iTrainingsplanLayoutId
        ) {
            $this->deactivateAllCurrentTrainingPlans($iUserId);

            $aData = array(
                'training_plan_training_plan_layout_fk' => $iTrainingsplanLayoutId,
                'training_plan_active' => 1,
                'training_plan_user_fk' => $iUserId,
                'training_plan_order' => 1,
                'training_plan_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_create_date' => date('Y-m-d H:i:s')
            );
            $iTrainingsplanParentId = $this->createTrainingPlan($aData);

            if (0 < $iTrainingsplanParentId) {
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

    /**
     * @param $aData
     *
     * @return mixed
     */
    public function createTrainingPlan($aData)
    {
        $trainingPlansDb = new TrainingPlans();

        $trainingPlanId = $trainingPlansDb->insert($aData);

        return $trainingPlanId;
    }

    /**
     * search current active training plan
     */
    public function searchCurrentTrainingPlan($userId)
    {
        $trainingPlansDb = new TrainingPlans();
        $trainingDiaryXTrainingPlanDb = new TrainingDiaryXTrainingPlan();
        $currentOpenTrainingDiary = $trainingDiaryXTrainingPlanDb->findLastOpenTrainingPlanByUserId($userId);

        /**
         * current open training diary entry found!
         */
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

        // no training diary entry found for the current active training plan,
        // get first entry of current active training plan
        return $trainingPlansDb->findActiveTrainingPlanByUserId($userId);
    }

    /**
     * @return bool
     */
    protected function findCurrentUserId()
    {
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            return $user->user_id;
        }
        return false;
    }

    /**
     * create copy from given trainingPlanId for given User
     *
     * @param $trainingPlanId
     * @param $userId
     *
     * @return int
     */
    public function createTrainingPlanFromTemplate($trainingPlanId, $userId)
    {
        $trainingPlansDb = new TrainingPlans();
        $trainingPlanCollection = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($trainingPlanId);
        $primaryTrainingPlanId = null;

        $this->deactivateAllCurrentTrainingPlans($userId);

        foreach ($trainingPlanCollection as $trainingPlan) {
            $trainingPlanName = $trainingPlan->offsetGet('training_plan_name');

            if (empty($trainingPlan->offsetGet('training_plan_parent_fk'))) {
                $trainingPlanName = 'Copy of ' . $trainingPlanName;
            }
            $data = [
                'training_plan_name' => $trainingPlanName,
                'training_plan_training_plan_layout_fk' => $trainingPlan->offsetGet(
                    'training_plan_training_plan_layout_fk'
                ),
                'training_plan_order' => $trainingPlan->offsetGet('training_plan_order'),
                'training_plan_active' => 1,
                'training_plan_user_fk' => $userId,
                'training_plan_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_create_date' => date('Y-m-d H:i:s')
            ];

            if (!empty($trainingPlan->offsetGet('training_plan_parent_fk'))) {
                $data['training_plan_parent_fk'] = $primaryTrainingPlanId;
            }
            $currentTrainingPlanId = $trainingPlansDb->insert($data);
            $this->copyExercisesFromTrainingPlan($trainingPlan->offsetGet('training_plan_id'), $currentTrainingPlanId);
            if (empty($trainingPlan->offsetGet('training_plan_parent_fk'))) {
                $primaryTrainingPlanId = $currentTrainingPlanId;
            }
        }
        return $primaryTrainingPlanId;
    }

    /**
     * @param $origTrainingPlanId
     * @param $newTrainingPlanId
     */
    private function copyExercisesFromTrainingPlan($origTrainingPlanId, $newTrainingPlanId)
    {
        $trainingPlanXExerciseDb = new TrainingPlanXExercise();
        $trainingPlanExerciseCollection = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($origTrainingPlanId);

        foreach ($trainingPlanExerciseCollection as $trainingPlanExercise) {
            $data = [
                'training_plan_x_exercise_exercise_fk' => $trainingPlanExercise->offsetGet(
                    'training_plan_x_exercise_exercise_fk'
                ),
                'training_plan_x_exercise_training_plan_fk' => $newTrainingPlanId,
                'training_plan_x_exercise_remark' => $trainingPlanExercise->offsetGet(
                    'training_plan_x_exercise_remark'
                ),
                'training_plan_x_exercise_exercise_order' => $trainingPlanExercise->offsetGet(
                    'training_plan_x_exercise_exercise_order'
                ),
                'training_plan_x_exercise_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_x_exercise_create_date' => date('Y-m-d H:i:s'),

            ];
            $currentTrainingPlanExerciseId = $trainingPlanXExerciseDb->saveTrainingPlanExercise($data);
            $this->copyExerciseOptionsFromTrainingPlanExercise($trainingPlanExercise->offsetGet(
                'training_plan_x_exercise_id'
            ), $currentTrainingPlanExerciseId);
            $this->copyDeviceOptionsFromTrainingPlanExercise($trainingPlanExercise->offsetGet(
                'training_plan_x_exercise_id'
            ), $currentTrainingPlanExerciseId);
        }
    }

    /**
     * @param $origTrainingPlanExerciseId
     * @param $newTrainingPlanExerciseId
     */
    private function copyExerciseOptionsFromTrainingPlanExercise(
        $origTrainingPlanExerciseId,
        $newTrainingPlanExerciseId
    ) {
        $trainingPlanXExerciseOptionsDb = new TrainingPlanXExerciseOption();
        $trainingPlanXExerciseOptionCollection =
            $trainingPlanXExerciseOptionsDb->findTrainingPlanExerciseOptionsByTrainingPlanExerciseId(
                $origTrainingPlanExerciseId
            );

        foreach ($trainingPlanXExerciseOptionCollection as $trainingPlanXExerciseOption) {
            $data = [
                'training_plan_x_exercise_option_training_plan_exercise_fk' => $newTrainingPlanExerciseId,
                'training_plan_x_exercise_option_exercise_option_value' =>
                    $trainingPlanXExerciseOption->offsetGet('training_plan_x_exercise_option_exercise_option_value'),
                'training_plan_x_exercise_option_exercise_option_fk' =>
                    $trainingPlanXExerciseOption->offsetGet('training_plan_x_exercise_option_exercise_option_fk'),
                'training_plan_x_exercise_option_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_x_exercise_option_create_date' => date('Y-m-d H:i:s')
            ];
            $currentTrainingPlanXExerciseOptionId =
                $trainingPlanXExerciseOptionsDb->saveTrainingPlanExerciseOption($data);
        }
    }

    /**
     * @param $origTrainingPlanExerciseId
     * @param $newTrainingPlanExerciseId
     */
    private function copyDeviceOptionsFromTrainingPlanExercise($origTrainingPlanExerciseId, $newTrainingPlanExerciseId)
    {
        $trainingPlanXDeviceOptionsDb = new TrainingPlanXDeviceOption();
        $trainingPlanXDeviceOptionCollection =
            $trainingPlanXDeviceOptionsDb->findTrainingPlanDeviceOptionsByTrainingPlanExerciseId(
                $origTrainingPlanExerciseId
            );

        foreach ($trainingPlanXDeviceOptionCollection as $trainingPlanXDeviceOption) {
            $data = [
                'training_plan_x_device_option_training_plan_exercise_fk' => $newTrainingPlanExerciseId,
                'training_plan_x_device_option_device_option_value' => $trainingPlanXDeviceOption->offsetGet(
                    'training_plan_x_device_option_device_option_value'
                ),
                'training_plan_x_device_option_device_option_fk' => $trainingPlanXDeviceOption->offsetGet(
                    'training_plan_x_device_option_device_option_fk'
                ),
                'training_plan_x_device_option_create_user_fk' => $this->findCurrentUserId(),
                'training_plan_x_device_option_create_date' => date('Y-m-d H:i:s')
            ];
            $currentTrainingPlanXDeviceOptionId = $trainingPlanXDeviceOptionsDb->saveTrainingPlanDeviceOption($data);
        }
    }
}

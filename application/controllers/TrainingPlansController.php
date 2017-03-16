<?php

class TrainingPlansController extends Zend_Controller_Action {

    private $usedMuscles = [];
    private $muscleUsageMin = 0;
    private $muscleUsageMax = 0;

    public function __init() {
    }

    public function postDispatch() {
        $a_params = $this->getRequest()->getParams();

        if (isset($a_params['ajax'])) {
            $this->view->layout()->disableLayout();
        }
    }

    /**
     * wenn admin und höher, liste aller aktiven trainingspläne
     * wenn nur user, seinen aktiven training-plans
     */
    public function indexAction() {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingPlansCollection = $trainingPlansDb->findAllActiveTrainingPlans();

        if (false !== $trainingPlansCollection) {
            $sContent = '';
            foreach ($trainingPlansCollection as $trainingPlan) {
                $trainingPlanName = $trainingPlan->user_first_name . ' - ' . date('d.m.Y',
                        strtotime($trainingPlan->training_plan_create_date));
                if (false === empty($trainingPlan->training_plan_name)) {
                    $trainingPlanName = $trainingPlan->training_plan_name;
                }

                $sContent .= '<a href="/training-plans/show/id/' . $trainingPlan->training_plan_id . '">' .
                    $trainingPlanName . '</a><br />';
            }
            $this->view->assign('sContent', $sContent);
        }
    }

    /**
     * wenn admin und höher, liste aller aktiven trainingspläne
     * wenn nur user, seinen aktiven training-plans
     */
    public function archiveAction() {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingPlansCollection = $trainingPlansDb->findAllActiveTrainingPlans();

        if (false !== $trainingPlansCollection) {
            $sContent = '';
            foreach ($trainingPlansCollection as $trainingPlan) {
                $trainingPlanName = $trainingPlan->user_first_name . ' - ' . date('d.m.Y',
                        strtotime($trainingPlan->training_plan_create_date));
                if (false === empty($trainingPlan->training_plan_name)) {
                    $trainingPlanName = $trainingPlan->training_plan_name;
                }

                $sContent .= '<a href="/training-plans/show/id/' . $trainingPlan->training_plan_id . '">' .
                    $trainingPlanName . '</a><br />';
            }
            $this->view->assign('sContent', $sContent);
        }
    }

    /**
     *
     */
    public function showAction() {
        $aParams = $this->getAllParams();

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $trainingPlanId = $aParams['id'];
            $trainingPlansDb = new Model_DbTable_TrainingPlans();
            $trainingPlan = $trainingPlansDb->findTrainingPlan($trainingPlanId);

            $this->view->assign('trainingPlanContent',
                $this->generateTrainingPlanContent($trainingPlanId,
                    $trainingPlan->offsetGet('training_plan_training_plan_layout_fk')));

            $this->view->assign('training_plan_id',$trainingPlanId);
        } else {
            $this->view->assign('trainingPlanContent', 'Trainingsplan konnte nicht gefunden werden!');
        }
    }

    public function getTrainingPlanForSplitAction() {
        $aParams = $this->getAllParams();

        $user = Zend_Auth::getInstance()->getIdentity();

        if (true === is_object($user)) {
            $userId = $user->user_id;

            if (is_numeric($userId)
                && 0 < $userId
                && true === array_key_exists('user_id', $aParams)
                && true === is_numeric($aParams['user_id'])
                && 0 < $aParams['user_id']
                && true === array_key_exists('training_plan_id', $aParams)
                && true === is_numeric($aParams['training_plan_id'])
                && 0 < $aParams['training_plan_id']
            ) {
                $aData = array(
                    'training_plan_training_plan_layout_fk' => 1,
                    'training_plan_parent_fk' => $aParams['training_plan_id'],
                    'training_plan_user_fk' => $aParams['user_id']
                );

                $sContent = '';

                try {
                    $trainingPlanId = $this->createTrainingPlan($aData);
                    $this->view->assign('trainingPlanId', $trainingPlanId);
                    $this->view->assign('training_plan_id', $trainingPlanId);
                    $sContent = $this->view->render('loops/training-plan-edit.phtml');
                } catch (Exception $oException) {
                    $sContent = $oException->getMessage();
                }
                $this->view->assign('sContent', $sContent);
            } else {
                $sContent = json_encode(array(
                    array(
                        'type' => 'fehler', 'message' => 'Konnte Trainingsplan nicht anlegen!'
                    )
                ));
                $this->view->assign('sContent', $sContent);
            }
        } else {
            $sContent = json_encode(array(
                array(
                    'type' => 'fehler', 'message' => 'Für diese Aktion müssen Sie angemeldet sein!'
                )
            ));
            $this->view->assign('sContent', $sContent);
        }
    }

    /**
     *
     */
    public function getExerciseAction() {
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)) {
            $exerciseId = $aParams['id'];
            $count = $aParams['counter'];
            $exercisesDb = new Model_DbTable_Exercises();
            $exercise = $exercisesDb->findExerciseById($exerciseId);

            $deviceOptionsService = new Service_View_Generator_DeviceOptions($this->view);
            $deviceOptionsService->setExerciseId($exerciseId);
            $deviceOptionsService->setOptionId($exercise['device_option_id']);
            $deviceOptionsService->setDeviceId($exercise['device_id']);
            $deviceOptionsService->setForceGenerateEmptyInput(true);
            $deviceOptionsService->setShowDelete(true);
            $this->view->assign('deviceOptionContent', $deviceOptionsService->generate());

            $exerciseOptionsService = new Service_View_Generator_ExerciseOptions($this->view);
            $exerciseOptionsService->setExerciseId($exerciseId);
            $exerciseOptionsService->setOptionId($exercise['exercise_option_id']);
            $exerciseOptionsService->setForceGenerateEmptyInput(true);
            $exerciseOptionsService->setShowDelete(true);

            $this->view->assign('exerciseOptionContent', $exerciseOptionsService->generate());

            $deviceOptionsService->setShowDelete(false);
            $this->view->assign('deviceOptionsSelectContent', $deviceOptionsService->generateDeviceOptionsSelectContent());
            $exerciseOptionsService->setShowDelete(false);
            $this->view->assign('exerciseOptionsSelectContent', $exerciseOptionsService->generateExerciseOptionsSelectContent());

            $this->view->assign('count', $count);
            $this->view->assign($exercise->toArray());

            $this->view->assign('exerciseContent', $this->view->render('loops/training-plan-exercise-edit.phtml'));
        }
    }

    /**
     *
     */
    public function getDeviceOptionAction() {
        $deviceOptionId = intval($this->getParam('deviceOptionId'));
        $exerciseId = intval($this->getParam('exerciseId'));
        $deviceId = intval($this->getParam('deviceId'));
        $trainingPlanExerciseId = intval($this->getParam('trainingPlanExerciseId'));

        $deviceOptionsService = new Service_View_Generator_DeviceOptions($this->view);
        $deviceOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
        $deviceOptionsService->setExerciseId($exerciseId);
        $deviceOptionsService->setOptionId($deviceOptionId);
        $deviceOptionsService->setDeviceId($deviceId);
        $deviceOptionsService->setForceGenerateEmptyInput(true);
        $deviceOptionsService->setShowDelete(true);

        $this->view->assign('deviceOptionContent', $deviceOptionsService->generate());
    }

    /**
     *
     */
    public function getExerciseOptionAction() {
        $exerciseOptionId = intval($this->getParam('exerciseOptionId'));
        $exerciseId = intval($this->getParam('exerciseId'));
        $trainingPlanExerciseId = intval($this->getParam('trainingPlanExerciseId'));

        $exerciseOptionsService = new Service_View_Generator_ExerciseOptions($this->view);
        $exerciseOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
        $exerciseOptionsService->setExerciseId($exerciseId);
        $exerciseOptionsService->setOptionId($exerciseOptionId);
        $exerciseOptionsService->setForceGenerateEmptyInput(true);
        $exerciseOptionsService->setShowDelete(true);

        $this->view->assign('exerciseOptionContent', $exerciseOptionsService->generate());
    }

    /**
     *
     */
    public function getExerciseProposalsAction() {

        $aParams = $this->getAllParams();
        $proposalContent = 'Es wurden leider keine Übungen gefunden!';

        if (array_key_exists('search', $aParams)) {
            $search = base64_decode($aParams['search']);
            $exercisesDb = new Model_DbTable_Exercises();
            $exercisesCollection = $exercisesDb->findExercisesByName('%' . $search . '%');
            $proposalContent = '';
            if ($exercisesCollection instanceof Zend_Db_Table_Rowset) {
                foreach ($exercisesCollection as $exercise) {
                    $this->view->assign('proposalId', $exercise->offsetGet('exercise_id'));
                    $this->view->assign('proposalText', $exercise->offsetGet('exercise_name'));
                    $proposalContent .= $this->view->render('globals/proposal-row.phtml');
                }
            }
        }
        $this->view->assign('proposalContent', $proposalContent);

        $this->view->assign('exerciseProposalsContent', $this->view->render('globals/proposals.phtml'));
    }

    /**
     *
     */
    public function editAction() {
        $params = $this->getAllParams();

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (array_key_exists('id', $params)
            && is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $user = Zend_Auth::getInstance()->getIdentity();
            $userId = null;
            if (true === is_object($user)) {
                $userId = $user->user_id;
            }
            $trainingPlanId = $params['id'];
            $exerciseOptionsService = new Service_View_Generator_ExerciseOptions($this->view);
            $deviceOptionsService = new Service_View_Generator_DeviceOptions($this->view);
            $this->view->assign('deviceOptionsSelectContent', $deviceOptionsService->generateDeviceOptionsSelectContent());
            $this->view->assign('exerciseOptionsSelectContent', $exerciseOptionsService->generateExerciseOptionsSelectContent());
            $this->view->assign('trainingPlanContent', $this->generateTrainingPlanForEditContent($trainingPlanId));
            $this->view->assign('trainingPlanId', $trainingPlanId);
            $this->view->assign('userId', $userId);
        } else {
        }
    }

    public function saveAction() {

        if (null !== ($trainingPlanCollection = $this->getParam('trainingPlanCollection', null))) {
            $this->saveTrainingPlan($trainingPlanCollection);
        }
    }

    /**
     * wenn trainingPlanCollection keine exercises enthält und eine trainingPlanId => parent eines splits
     * wenn trainingPlanCollection exercises enthält und eine trainingPlanParentId übergeben wurde => children eines
     * splits wenn trainingPlanCollection exercises enthält und keinen TrainingPlanParentId => einzelner trianingsplan
     * ohne split
     *
     * @param      $trainingPlanCollection
     * @param null $trainingPlantParentId
     */
    private function saveTrainingPlan($trainingPlan, $trainingPlantParentId = null) {
        $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
        $trainingPlanXExerciseOptionDb = new Model_DbTable_TrainingPlanXExerciseOption();
        $trainingPlanXDeviceOptionDb = new Model_DbTable_TrainingPlanXDeviceOption();
        $trainingPlanId = intval($trainingPlan['trainingPlanId']);

        $userId = 1;

        /** new TrainingPlan */
        if (empty($trainingPlanId)) {
            $trainingPlanDb = new Model_DbTable_TrainingPlans();
            $data = [
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
                        $this->saveTrainingPlan($currentTrainingPlan, $trainingPlanId);
                    }
                }
            }
        } else {
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

            $exerciseCount = 0;

            foreach ($trainingPlan['exercises'] as $exercise) {
                $trainingPlanXExerciseId = $exercise['trainingPlanExerciseId'];
                $exerciseId = $exercise['exerciseId'];

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
                    ) {
                        $data = [
                            'training_plan_x_exercise_exercise_order' => $exerciseCount,
                            'training_plan_x_exercise_training_plan_fk' => $trainingPlanId,
                            'training_plan_x_exercise_update_date' => date('Y-m-d H:i:s'),
                            'training_plan_x_exercise_update_user_fk' => $userId
                        ];
                        $trainingPlanXExerciseDb->updateTrainingPlanExercise($data, $trainingPlanXExerciseId);
                    }
                }

                if (isset($exercise['exerciseOptions'])
                    && is_array($exercise['exerciseOptions'])
                ) {
                    foreach ($exercise['exerciseOptions'] as $exerciseOption) {
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
                }

                if (isset($exercise['deviceOptions'])
                    && is_array($exercise['deviceOptions'])
                ) {
                    foreach ($exercise['deviceOptions'] as $deviceOption) {
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
                }
                unset($currentTrainingPlanXExerciseCollection[$exercise['exerciseId']]);
                ++$exerciseCount;
            }

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
        }
    }

    /**
     * zum auswählen des grundlayouts des training_planes
     *  - normal
     *  - split
     */
    public function selectLayoutAction() {
        $oUsersStorage = new Model_DbTable_Users();
        $oUsers = $oUsersStorage->findActiveUsers();

        if (false !== $oUsers) {
            $this->view->assign('aUsers', $oUsers->toArray());
        }

        if ($this->getRequest()->isPost()) {
            try {
                $iTrainingsplanId = $this->createLayoutAction();
                if (is_numeric($iTrainingsplanId)
                    && 0 < $iTrainingsplanId
                ) {
                    $this->redirect('/training-plans/edit/id/' . $iTrainingsplanId);
                } else {
                    echo '<br /><br />' . "Konnte aktuellen Trainingsplan nicht anlegen!";
                }
            } catch (Exception $oException) {
                echo '<br /><br />' . $oException->getMessage();
            }
        }
    }

    public function createLayoutAction() {
        $aParams = $this->getAllParams();
        $iTrainingsplanId = null;

        if (true === array_key_exists('layout', $aParams)
            && true === array_key_exists('user_id', $aParams)
        ) {
            $iUserId = $aParams['user_id'];
            switch ($aParams['layout']) {
                case 1:
                    $iTrainingsplanId = $this->createBaseTrainingPlan($iUserId);
                    break;
                case 2:
                    $iTrainingsplanId = $this->createSplitTrainingPlan($iUserId);
                    break;
                default:
            }
        } else {
            echo "Funktion falsch aufgerufen!<br />";
        }

        return $iTrainingsplanId;
    }

    /**
     * @param int $trainingPlanId
     * @param int $trainingPlanLayoutId
     *
     * @return string
     */
    private function generateTrainingPlanContent($trainingPlanId, $trainingPlanLayoutId) {
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
        $this->view->assign('trainingPlanId', $trainingPlanId);
        $this->view->assign('musclesForExerciseContent', $this->generateUsedMusclesForTrainingPlanContent());
        $this->view->assign('exercisesContent', $exercisesContent);

        return $this->view->render('loops/training-plan.phtml');
    }

    /**
     * @param $trainingPlanExercise
     *
     * @return string
     */
    private function generateExerciseForTrainingPlanContent($trainingPlanExercise) {

        $deviceOptionsService = new Service_View_Generator_DeviceOptions($this->view);
        $deviceOptionsService->setTrainingPlanXExerciseId(intval($trainingPlanExercise->offsetGet('training_plan_x_exercise_id')));
        $deviceOptionsService->setExerciseId(intval($trainingPlanExercise->offsetGet('exercise_id')));
        $deviceOptionsService->setOptionId(intval($trainingPlanExercise->offsetGet('device_option_id')));
        $this->view->assign('deviceOptionsContent', $deviceOptionsService->generate($trainingPlanExercise));

        $exerciseOptionsService = new Service_View_Generator_ExerciseOptions($this->view);
        $exerciseOptionsService->setTrainingPlanXExerciseId(intval($trainingPlanExercise->offsetGet('training_plan_x_exercise_id')));
        $exerciseOptionsService->setExerciseId(intval($trainingPlanExercise->offsetGet('exercise_id')));
        $exerciseOptionsService->setOptionId(intval($trainingPlanExercise->offsetGet('exercise_option_id')));
        $this->view->assign('exerciseOptionsContent', $exerciseOptionsService->generate($trainingPlanExercise));

        $this->collectMusclesForExerciseContent($trainingPlanExercise);
        $this->view->assign($trainingPlanExercise->toArray());

        return $this->view->render('loops/training-plan-exercise.phtml');
    }

    private function generateTrainingPlanExerciseForEditContent($trainingPlanId) {
        $trainingPlanXExercisesDb = new Model_DbTable_TrainingPlanXExercise();

        $trainingPlanXExerciseCollection =
            $trainingPlanXExercisesDb->findExercisesByTrainingPlanId($trainingPlanId);

        $exercisesContent = '';

        foreach ($trainingPlanXExerciseCollection as $trainingPlanExercise) {
            $this->view->assign($trainingPlanExercise->toArray());

            $exerciseId = $trainingPlanExercise->offsetGet('exercise_id');
            $trainingPlanExerciseId = $trainingPlanExercise->offsetGet('training_plan_x_exercise_id');
            $exerciseOptionsService = new Service_View_Generator_ExerciseOptions($this->view);
            $exerciseOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
            $exerciseOptionsService->setExerciseId($exerciseId);
            $exerciseOptionsService->setShowDelete(true);

            $this->view->assign('exerciseOptionsContent', $exerciseOptionsService->generate());

            $deviceOptionsService = new Service_View_Generator_DeviceOptions($this->view);
            $deviceOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
            $deviceOptionsService->setExerciseId($exerciseId);
            $deviceOptionsService->setShowDelete(true);

            $this->view->assign('deviceOptionsContent', $deviceOptionsService->generate());

            $exercisesContent .= $this->view->render('loops/training-plan-exercise-edit.phtml');
        }

        return $exercisesContent;
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
            $this->view->assign('muscleName', $muscleName);
            $this->view->assign('usagePosX', $usagePosX);
            $this->view->assign('muscleUse', $muscleUse);
            $this->view->assign('muscleUsePercentage', number_format(($muscleUse / $this->muscleUsageMax) * 100, 2));
            $muscleUseContent .= $this->view->render('loops/muscles-for-exercise.phtml');
        }

        return $muscleUseContent;
    }

    private function generateTrainingPlanForEditContent($trainingPlanId) {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingPlanCollection = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($trainingPlanId);
        $trainingPlanContent = '';

        foreach ($trainingPlanCollection as $trainingPlan) {
            // es ist nur ein trainingsplan, oder es gibt mehrere, dann den parent nicht rendern
            if (($trainingPlan->offsetGet('training_plan_parent_fk')
                    && 0 < count($trainingPlanCollection))
                || (! $trainingPlan->offsetGet('training_plan_parent_fk')
                    && 1 == count($trainingPlanCollection))
            ) {
                $this->view->assign('exercisesContent',
                    $this->generateTrainingPlanExerciseForEditContent($trainingPlan->offsetGet('training_plan_id')));
                $this->view->assign('trainingPlanId', $trainingPlan->offsetGet('training_plan_id'));
                $trainingPlanContent .= $this->view->render('loops/training-plan-edit.phtml');
            }
        }

        return $trainingPlanContent;
    }

    private function createBaseTrainingPlan($iUserId) {
        $oTrainingsplanLayouts = new Model_DbTable_TrainingPlanLayouts();
        $oTrainingsplanLayout = $oTrainingsplanLayouts->findTrainingPlanLayoutByName('Normal');
        $iTrainingsplanLayoutId = $oTrainingsplanLayout->training_plan_layout_id;

        if (is_numeric($iTrainingsplanLayoutId)
            && 0 < $iTrainingsplanLayoutId
        ) {
            $aData = array(
                'training_plan_training_plan_layout_fk' => $iTrainingsplanLayoutId,
                'training_plan_create_user_fk' => $iUserId,
                'training_plan_create_date' => date('Y-m-d H:i:s'),
            );
            $iTrainingsplanId = $this->createTrainingPlan($aData);

            return $iTrainingsplanId;
        } else {
            echo "Es konnte kein Layout für Normale Trainingspläne gefunden werden!";

            return false;
        }
    }

    private function createSplitTrainingPlan($iUserId) {
        $oTrainingsplanLayouts = new Model_DbTable_TrainingPlanLayouts();
        $oTrainingsplanLayout = $oTrainingsplanLayouts->findTrainingPlanLayoutByName('Split');
        $iTrainingsplanLayoutId = $oTrainingsplanLayout->training_plan_layout_id;

        if (is_numeric($iTrainingsplanLayoutId)
            && 0 < $iTrainingsplanLayoutId
        ) {
            $aData = array(
                'training_plan_training_plan_layout_fk' => $iTrainingsplanLayoutId,
                'training_plan_create_user_fk' => $iUserId,
                'training_plan_create_date' => date('Y-m-d H:i:s'),
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
                    'training_plan_user_fk' => $iUserId
                );
                $iTrainingsplanId = $this->createTrainingPlan($aData);
            }

            return $iTrainingsplanParentId;
        } else {
            echo "Es konnte kein Layout für Split Trainingspläne gefunden werden!";

            return false;
        }
    }

    private function createTrainingPlan($aData) {
        $oUser = Zend_Auth::getInstance()->getIdentity();
        $oTrainingsplaene = new Model_DbTable_TrainingPlans();

        $iUserId = $oUser->user_id;

        if (is_numeric($iUserId)
            && 0 < $iUserId
        ) {
            $aData['training_plan_create_date'] = date('Y-m-d H:i:s');
            $aData['training_plan_create_user_fk'] = $iUserId;

            $iTrainingsplanId = $oTrainingsplaene->insert($aData);

            return $iTrainingsplanId;
        } else {
            throw new Exception('Sie müssen angemeldet sein, um diese Aktion durchzuführen!');
        }
    }
}

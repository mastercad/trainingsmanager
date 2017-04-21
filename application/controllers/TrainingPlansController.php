<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class TrainingPlansController extends AbstractController {

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
            $trainingPlanService = new Service_Generator_View_TrainingPlan($this->view);

            $this->view->assign('trainingPlanContent',
                $trainingPlanService->generateTrainingPlanContent($trainingPlanId,
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
                    $trainingPlanService = new Service_TrainingPlan();
                    $trainingPlanId = $trainingPlanService->createTrainingPlan($aData);
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

            $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
            $deviceOptionsService->setExerciseId($exerciseId);
            $deviceOptionsService->setOptionId($exercise['device_option_id']);
            $deviceOptionsService->setDeviceId($exercise['device_id']);
            $deviceOptionsService->setForceGenerateEmptyInput(true);
            $deviceOptionsService->setShowDelete(true);
            $this->view->assign('deviceOptionContent', $deviceOptionsService->generate());

            $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
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

        $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
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

        $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
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
            $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
            $trainingPlanService = new Service_Generator_View_TrainingPlan($this->view);
            $this->view->assign('deviceOptionsSelectContent', $deviceOptionsService->generateDeviceOptionsSelectContent());
            $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
            $this->view->assign('exerciseOptionsSelectContent', $exerciseOptionsService->generateExerciseOptionsSelectContent());
            $this->view->assign('trainingPlanContent', $trainingPlanService->generateTrainingPlanForEditContent($trainingPlanId));
            $this->view->assign('trainingPlanId', $trainingPlanId);
            $this->view->assign('userId', $userId);
        } else {
        }
    }

    public function saveAction() {

        if (null !== ($trainingPlanCollection = $this->getParam('trainingPlanCollection', null))) {
            $trainingPlanService = new Service_TrainingPlan();
            $trainingPlanService->saveTrainingPlan($trainingPlanCollection);
        }
    }

    /**
     * zum auswählen des grundlayouts des training_planes
     *  - normal
     *  - split
     */
    public function selectLayoutAction() {

        $this->view->assign('userSelect', $this->generateUserSelectContent());
        $this->view->assign('trainingPlanSelect', $this->generateTrainingPlanSelectContent());

        if ($this->getRequest()->isPost()) {
            try {
                $trainingPlanId = $this->createLayoutAction();
                if (is_numeric($trainingPlanId)
                    && 0 < $trainingPlanId
                ) {
                    $this->redirect('/training-plans/edit/id/' . $trainingPlanId);
                } else {
                    echo '<br /><br />' . "Konnte aktuellen Trainingsplan nicht anlegen!";
                }
            } catch (Exception $oException) {
                echo '<br /><br />' . $oException->getMessage();
            }
        }
    }

    private function generateUserSelectContent()
    {
        $usersDb = new Model_DbTable_Users();
        $usersCollection = $usersDb->findActiveUsers();
        $content = '';

        if (false !== $usersCollection) {
            $optionsContent = '';
            foreach ($usersCollection as $user) {
                $this->view->assign('optionValue', $user->offsetGet('user_id'));
                $this->view->assign('optionText', $user->offsetGet('user_email'));

                $optionsContent .= $this->view->render('loops/option.phtml');
            }

            $this->view->assign('optionsContent', $optionsContent);
            $this->view->assign('optionLabelText', $this->translate('label_user_select'));
            $this->view->assign('optionClassName', 'member-select');
            $content = $this->view->render('globals/select.phtml');
        }
        return $content;
    }

    private function generateTrainingPlanSelectContent()
    {
        $trainingPlanSelectContent = '';
        $user = Zend_Auth::getInstance()->getIdentity();
        $userId = null;
        if (true === is_object($user)) {
            $userId = $user->user_id;
        }

        if ($userId) {
            $trainingPlansDb = new Model_DbTable_TrainingPlans();
            $trainingPlanCollection = $trainingPlansDb->findAllTrainingPlansForUser($userId);

            var_dump($trainingPlanCollection);
        }

        return $trainingPlanSelectContent;
    }

    public function createLayoutAction() {
        $aParams = $this->getAllParams();
        $trainingPlanId = null;

        if (true === array_key_exists('layout', $aParams)
            && true === array_key_exists('user_id', $aParams)
        ) {
            $iUserId = $aParams['user_id'];
            $trainingPlanService = new Service_TrainingPlan();
            switch ($aParams['layout']) {
                case 1:
                    $trainingPlanId = $trainingPlanService->createBaseTrainingPlan($iUserId);
                    break;
                case 2:
                    $trainingPlanId = $trainingPlanService->createSplitTrainingPlan($iUserId);
                    break;
                default:
            }
        } else {
            echo "Funktion falsch aufgerufen!<br />";
        }

        return $trainingPlanId;
    }
}

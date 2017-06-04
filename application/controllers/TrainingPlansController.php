<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class TrainingPlansController extends AbstractController {

    private $trainingPlanTabHeaderContent = '';

    public function init() {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_training_plan_accordion.js',
                'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript');

            $this->view->assign('demoContent',
                '<button type="button demo" id="training_plans_demo" class="btn btn-default btn-md" data-demo="">
                    <span class="glyphicon glyphicon-play"></span>
                    Start ' . $this->translate('label_training_plans') . ' demo
                </button>');
        }
    }

    /**
     * wenn admin und höher, liste aller aktiven trainingspläne
     * wenn nur user, seinen aktiven training-plans
     */
    public function indexAction() {
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $id = intval($this->getParam('id'));
        $currentTrainingPlan = false;
        $trainingPlanContent = $this->translate('training_plans_default_content');

        if (0 < $id) {
            $currentTrainingPlan = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($id);
            $role = new Auth_Model_Role_Member();
            $resource = new Auth_Model_Resource_TrainingPlans($currentTrainingPlan->current());
            $resourceName = $this->getRequest()->getModuleName().':'.$this->getRequest()->getControllerName();

            Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'show');
            if (false === ($aclResult = Zend_Registry::get('acl')->isAllowed($role, $resource, 'show'))) {
                $currentTrainingPlan = false;
                $trainingPlanContent = $this->translate('text_need_permissions_to_show_training_plan');
            }
        } else if ($this->findCurrentUserId()) {
            $currentTrainingPlan = $trainingPlansDb->findActiveTrainingPlan($this->findCurrentUserId());
        }

        if (0 < count($currentTrainingPlan)
            && false !== $currentTrainingPlan
        ) {
            $this->view->assign('allowedToInterpolate', Auth_Plugin_CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary'));

            $trainingPlanGenerator = new Service_Generator_View_TrainingPlan($this->view);
            $trainingPlanGenerator->setControllerName($this->getRequest()->getControllerName())
                ->setActionName($this->getRequest()->getActionName())
                ->setModuleName($this->getRequest()->getModuleName());

            $trainingPlanContent = $trainingPlanGenerator->generateTrainingPlanContent($currentTrainingPlan);
        }

        $oldTrainingPlans = $trainingPlansDb->findAllTrainingPlansInArchive($this->findCurrentUserId());
        $this->view->assign('trainingPlanContent', $trainingPlanContent);
        $this->view->assign('oldTrainingPlans', $this->generateOldTrainingPlansDropDown($oldTrainingPlans));
    }

    public function getTrainingPlanAction() {
        $id = intval($this->getParam('id'));
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $currentTrainingPlan = false;
        $trainingPlanContent = '';

        if (0 < $id) {
            $currentTrainingPlan = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($id);
            $role = new Auth_Model_Role_Member();
            $resource = new Auth_Model_Resource_TrainingPlans($currentTrainingPlan->current());
            $resourceName = $this->getRequest()->getModuleName().':'.$this->getRequest()->getControllerName();

            Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'show');
            if (false === ($aclResult = Zend_Registry::get('acl')->isAllowed($role, $resource, 'show'))) {
                $currentTrainingPlan = false;
                $trainingPlanContent = $this->translate('text_need_permissions_to_show_training_plan');
            }
        }

        if (false !== $currentTrainingPlan) {
            $this->view->assign('allowedToInterpolate', Auth_Plugin_CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary'));

            $trainingPlanGenerator = new Service_Generator_View_TrainingPlan($this->view);
            $trainingPlanGenerator->setControllerName($this->getRequest()->getControllerName())
                ->setActionName($this->getRequest()->getActionName())
                ->setModuleName($this->getRequest()->getModuleName());

            $trainingPlanContent = $trainingPlanGenerator->generateTrainingPlanContent($currentTrainingPlan);
        }
        $this->view->assign('trainingPlanContent', $trainingPlanContent);
    }

    /**
     * @param \Zend_Db_Table_Rowset_Abstract $trainingPlans
     *
     * @return string
     */
    private function generateOldTrainingPlansDropDown(Zend_Db_Table_Rowset_Abstract $trainingPlans) {

        $this->view->assign('optionText', $this->translate('label_please_select'));
        $this->view->assign('optionValue', -1);
        $this->view->assign('optionClass', 'old-item item');
        $this->view->assign('optionSelectText', $this->translate('label_please_select'));
        $content = $this->view->render('loops/option.phtml');
        foreach ($trainingPlans as $trainingPlan) {
            $this->view->assign('optionText', Date('Y-m-d', strtotime($trainingPlan->offsetGet('training_plan_create_date'))));
            $this->view->assign('optionValue', $trainingPlan->offsetGet('training_plan_id'));
            $content .= $this->view->render('loops/option.phtml');
        }
        $this->view->assign('optionLabelText', $this->translate('label_old_training_plans') . ':');
        $this->view->assign('optionsContent', $content);
        return $this->view->render('globals/select.phtml');
    }

    /**
     * @param Zend_Db_Table_Row $exercise
     *
     * @todo hier werden nur alle muskeln geholt, die für die übung abgelegt wurden, es müssen aber auch alle
     *       anderen muskeln zu der muskelgruppe gezogen werden. muskeln, die zu dieser übung gehören, können
     *       aus der übersicht gelöscht werden, gehört der jeweiligen muskelgruppe kein muskel mehr der übung an
     *       wird die komplette muskelgruppe entfernt
     *
     * @return string
     */
    private function generateMuscleRatingContent($exercise) {
        $exerciseId = $exercise->offsetGet('exercise_id');

        $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
        $muscleGroupCollection = $exerciseXMuscleDb->findMuscleGroupsForExercise($exerciseId);

        $content = '';

        foreach ($muscleGroupCollection as $muscleGroup) {
            $muscleGroupId = $muscleGroup->offsetGet('muscle_group_id');
            $this->view->assign('musclesInMuscleGroupContent',
                $this->generateExerciseMuscleGroupContent($muscleGroupId, $exerciseId));
            $this->view->assign($muscleGroup->toArray());
            $content .= $this->view->render('/loops/muscle-group-row.phtml');
        }
        return $content;
    }

    /**
     * @param Zend_Db_Table_Row $exercise
     *
     * @todo hier werden nur alle muskeln geholt, die für die übung abgelegt wurden, es müssen aber auch alle
     *       anderen muskeln zu der muskelgruppe gezogen werden. muskeln, die zu dieser übung gehören, können
     *       aus der übersicht gelöscht werden, gehört der jeweiligen muskelgruppe kein muskel mehr der übung an
     *       wird die komplette muskelgruppe entfernt
     *
     * @return string
     */
    public function generateExerciseMuscleGroupContent($muscleGroupId, $exerciseId) {
        $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
        $musclesInMuscleGroupForExerciseCollection = $exerciseXMuscleDb->findAllMusclesForMuscleGroupWithExerciseMuscles($exerciseId, $muscleGroupId);
        $musclesInMuscleGroupContent = '';

        foreach ($musclesInMuscleGroupForExerciseCollection as $exerciseXMuscle) {
            $this->view->assign('name', $exerciseXMuscle->offsetGet('muscle_name'));
            $this->view->assign('id', $exerciseXMuscle->offsetGet('muscle_id'));
            $usePosX = -100;
            if(0 < $exerciseXMuscle->offsetGet('exercise_x_muscle_muscle_use')) {
                $usePosX = -100 + ($exerciseXMuscle->offsetGet('exercise_x_muscle_muscle_use') * 20);
            }
            $this->view->assign('usePosX', $usePosX);
            $this->view->assign('muscleUseRatingContent', $this->view->render('loops/rating.phtml'));
            $musclesInMuscleGroupContent .= $this->view->render('/loops/muscle-row.phtml');
        }
        return $musclesInMuscleGroupContent;
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
            $this->view->assign('allowedToInterpolate', Auth_Plugin_CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary'));

            $this->view->assign('training_plan_id',$trainingPlanId);
        } else {
            $this->view->assign('trainingPlanContent', 'Trainingsplan konnte nicht gefunden werden!');
        }
    }

    public function getTrainingPlanForSplitAction() {
        $aParams = $this->getAllParams();

        if ($userId = $this->findCurrentUserId()) {
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
                    $currentName = 'NewPlan'.uniqid();
                    $this->view->assign('name', $currentName);
                    $sContent = '<ul class="nav nav-tabs"><li><a data-toggle="tab" href="#'.$currentName.'">New Plan</a>' .
                        '<div class="training-plans detail-options"><div class="glyphicon glyphicon-trash delete-button" data-id="' .
                        $currentName . '"></div></div></li></ul>';
                    $sContent .= $this->view->render('loops/training-plan-edit.phtml');
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
        if (0 < ($id = $this->getParam('id'))) {
            /*
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

            */
            $this->view->assign('exerciseContent', $this->generateExerciseForEditContent($id));
        }
    }

    private function generateExerciseForEditContent($id) {
        $exercisesDb = new Model_DbTable_Exercises();
        $exercise = $exercisesDb->findByPrimary($id);
        $previewPicture = '/images/content/dynamisch/exercises/' . $exercise->offsetGet('exercise_id') . '/' . $exercise->offsetGet('exercise_preview_picture');
        $previewPictureSource = '/images/content/statisch/grafiken/kein_bild.png';
        $count = $this->getParam('counter');

        if (is_file(getcwd() . '/' . $previewPicture)
            && is_readable(getcwd() . '/' . $previewPicture)
        ) {
            $thumbnailService = new Service_Generator_Thumbnail();
            $thumbnailService->setThumbWidth(1024);
            $thumbnailService->setThumbHeight(768);
            $thumbnailService->setSourceFilePathName(getcwd() . '/' . $previewPicture);
            $previewPictureSource = $thumbnailService->generateImageString();
        }
        $this->view->assign($exercise->toArray());
        $this->view->assign('previewSource', $previewPictureSource);
        $this->view->assign('exercise_description', $exercise->offsetGet('exercise_description'));

        $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
        $deviceOptionsService->setExerciseId($id);

        if (isset($exercise['device_option_id'])) {
            $deviceOptionsService->setOptionId($exercise['device_option_id']);
        }
        if (isset($exercise['device_id'])) {
            $deviceOptionsService->setDeviceId($exercise['device_id']);
        }
        $deviceOptionsService->setForceGenerateEmptyInput(true);
        $deviceOptionsService->setShowDelete(true);
        $this->view->assign('deviceOptionContent', $deviceOptionsService->generate());

        $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
        $exerciseOptionsService->setExerciseId($id);

        if (isset($exercise['device_option_id'])) {
            $exerciseOptionsService->setOptionId($exercise['exercise_option_id']);
        }
        $exerciseOptionsService->setForceGenerateEmptyInput(true);
        $exerciseOptionsService->setShowDelete(true);

        $this->view->assign('exerciseOptionContent', $exerciseOptionsService->generate());

        $deviceOptionsService->setShowDelete(false);
        $this->view->assign('deviceOptionsSelectContent', $deviceOptionsService->generateDeviceOptionsSelectContent());
        $exerciseOptionsService->setShowDelete(false);
        $this->view->assign('exerciseOptionsSelectContent', $exerciseOptionsService->generateExerciseOptionsSelectContent());

        $this->view->assign('count', $count);
        $this->view->assign('muscleRatingContent', $this->generateMuscleRatingContent($exercise));

        return $this->view->render('loops/training-plan-exercise-edit.phtml');
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
        $deviceOptionsService->setAllowEdit(true);
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
        $exerciseOptionsService->setAllowEdit(true);
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
    public function newAction() {
        $trainingPlanId = null;

        if ($this->getRequest()->isPost()) {
            $trainingPlanId = $this->createLayoutAction();
        }

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (0 < $trainingPlanId) {
            $messageEntity = new Model_Entity_Message();
            $messageEntity->setRedirectId($trainingPlanId)
                ->setMessage('Trainingsplan erfolgreich angelegt!')
                ->setState(Model_Entity_Message::STATUS_OK);
            Service_GlobalMessageHandler::setMessageEntity($messageEntity);
        }
    }

    /**
     *
     */
    public function editAction() {
        $trainingPlanId = intval($this->getParam('id'));

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (0 < $trainingPlanId) {
            $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
            $trainingPlanService = new Service_Generator_View_TrainingPlan($this->view);
            $this->view->assign('deviceOptionsSelectContent', $deviceOptionsService->generateDeviceOptionsSelectContent());
            $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
            $this->view->assign('exerciseOptionsSelectContent', $exerciseOptionsService->generateExerciseOptionsSelectContent());
            $this->view->assign('trainingPlanContent', $trainingPlanService->generateTrainingPlanForEditContent($trainingPlanId));

            $trainingPlanTabHeaderContent = $trainingPlanService->getTrainingPlanTabHeaderContent();
            $trainingPlanTabHeaderContent .= '<li><span id="new_split_plan" class="glyphicon glyphicon-plus-sign add"></span></li>';

            $this->view->assign('trainingPlansHeaderContent', $trainingPlanTabHeaderContent);
            $this->view->assign('trainingPlanId', $trainingPlanId);
            $this->view->assign('userNameContent', $this->considerCurrentTrainingPlanOwner($trainingPlanId));
        }
    }

    private function considerCurrentTrainingPlanOwner($trainingPlanId) {
        $userId = $this->findCurrentUserId();
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $trainingPlan = $trainingPlansDb->findByPrimary($trainingPlanId);
        $content = '';

        if ($trainingPlan instanceof Zend_Db_Table_Row_Abstract
            && $userId != $trainingPlan->offsetGet('training_plan_user_fk')
        ) {
            $usersDb = new Model_DbTable_Users();
            $trainingPlanUser = $usersDb->findUser($trainingPlan->offsetGet('training_plan_user_fk'));
            $content = 'Bearbeitet wird der Trainingsplan von ' . $trainingPlanUser->offsetGet('user_first_name') . ' ' . $trainingPlanUser->offsetGet('user_last_name');
        }
        return $content;
    }

    public function saveAction() {

        if (null !== ($trainingPlanCollection = $this->getParam('trainingPlanCollection', null))) {
            $trainingPlanService = new Service_TrainingPlan();
            $trainingPlanService->saveTrainingPlan($trainingPlanCollection, $this->getParam('trainingPlanUserId'));
            Service_GlobalMessageHandler::appendMessage('Trainingsplan erfolgreich angepasst!', Model_Entity_Message::STATUS_OK);
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
    }

    private function generateUserSelectContent()
    {
        $usersCollection = false;
        $usersDb = new Model_DbTable_Users();
        $currentUser = Zend_Auth::getInstance()->getIdentity();
        $currentUserRightGroupName = strtoupper($currentUser->user_right_group_name);
        if ('ADMIN' == $currentUserRightGroupName
            || 'TEST_ADMIN' == $currentUserRightGroupName
            || 'SUPERADMIN' == $currentUserRightGroupName
        ) {
            $usersCollection = $usersDb->findActiveUsers();
        } else if ('GROUP_ADMIN' == $currentUserRightGroupName
            || 'TEST_GROUP_ADMIN' == $currentUserRightGroupName
        ) {
            $usersCollection = $usersDb->findAllActiveUsersInSameUserGroup($currentUser->user_group_id);
        }
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

    private function generateTrainingPlanSelectContent($userId = null)
    {
        $trainingPlanSelectContent = '';
        $trainingPlansDb = new Model_DbTable_TrainingPlans();
        $userTrainingPlansFound = false;
        if (0 < $userId) {
            $trainingPlanCollection = $trainingPlansDb->findAllSingleOrParentTrainingPlansForUser($userId);

            foreach ($trainingPlanCollection as $trainingPlan) {
                $userTrainingPlansFound = true;
                $this->view->assign('optionText', $trainingPlan->offsetGet('training_plan_name') . ' - ' . date('Y-m-d', strtotime($trainingPlan->offsetGet('training_plan_create_date'))));
                $this->view->assign('optionValue', $trainingPlan->offsetGet('training_plan_id'));
                $trainingPlanSelectContent .= $this->view->render('loops/option.phtml');
            }
        }

        if ($this->findCurrentUserId()) {
            $trainingPlanCollection = $trainingPlansDb->findAllSingleOrParentTrainingPlansForUser($this->findCurrentUserId());

            if ($userTrainingPlansFound
                && 0 < count($trainingPlanCollection)
            ) {
                $trainingPlanSelectContent .= '<hr />';
            }
            foreach ($trainingPlanCollection as $trainingPlan) {
                $this->view->assign('optionText', $trainingPlan->offsetGet('training_plan_name') . ' - ' . date('Y-m-d', strtotime($trainingPlan->offsetGet('training_plan_create_date'))));
                $this->view->assign('optionValue', $trainingPlan->offsetGet('training_plan_id'));
                $trainingPlanSelectContent .= $this->view->render('loops/option.phtml');
            }
        }

        $this->view->assign('optionLabelText', $this->translate('label_select_training_plan'));
        $this->view->assign('optionClassName', 'training-plan-select');
        $this->view->assign('optionsContent', $trainingPlanSelectContent);
        return $this->view->render('globals/select.phtml');
    }

    public function getTrainingPlanSelectAction() {
        $userId = intval($this->getParam('user_id'));
        $this->view->assign('trainingPlanSelectContent', $this->generateTrainingPlanSelectContent($userId));
    }

    public function createLayoutAction() {
        $aParams = $this->getAllParams();
        $trainingPlanService = new Service_TrainingPlan();
        $trainingPlanId = null;
        $trainingPlanUserId = $this->getParam('trainingPlanUserId');

        if (empty($trainingPlanUserId)) {
            $trainingPlanUserId = $this->findCurrentUserId();
        }
        if (true === array_key_exists('trainingPlanLayoutId', $aParams)
            && !empty($trainingPlanUserId)
        ) {
            switch ($aParams['trainingPlanLayoutId']) {
                case 1:
//                    return $trainingPlanService->createBaseTrainingPlan($iUserId);
                    return $trainingPlanService->createSplitTrainingPlan($trainingPlanUserId);
                    break;
                case 2:
                    return $trainingPlanService->createSplitTrainingPlan($trainingPlanUserId);
                    break;
                default:
            }
        } else if (!empty($this->getParam('templateTrainingPlanId'))) {
            return $trainingPlanService->createTrainingPlanFromTemplate($this->getParam('templateTrainingPlanId'), $this->getParam('trainingPlanUserId'));
        } else {
            echo "Funktion falsch aufgerufen!<br />";
        }

        return false;
    }

    public function interpolateTrainingDiaryAction() {
//        $userId = intval($this->getParam('userId'));
        $userId = $this->findCurrentUserId();

        if (0 < $userId
            && true === Auth_Plugin_CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary')
        ) {
            $interpolateService = new Service_Interpolate();
            $result = $interpolateService->trainingDiary($userId);

            if ($result) {
                Service_GlobalMessageHandler::appendMessage('Trainingstagebucheinträge erfolgreich genertiert!', Model_Entity_Message::STATUS_OK);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage('Ihnen fehlen die notwendigen Rechte, um diese Aktion auszuführen!', Model_Entity_Message::STATUS_ERROR);
        }
    }

    public function deleteAction() {
        $id = intval($this->getParam('id'));

        if (0 < $id) {
            $trainingPlanService = new Service_TrainingPlan();
            $trainingPlanService->deleteTrainingPlan($id);
        }
    }
}

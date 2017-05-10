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

        if (false !== $currentTrainingPlan) {
            $trainingPlanContent = $this->generateTrainingPlanContent($currentTrainingPlan);
        }

        $oldTrainingPlans = $trainingPlansDb->findAllTrainingPlansInArchive($this->findCurrentUserId());
        $this->view->assign('oldTrainingPlans', $this->generateOldTrainingPlansDropDown($oldTrainingPlans));
        $this->view->assign('trainingPlanContent', $trainingPlanContent);
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
            $trainingPlanContent = $this->generateTrainingPlanContent($currentTrainingPlan);
        }
        $this->view->assign('trainingPlanContent', $trainingPlanContent);
    }

    /**
     * @param \Zend_Db_Table_Rowset
     *
     * @return string
     */
    private function generateTrainingPlanContent(Zend_Db_Table_Rowset_Abstract $trainingPlan) {
        $content = '';

        // split plan
        if (1 < count($trainingPlan)) {
            $content = $this->generateSplitTrainingPlanContent($trainingPlan);
        } else if (1 === count($trainingPlan)) {
            $this->view->assign('currentTrainingPlan', $this->generateCurrentTrainingPlanItem($trainingPlan->current()));
            $content = $this->generateSingleTrainingPlanContent($trainingPlan->current());
        }
        $this->view->assign('trainingPlanOptionsContent', $this->generateDetailOptionsContent($trainingPlan->offsetGet(0)->offsetGet('training_plan_id')));

        return $content;
    }

    private function generateSplitTrainingPlanContent(Zend_Db_Table_Rowset_Abstract $trainingPlanCollection) {
        $content = '';
        $headerContent = '';
        $active = 'active';
        $count = 1;
        foreach ($trainingPlanCollection as $trainingPlan) {
            // parent außer acht lassen
            if (0 < $trainingPlan->offsetGet('training_plan_parent_fk')) {
                $this->view->assign('classActive', $active);
                $headerContent .= $this->generateSplitTrainingPlanHeaderRow($trainingPlan, $count);
                $content .= $this->generateSingleTrainingPlanContent($trainingPlan);
                ++$count;
                $active = '';
            } else {
                $this->view->assign('currentTrainingPlan', $this->generateCurrentTrainingPlanItem($trainingPlan));
            }
        }
        $this->view->assign('trainingPlansHeaderContent', $headerContent);
        $this->view->assign('trainingPlansExercisesContent', $content);
        return $this->view->render('loops/training-plan-split-container.phtml');
    }

    private function generateCurrentTrainingPlanItem(Zend_Db_Table_Row $trainingPlan) {
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
     * @param \Zend_Db_Table_Rowset_Abstract $trainingPlans
     *
     * @return string
     */
    private function generateOldTrainingPlansDropDown(Zend_Db_Table_Rowset_Abstract $trainingPlans) {

        $this->view->assign('optionText', $this->translate('label_please_select'));
        $this->view->assign('optionValue', -1);
        $this->view->assign('optionClass', 'old-item item');
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

    private function generateSplitTrainingPlanHeaderRow(Zend_Db_Table_Row_Abstract $trainingPlan, $count) {
        $name = trim($trainingPlan->offsetGet('training_plan_name'));
        if (0 == strlen($name)) {
            $name = $this->translate('label_day') . '' . $count;
        }
        $this->view->assign('name', $name);
        return $this->view->render('loops/training-plan-split-header-row.phtml');
    }

    private function generateSingleTrainingPlanContent(Zend_Db_Table_Row_Abstract $trainingPlan) {
        $content = '';

        if (0 == $trainingPlan->offsetGet('training_plan_active')) {
            $this->view->assign('archiveTrainingPlanLoaded', true);
        }
        $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
        $exercises = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlan->offsetGet('training_plan_id'));

        foreach ($exercises as $exercise) {
            $content .= $this->generateExerciseContent($exercise);
        }

        $this->view->assign('exercisesContent', $content);

        $this->view->assign('startTrainingPlanLink', '/training-diaries/start/id/' . $trainingPlan->offsetGet('training_plan_id'));
        return $this->view->render('loops/training-plan-split-exercise-row.phtml');
    }

    private function generateExerciseContent(Zend_Db_Table_Row_Abstract $exercise) {
        $previewPicture = '/images/content/dynamisch/exercises/' . $exercise->offsetGet('exercise_id') . '/' . $exercise->offsetGet('exercise_preview_picture');
        $previewPictureSource = '/images/content/statisch/grafiken/kein_bild.png';

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
        $this->view->assign('deviceOptionsContent', $this->generateDeviceOptionsContent($exercise));
        $this->view->assign('exerciseOptionsContent', $this->generateExerciseOptionsContent($exercise));

        return $this->view->render('loops/training-plan-exercise.phtml');
    }

    private function generateDeviceOptionsContent(Zend_Db_Table_Row_Abstract $exercise) {
        $content = '';
        return $content;
    }

    private function generateExerciseOptionsContent(Zend_Db_Table_Row_Abstract $exercise) {
        $content = '';
        return $content;
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
//                    $trainingPlanService = new Service_TrainingPlan();
//                    $trainingPlanId = $trainingPlanService->createTrainingPlan($aData);
//                    $this->view->assign('trainingPlanId', $trainingPlanId);
//                    $this->view->assign('training_plan_id', $trainingPlanId);
                    $currentName = 'NewPlan'.time();
                    $this->view->assign('name', $currentName);
                    $sContent = '<ul class="nav nav-tabs"><li><a data-toggle="tab" href="#'.$currentName.'">New Plan</a></li></ul>';
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

//            $this->view->assign('exerciseContent', $this->view->render('loops/training-plan-exercise-edit.phtml'));
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
            $this->view->assign('userId', $this->findCurrentUserId());
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
//        $this->view->assign('optionSelectText', $this->translate('label_select_training_plan'));
        $this->view->assign('optionsContent', $trainingPlanSelectContent);
        return $this->view->render('globals/select.phtml');
    }

    public function getTrainingPlanSelectAction() {
        $userId = intval($this->getParam('user_id'));
        $this->view->assign('trainingPlanSelectContent', $this->generateTrainingPlanSelectContent($userId));
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

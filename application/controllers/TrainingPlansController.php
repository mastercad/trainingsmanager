<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

use Model\DbTable\TrainingPlans;
use Auth\Model\Role\Member as MemberRole;
use Auth\Model\Resource\TrainingPlans as TrainingPlansResource;
use Auth\Plugin\CheckRight;
use Service\Generator\View\TrainingPlan as TrainingPlanViewGenerator;
use Model\DbTable\ExerciseXMuscle;
use Model\DbTable\Exercises;
use Service\Generator\Thumbnail;
use Service\Generator\View\DeviceOptions as DeviceOptionsViewGenerator;
use Service\Generator\View\ExerciseOptions as ExerciseOptionsViewGenerator;
use Service\GlobalMessageHandler;
use Model\Entity\Message;
use Model\DbTable\Users;
use Service\TrainingPlan as TrainingPlanService;
use Service\Interpolate;

/**
 * Class TrainingPlansController
 */
class TrainingPlansController extends AbstractController {

    private $trainingPlanTabHeaderContent = '';

    /**
     * initial function for controller
     */
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
     * index action
     */
    public function indexAction() {
        $trainingPlansDb = new TrainingPlans();
        $id = intval($this->getParam('id'));
        $currentTrainingPlan = false;
        $trainingPlanContent = $this->translate('training_plans_default_content');

        if (0 < $id) {
            $currentTrainingPlan = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($id);
            $role = new MemberRole();
            $resource = new TrainingPlansResource($currentTrainingPlan->current());
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
            $this->view->assign('allowedToInterpolate', CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary'));

            $trainingPlanGenerator = new TrainingPlanViewGenerator($this->view);
            $trainingPlanGenerator->setControllerName($this->getRequest()->getControllerName())
                ->setActionName($this->getRequest()->getActionName())
                ->setModuleName($this->getRequest()->getModuleName());

            $trainingPlanContent = $trainingPlanGenerator->generateTrainingPlanContent($currentTrainingPlan);
        }

        $oldTrainingPlans = $trainingPlansDb->findAllTrainingPlansInArchive($this->findCurrentUserId());
        $this->view->assign('trainingPlanContent', $trainingPlanContent);
        $this->view->assign('oldTrainingPlans', $this->generateOldTrainingPlansDropDown($oldTrainingPlans));
    }

    /**
     * get training plan action
     *
     * @throws \Zend_Exception
     */
    public function getTrainingPlanAction() {
        $id = intval($this->getParam('id'));
        $trainingPlansDb = new TrainingPlans();
        $currentTrainingPlan = false;
        $trainingPlanContent = '';

        if (0 < $id) {
            $currentTrainingPlan = $trainingPlansDb->findTrainingPlanAndChildrenByParentTrainingPlanId($id);
            $role = new MemberRole();
            $resource = new TrainingPlansResource($currentTrainingPlan->current());
            $resourceName = $this->getRequest()->getModuleName().':'.$this->getRequest()->getControllerName();

            Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'show');
            if (false === ($aclResult = Zend_Registry::get('acl')->isAllowed($role, $resource, 'show'))) {
                $currentTrainingPlan = false;
                $trainingPlanContent = $this->translate('text_need_permissions_to_show_training_plan');
            }
        }

        if (false !== $currentTrainingPlan) {
            $this->view->assign('allowedToInterpolate', CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary'));

            $trainingPlanGenerator = new TrainingPlanViewGenerator($this->view);
            $trainingPlanGenerator->setControllerName($this->getRequest()->getControllerName())
                ->setActionName($this->getRequest()->getActionName())
                ->setModuleName($this->getRequest()->getModuleName());

            $trainingPlanContent = $trainingPlanGenerator->generateTrainingPlanContent($currentTrainingPlan);
        }
        $this->view->assign('trainingPlanContent', $trainingPlanContent);
    }

    /**
     * generate drop down with old training plans
     *
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
     * generate muscle rating content
     *
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

        $exerciseXMuscleDb = new ExerciseXMuscle();
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
     * generate exercise muscle group content
     *
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
        $exerciseXMuscleDb = new ExerciseXMuscle();
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
     * archive action
     */
    public function archiveAction() {
        $trainingPlansDb = new TrainingPlans();
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
     * show action
     */
    public function showAction() {
        $aParams = $this->getAllParams();

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $trainingPlanId = $aParams['id'];
            $trainingPlansDb = new TrainingPlans();
            $trainingPlan = $trainingPlansDb->findTrainingPlan($trainingPlanId);
            $trainingPlanService = new TrainingPlanViewGenerator($this->view);

            $this->view->assign('trainingPlanContent',
                $trainingPlanService->generateTrainingPlanContent($trainingPlanId,
                    $trainingPlan->offsetGet('training_plan_training_plan_layout_fk')));
            $this->view->assign('allowedToInterpolate', CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary'));

            $this->view->assign('training_plan_id',$trainingPlanId);
        } else {
            $this->view->assign('trainingPlanContent', 'Trainingsplan konnte nicht gefunden werden!');
        }
    }

    /**
     * get training plan for split action
     */
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
     * get exercise action
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

    /**
     * generate exercise content for edit
     *
     * @param $id
     *
     * @return string
     */
    private function generateExerciseForEditContent($id) {
        $exercisesDb = new Exercises();
        $exercise = $exercisesDb->findByPrimary($id);
        $previewPicture = '/images/content/dynamisch/exercises/' . $exercise->offsetGet('exercise_id') . '/' .
            $exercise->offsetGet('exercise_preview_picture');
        $previewPictureSource = '/images/content/statisch/grafiken/kein_bild.png';
        $count = $this->getParam('counter');

        if (is_file(getcwd() . '/' . $previewPicture)
            && is_readable(getcwd() . '/' . $previewPicture)
        ) {
            $thumbnailService = new Thumbnail();
            $thumbnailService->setThumbWidth(1024);
            $thumbnailService->setThumbHeight(768);
            $thumbnailService->setSourceFilePathName(getcwd() . '/' . $previewPicture);
            $previewPictureSource = $thumbnailService->generateImageString();
        }
        $this->view->assign($exercise->toArray());
        $this->view->assign('previewSource', $previewPictureSource);
        $this->view->assign('exercise_description', $exercise->offsetGet('exercise_description'));

        $deviceOptionsService = new DeviceOptionsViewGenerator($this->view);
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

        $exerciseOptionsService = new ExerciseOptionsViewGenerator($this->view);
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
     * get device option action
     */
    public function getDeviceOptionAction() {
        $deviceOptionId = intval($this->getParam('deviceOptionId'));
        $exerciseId = intval($this->getParam('exerciseId'));
        $deviceId = intval($this->getParam('deviceId'));
        $trainingPlanExerciseId = intval($this->getParam('trainingPlanExerciseId'));

        $deviceOptionsService = new DeviceOptionsViewGenerator($this->view);
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
     * get exercise option action
     */
    public function getExerciseOptionAction() {
        $exerciseOptionId = intval($this->getParam('exerciseOptionId'));
        $exerciseId = intval($this->getParam('exerciseId'));
        $trainingPlanExerciseId = intval($this->getParam('trainingPlanExerciseId'));

        $exerciseOptionsService = new ExerciseOptionsViewGenerator($this->view);
        $exerciseOptionsService->setTrainingPlanXExerciseId($trainingPlanExerciseId);
        $exerciseOptionsService->setExerciseId($exerciseId);
        $exerciseOptionsService->setOptionId($exerciseOptionId);
        $exerciseOptionsService->setForceGenerateEmptyInput(true);
        $exerciseOptionsService->setAllowEdit(true);
        $exerciseOptionsService->setShowDelete(true);

        $this->view->assign('exerciseOptionContent', $exerciseOptionsService->generate());
    }

    /**
     * get exercise proposals action
     */
    public function getExerciseProposalsAction() {

        $aParams = $this->getAllParams();
        $proposalContent = 'Es wurden leider keine Übungen gefunden!';

        if (array_key_exists('search', $aParams)) {
            $search = base64_decode($aParams['search']);
            $exercisesDb = new Exercises();
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
     * new action
     */
    public function newAction() {
        $trainingPlanId = null;

        if ($this->getRequest()->isPost()) {
            $trainingPlanId = $this->createLayoutAction();
        }

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (0 < $trainingPlanId) {
            $messageEntity = new Message();
            $messageEntity->setRedirectId($trainingPlanId)
                ->setMessage('Trainingsplan erfolgreich angelegt!')
                ->setState(Message::STATUS_OK);
            GlobalMessageHandler::setMessageEntity($messageEntity);
        }
    }

    /**
     * edit action
     */
    public function editAction() {
        $trainingPlanId = intval($this->getParam('id'));

        // wenn ein vorhandener training-plans abgerufen werden soll
        if (0 < $trainingPlanId) {
            $deviceOptionsService = new DeviceOptionsViewGenerator($this->view);
            $trainingPlanService = new TrainingPlanViewGenerator($this->view);
            $this->view->assign('deviceOptionsSelectContent', $deviceOptionsService->generateDeviceOptionsSelectContent());
            $exerciseOptionsService = new ExerciseOptionsViewGenerator($this->view);
            $this->view->assign('exerciseOptionsSelectContent', $exerciseOptionsService->generateExerciseOptionsSelectContent());
            $this->view->assign('trainingPlanContent', $trainingPlanService->generateTrainingPlanForEditContent($trainingPlanId));

            $trainingPlanTabHeaderContent = $trainingPlanService->getTrainingPlanTabHeaderContent();
            $trainingPlanTabHeaderContent .= '<li><span id="new_split_plan" class="glyphicon glyphicon-plus-sign add"></span></li>';

            $this->view->assign('trainingPlansHeaderContent', $trainingPlanTabHeaderContent);
            $this->view->assign('trainingPlanId', $trainingPlanId);
            $this->view->assign('userNameContent', $this->considerCurrentTrainingPlanOwner($trainingPlanId));
        }
    }

    /**
     * consider current training plan owner
     *
     * @param $trainingPlanId
     *
     * @return string
     */
    private function considerCurrentTrainingPlanOwner($trainingPlanId) {
        $userId = $this->findCurrentUserId();
        $trainingPlansDb = new TrainingPlans();
        $trainingPlan = $trainingPlansDb->findByPrimary($trainingPlanId);
        $content = '';

        if ($trainingPlan instanceof Zend_Db_Table_Row_Abstract
            && $userId != $trainingPlan->offsetGet('training_plan_user_fk')
        ) {
            $usersDb = new Users();
            $trainingPlanUser = $usersDb->findUser($trainingPlan->offsetGet('training_plan_user_fk'));
            $content = 'Bearbeitet wird der Trainingsplan von ' . $trainingPlanUser->offsetGet('user_first_name') .
                ' ' . $trainingPlanUser->offsetGet('user_last_name');
        }
        return $content;
    }

    /**
     * save action
     */
    public function saveAction() {

        if (null !== ($trainingPlanCollection = $this->getParam('trainingPlanCollection', null))) {
            $trainingPlanService = new TrainingPlanService();
            $trainingPlanService->saveTrainingPlan($trainingPlanCollection, $this->getParam('trainingPlanUserId'));
            GlobalMessageHandler::appendMessage('Trainingsplan erfolgreich angepasst!', Message::STATUS_OK);
        }
    }

    /**
     * select training plan layout, i think is obsolete
     */
    public function selectLayoutAction() {
        $this->view->assign('userSelect', $this->generateUserSelectContent());
        $this->view->assign('trainingPlanSelect', $this->generateTrainingPlanSelectContent());
    }

    private function generateUserSelectContent()
    {
        $usersCollection = false;
        $usersDb = new Users();
        $currentUser = Zend_Auth::getInstance()->getIdentity();
        $currentUserRightGroupName = strtoupper($currentUser->user_right_group_name);
        if ('ADMIN' == $currentUserRightGroupName
            || 'TEST_ADMIN' == $currentUserRightGroupName
            || 'SUPERADMIN' == $currentUserRightGroupName
        ) {
            $usersCollection = $usersDb->findActiveUsers();
        } else if (('GROUP_ADMIN' == $currentUserRightGroupName
            || 'TEST_GROUP_ADMIN' == $currentUserRightGroupName)
            && $currentUser->user_group_id
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

    /**
     * generate content for select training plan
     *
     * @param null $userId
     *
     * @return string
     */
    private function generateTrainingPlanSelectContent($userId = null)
    {
        $trainingPlanSelectContent = '';
        $trainingPlansDb = new TrainingPlans();
        $userTrainingPlansFound = false;
        if (0 < $userId) {
            $trainingPlanCollection = $trainingPlansDb->findAllSingleOrParentTrainingPlansForUser($userId);

            foreach ($trainingPlanCollection as $trainingPlan) {
                $userTrainingPlansFound = true;
                $this->view->assign('optionText', $trainingPlan->offsetGet('training_plan_name') . ' - ' .
                    date('Y-m-d', strtotime($trainingPlan->offsetGet('training_plan_create_date'))));
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
                $this->view->assign('optionText', $trainingPlan->offsetGet('training_plan_name') . ' - ' .
                    date('Y-m-d', strtotime($trainingPlan->offsetGet('training_plan_create_date'))));
                $this->view->assign('optionValue', $trainingPlan->offsetGet('training_plan_id'));
                $trainingPlanSelectContent .= $this->view->render('loops/option.phtml');
            }
        }

        $this->view->assign('optionLabelText', $this->translate('label_select_training_plan'));
        $this->view->assign('optionClassName', 'training-plan-select');
        $this->view->assign('optionsContent', $trainingPlanSelectContent);
        return $this->view->render('globals/select.phtml');
    }

    /**
     * get training plan select action
     */
    public function getTrainingPlanSelectAction() {
        $userId = intval($this->getParam('user_id'));
        $this->view->assign('trainingPlanSelectContent', $this->generateTrainingPlanSelectContent($userId));
    }

    /**
     * create layout action
     *
     * @return bool|int|mixed
     */
    public function createLayoutAction() {
        $aParams = $this->getAllParams();
        $trainingPlanService = new TrainingPlanService();
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

    /**
     * interpolate training diary action
     *
     * interpolates current training plan with fake training diary exercises entries
     */
    public function interpolateTrainingDiaryAction() {
//        $userId = intval($this->getParam('userId'));
        $userId = $this->findCurrentUserId();

        if (0 < $userId
            && true === CheckRight::hasRight('default', 'training-plans', 'interpolate-training-diary')
        ) {
            $interpolateService = new Interpolate();
            $result = $interpolateService->trainingDiary($userId);

            if ($result) {
                GlobalMessageHandler::appendMessage('Trainingstagebucheinträge erfolgreich genertiert!', Message::STATUS_OK);
            }
        } else {
            GlobalMessageHandler::appendMessage('Ihnen fehlen die notwendigen Rechte, um diese Aktion auszuführen!', Message::STATUS_ERROR);
        }
    }

    /**
     * delete action
     */
    public function deleteAction() {
        $id = intval($this->getParam('id'));

        if (0 < $id) {
            $trainingPlanService = new TrainingPlanService();
            $trainingPlanService->deleteTrainingPlan($id);
        }
    }
}

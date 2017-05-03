<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class ExercisesController extends AbstractController {
    
    public function indexAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

        $exercisesDb = new Model_DbTable_Exercises();
        $exerciseType = $this->getParam('exercise-type', null);
        $device = $this->getParam('device', null);
        $exercisesCollection = $exercisesDb->findExercises($exerciseType, $device)->toArray();
        
        $this->view->assign('exercisesCollection', $exercisesCollection);
        $this->view->assign('exercisesFilterContent', $this->generateFilterContent());
    }
    
    public function showAction() {
        $exerciseId = intval($this->getRequest()->getParam('id', null));
        $exercise = null;

        if (0 < $exerciseId) {
            $exerciseDb = new Model_DbTable_Exercises();
            $exercise = $exerciseDb->findExerciseById($exerciseId);

            if ($exercise instanceof Zend_Db_Table_Row) {
                $this->view->assign($exercise->toArray());
                $this->view->assign('detailOptionsContent', $this->generateDetailOptionsContent($exerciseId));
                $this->view->assign('previewPictureContent', $this->generatePreviewPictureContent($exercise));
                $this->view->assign('exerciseMuscleGroupsContent', $this->generateExerciseMuscleGroupsContent($exercise));
                $this->view->assign('exerciseOptionsContent', $this->generateExerciseOptionsContent($exerciseId));
                $this->view->assign('deviceOptionsContent', $this->generateDeviceOptionsContent($exerciseId));
            } else {
                echo "Übung konte nicht geladen werden!";
            }
        }
    }
    
    public function editAction() {
        if (true) {
            $exerciseId = intval($this->getRequest()->getParam('id', null));
            $exercise = null;

            if (0 < $exerciseId) {
                $exerciseDb = new Model_DbTable_Exercises();
                $exercise = $exerciseDb->findExerciseById($exerciseId);

                if ($exercise instanceof Zend_Db_Table_Row) {
                    $this->view->assign($exercise->toArray());
                    $this->view->assign('exerciseMuscleGroupsContent',
                        $this->generateExerciseMuscleGroupsEditContent($exercise));
                    $this->view->assign('exerciseOptionsContent', $this->generateExerciseOptionsEditContent($exercise));
                    $this->view->assign('deviceOptionsContent', $this->generateDeviceOptionsEditContent($exercise));
                } else {
                    echo "Übung konte nicht geladen werden!";
                }
            }
            $this->view->assign('previewPicturesContent', $this->generatePreviewPicturesForEditContent($exerciseId));
            $this->view->assign('previewPictureContent', $this->generatePreviewPictureForEditContent($exercise));
            $this->view->assign('exerciseTypeSelectContent', $this->generateExerciseTypeContent($exercise));
            $this->view->assign('deviceOptionsDropDownContent', $this->generateDeviceOptionsDropDownContent());
            $this->view->assign('exerciseOptionsDropDownContent', $this->generateExerciseOptionsDropDownContent());
        } else {
            echo "Keine rechte zum bearbeiten!";
        }
    }

    /**
     * @param $exerciseId
     *
     * @return string
     */
    private function generateExerciseOptionsContent($exerciseId)
    {
        $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
        $exerciseOptionsService->setExerciseId($exerciseId);
        $exerciseOptionsService->setAllowEdit(false);

        return $exerciseOptionsService->generate();
    }

    /**
     * @param $exerciseId
     *
     * @return string
     */
    private function generateDeviceOptionsContent($exerciseId)
    {
        $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
        $deviceOptionsService->setExerciseId($exerciseId);
        $deviceOptionsService->setAllowEdit(false);

        return $deviceOptionsService->generate();
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
    public function generateExerciseMuscleGroupsContent($exercise) {
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
     * @param Zend_Db_Table_Row $exercise
     *
     * @todo hier werden nur alle muskeln geholt, die für die übung abgelegt wurden, es müssen aber auch alle
     *       anderen muskeln zu der muskelgruppe gezogen werden. muskeln, die zu dieser übung gehören, können
     *       aus der übersicht gelöscht werden, gehört der jeweiligen muskelgruppe kein muskel mehr der übung an
     *       wird die komplette muskelgruppe entfernt
     *
     * @return string
     */
    public function generateExerciseMuscleGroupsEditContent($exercise) {
        $exerciseId = $exercise->offsetGet('exercise_id');

        $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
        $muscleGroupCollection = $exerciseXMuscleDb->findMuscleGroupsForExercise($exerciseId);

        $content = '';

        foreach ($muscleGroupCollection as $muscleGroup) {
            $muscleGroupId = $muscleGroup->offsetGet('muscle_group_id');
            $this->view->assign('musclesInMuscleGroupContent',
                $this->generateExerciseMuscleGroupEditContent($muscleGroupId, $exerciseId));
            $this->view->assign($muscleGroup->toArray());
            $content .= $this->view->render('/loops/muscle-group-edit.phtml');
        }
        return $content;
    }

    private function generateFilterContent() {

        $this->view->assign('exerciseTypeFilterContent', $this->generateExerciseTypeContentForFilter(null));
        $this->view->assign('deviceFilterContent', $this->generateDeviceFilterContentForFilter());

        return $this->view->render('exercises/filter.phtml');
    }

    private function generateDeviceFilterContentForFilter() {

        $device = $this->getParam('device', null);
        $deviceName = '';

        $exerciseXDeviceDb = new Model_DbTable_ExerciseXDevice();
        $devicesCollection = $exerciseXDeviceDb->findDevicesWithExercises();
        $exercisesWithoutDevices = $exerciseXDeviceDb->findExercisesWithoutDevices();

        $deviceContent = '';
        $optionSelectText = $this->translate('label_please_select');

        $this->view->assign('optionValue', 0);
        $this->view->assign('optionText', $this->translate('label_please_select'));
        $deviceContent .= $this->view->render('loops/option.phtml');

        foreach ($devicesCollection as $currentDevice) {
            $this->view->assign('optionValue', $currentDevice->offsetGet('device_id'));
            $this->view->assign('optionText', $currentDevice->offsetGet('device_name') . ' (' .
                $currentDevice->offsetGet('exerciseCount') . ' ' . (1 < $currentDevice->offsetGet('exerciseCount') ?
                    $this->translate('label_exercises') : $this->translate('label_exercise')) . ') ');

            if ($device == $currentDevice->offsetGet('device_id')) {
                $deviceName = $currentDevice->offsetGet('device_name');
            }

            $deviceContent .= $this->view->render('loops/option.phtml');
        }

        if ($exercisesWithoutDevices) {
            $this->view->assign('optionValue', 'WITHOUT');
            $this->view->assign('optionText', $this->translate('label_exercises_without_device') . ' (' .
                $exercisesWithoutDevices->offsetGet('exerciseCount') . ' ' . (1 < $exercisesWithoutDevices->offsetGet('exerciseCount') ?
                    $this->translate('label_exercises') : $this->translate('label_exercise')) . ') ');

            if ($device == 'WITHOUT') {
                $deviceName = $this->translate('label_exercises_without_exercise_type');
            }

            $deviceContent .= $this->view->render('loops/option.phtml');
        }

        if ($device) {
            $optionSelectText = $deviceName;
            $this->view->assign('currentValue', $device);
        }

        $this->view->assign('selectId', 'device_id');
        $this->view->assign('optionsContent', $deviceContent);

        $this->view->assign('optionSelectText', $optionSelectText);
        $this->view->assign('optionLabelText', $this->translate('label_device_name') . ':');
        $this->view->assign('optionClassName', 'device-select custom-drop-down');

        return $this->view->render('globals/select.phtml');
    }

    private function generateExerciseTypeContentForFilter() {

        $exerciseType = $this->getParam('exercise-type', null);
        $exerciseTypeName = '';

        $exerciseTypeDb = new Model_DbTable_ExerciseTypes();
        $exerciseXExerciseTypes = new Model_DbTable_ExerciseXExerciseType();
        $exerciseTypesCollection = $exerciseTypeDb->findAllExerciseTypes();

        $exerciseTypeContent = '';
        $optionSelectText = $this->translate('label_please_select');

        $this->view->assign('optionValue', 0);
        $this->view->assign('optionText', $this->translate('label_please_select'));
        $exerciseTypeContent .= $this->view->render('loops/option.phtml');

        foreach ($exerciseTypesCollection as $currentExerciseType) {
            $this->view->assign('optionValue', $currentExerciseType->offsetGet('exercise_type_id'));
            $this->view->assign('optionText', $currentExerciseType->offsetGet('exercise_type_name'));

            if ($exerciseType == $currentExerciseType->offsetGet('exercise_type_id')) {
                $exerciseTypeName = $currentExerciseType->offsetGet('exercise_type_name');
            }
            $exerciseTypeContent .= $this->view->render('loops/option.phtml');
        }

        $exercisesWithoutExerciseTypes = $exerciseXExerciseTypes->findExercisesWithoutExerciseTypes();

        if ($exercisesWithoutExerciseTypes) {
            $this->view->assign('optionValue', 'WITHOUT');
            $this->view->assign('optionText', $this->translate('label_exercises_without_exercise_type') . ' (' .
                $exercisesWithoutExerciseTypes->offsetGet('exerciseCount') . ' ' . (1 < $exercisesWithoutExerciseTypes->offsetGet('exerciseCount') ?
                    $this->translate('label_exercises') : $this->translate('label_exercise')) . ') ');

            if ($exerciseType == 'WITHOUT') {
                $exerciseTypeName = $this->translate('label_exercises_without_exercise_type');
            }

            $exerciseTypeContent .= $this->view->render('loops/option.phtml');
        }

        if ($exerciseType) {
            $optionSelectText = $exerciseTypeName;
            $this->view->assign('currentValue', $exerciseType);
        }

        $this->view->assign('selectId', 'exercise_type_id');
        $this->view->assign('optionsContent', $exerciseTypeContent);

        $this->view->assign('optionSelectText', $optionSelectText);
        $this->view->assign('optionLabelText', $this->translate('label_exercise_type') . ':');
        $this->view->assign('optionClassName', 'exercise-type-select custom-drop-down');

        return $this->view->render('globals/select.phtml');
    }

    private function generateExerciseMuscleGroupEditContent($muscleGroupId, $exerciseId) {
        $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
        $musclesInMuscleGroupForExerciseCollection =
            $exerciseXMuscleDb->findAllMusclesForMuscleGroupWithExerciseMuscles($exerciseId, $muscleGroupId);
        $musclesInMuscleGroupContent = '';

        foreach ($musclesInMuscleGroupForExerciseCollection as $exerciseXMuscle) {
            $this->view->assign($exerciseXMuscle->toArray());
            $usePosX = -100;
            if(0 < $exerciseXMuscle->offsetGet('exercise_x_muscle_muscle_use')) {
                $usePosX = -100 + ($exerciseXMuscle->offsetGet('exercise_x_muscle_muscle_use') * 20);
            }
            $this->view->assign('usePosX', $usePosX);
            $this->view->assign('muscleUseRatingContent', $this->view->render('loops/rating.phtml'));
            $musclesInMuscleGroupContent .= $this->view->render('/loops/muscle.phtml');
        }
        return $musclesInMuscleGroupContent;
    }

    public function getMuscleGroupForExerciseEditAction() {
        $muscleGroupId = $this->getParam('muscle_group_id');
        $exerciseId = $this->getParam('exercise_id');

        $this->view->assign('musclesInMuscleGroupContent',
            $this->generateExerciseMuscleGroupEditContent($muscleGroupId, $exerciseId));

        $this->view->assign('muscleGroupContent', $this->view->render('/loops/muscle-group.phtml'));
    }

    /**
     * @param Zend_Db_Table_Row $exercise
     */
    public function generateDeviceOptionsEditContent($exercise) {
        $content = '';

        foreach ($this->collectDeviceOptions($exercise) as $deviceOptionId => $deviceOption) {
            $this->view->assign($deviceOption);
            $this->view->assign('device_option_value',
                $deviceOption['exercise_x_device_option_device_option_value'] ?
                    $deviceOption['exercise_x_device_option_device_option_value'] :
                    $deviceOption['device_x_device_option_device_option_value']);
            $content .= $this->view->render('/loops/device-option-edit.phtml');
        }
        return $content;
    }

    private function collectDeviceOptions($exercise) {
        $exerciseId = $exercise->offsetGet('exercise_id');

        $deviceXDeviceOptionDb = new Model_DbTable_DeviceXDeviceOption();

        $deviceOptionCollection = [];

        if ($exercise->offsetGet('exercise_x_device_device_fk')) {
            $deviceXDeviceOptionCollection = $deviceXDeviceOptionDb->findAllDeviceXDeviceOptionsByDeviceId($exercise->offsetGet('exercise_x_device_device_fk'))->toArray();

            /** unify DeviceOptions */
            foreach ($deviceXDeviceOptionCollection as $deviceOption) {
                $deviceOptionCollection[$deviceOption['device_option_id']] = $deviceOption;
            }
        }
        $ExerciseXDeviceOptionDb = new Model_DbTable_ExerciseXDeviceOption();
        $exerciseOptionCollection = $ExerciseXDeviceOptionDb->findDeviceOptionsForExercise($exerciseId)->toArray();


        foreach ($exerciseOptionCollection as $exerciseDeviceOption) {
            $deviceOptionId = $exerciseDeviceOption['device_option_id'];
            if (array_key_exists($deviceOptionId, $deviceOptionCollection)
                && ! empty($exerciseDeviceOption['exercise_x_device_option_device_option_value'])
            ) {
                $deviceOptionCollection[$deviceOptionId] = array_merge($deviceOptionCollection[$deviceOptionId],
                    $exerciseDeviceOption);
            } else if (! array_key_exists($deviceOptionId, $deviceOptionCollection)) {
                $deviceOptionCollection[$deviceOptionId] = $exerciseDeviceOption;
            }
        }

        return $deviceOptionCollection;
    }

    public function getExerciseOptionEditAction() {
        $exerciseId = $this->getParam('exercise_id');
        $exerciseOptionId = $this->getParam('exercise_option_id');

        $exerciseXExerciseOptionDb = new Model_DbTable_ExerciseXExerciseOption();
        $exerciseXExerciseOption = $exerciseXExerciseOptionDb->findExerciseOptionForExercise($exerciseId, $exerciseOptionId);

        $this->view->assign($exerciseXExerciseOption->toArray());
        $this->view->assign('exercise_option_value', $exerciseXExerciseOption->offsetGet('exercise_x_exercise_option_exercise_option_value'));
        $this->view->assign('exerciseOptionContent', $this->view->render('/loops/exercise-option-edit.phtml'));
    }

    /**
     * @param Zend_Db_Table_Row $exercise
     */
    public function generateExerciseOptionsEditContent($exercise) {
        $exerciseId = $exercise->offsetGet('exercise_id');

        $exerciseXExerciseOptionDb = new Model_DbTable_ExerciseXExerciseOption();
        $exerciseXExerciseOptionCollection = $exerciseXExerciseOptionDb->findExerciseOptionsForExercise($exerciseId);

        $content = '';

        foreach ($exerciseXExerciseOptionCollection as $exerciseOption) {
            $this->view->assign($exerciseOption->toArray());
            $this->view->assign('exercise_option_value', $exerciseOption->offsetGet('exercise_x_exercise_option_exercise_option_value'));
            $content .= $this->view->render('/loops/exercise-option-edit.phtml');
        }
        return $content;
    }

    private function generateExerciseTypeContent($exercise) {
        $exerciseTypeDb = new Model_DbTable_ExerciseTypes();
        $exerciseTypesCollection = $exerciseTypeDb->findAllExerciseTypes();

        $exerciseTypeContent = '';
        $optionSelectText = $this->translate('label_please_select');

        $this->view->assign('optionValue', 0);
        $this->view->assign('optionText', $this->translate('label_please_select'));
        $exerciseTypeContent .= $this->view->render('loops/option.phtml');

        foreach ($exerciseTypesCollection as $exerciseType) {
            $this->view->assign('optionValue', $exerciseType->offsetGet('exercise_type_id'));
            $this->view->assign('optionText', $exerciseType->offsetGet('exercise_type_name'));

            $exerciseTypeContent .= $this->view->render('loops/option.phtml');
        }

        if ($exercise instanceof Zend_Db_Table_Row
            && $exercise->offsetGet('exercise_type_id')
        ) {
            $optionSelectText = $exercise->offsetGet('exercise_type_name');
            $this->view->assign('currentValue', $exercise->offsetGet('exercise_type_id'));
        }

        $this->view->assign('selectId', 'exercise_type_id');
        $this->view->assign('optionsContent', $exerciseTypeContent);

        $this->view->assign('optionSelectText', $optionSelectText);
        $this->view->assign('optionLabelText', $this->translate('label_exercise_type') . ':');
        $this->view->assign('optionClassName', 'exercise-type-select custom-drop-down');

        return $this->view->render('globals/select.phtml');
    }

    private function generatePreviewPictureContent($exercise)
    {
        $this->view->assign('previewPictureId', 'exercise_preview_picture');
        $this->view->assign('previewPicturePath', $this->generatePreviewPicturePath($exercise));

        return $this->view->render('globals/preview-picture.phtml');
    }

    private function generatePreviewPictureForEditContent($exercise)
    {
        $this->view->assign('previewPictureId', 'exercise_preview_picture');
        $previewPicturePath = $this->generatePreviewPicturePath($exercise);
        list($width, $height, $type, $sizeString, $bits, $mime) = getimagesize(APPLICATION_PATH.'/../public/'.$previewPicturePath);


        $this->view->assign('dropZoneBackgroundImage', $previewPicturePath);

        return $this->view->render('loops/preview-picture-for-edit.phtml');
    }

    /**
     * @param Zend_Db_Table_Row $exercise
     *
     * @return string
     */
    private function generatePreviewPicturePath($exercise) {

        $previewPicturePath = '/images/content/statisch/grafiken/kein_bild.png';
        if ($exercise instanceof Zend_Db_Table_Row) {
            $picturePath = '/images/content/dynamisch/exercises/' . $exercise->offsetGet('exercise_id') . '/';
            $tempPicturePath = '/tmp/exercises/';

            if (0 < strlen(trim($exercise->offsetGet('exercise_preview_picture')))
                && file_exists(getcwd() . $picturePath . $exercise->offsetGet('exercise_preview_picture'))
                && is_file(getcwd() . $picturePath . $exercise->offsetGet('exercise_preview_picture'))
                && is_readable(getcwd() . $picturePath . $exercise->offsetGet('exercise_preview_picture'))
            ) {
                $previewPicturePath = $picturePath . $exercise->offsetGet('exercise_preview_picture');
            }
            $this->view->assign('picturePath', $picturePath);
            $this->view->assign('pictureTempPath', $tempPicturePath);
        }
        return $previewPicturePath;
    }

    public function uploadPictureAction() {
        $this->view->layout()->disableLayout();
        $result = [];
        if (true === isset($_FILES['file'])) {
            $temp_bild_pfad = getcwd() . '/tmp/exercises/';

            $obj_file = new CAD_File();
            $obj_file->setDestPath($temp_bild_pfad);
            $obj_file->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'svg', 'gif'));
            $obj_file->setUploadedFiles($_FILES['file']);
            $obj_file->moveUploadedFiles();

            $a_files = $obj_file->getDestFiles();
            if (true === isset($a_files[0][CAD_FILE::HTML_PFAD])) {
                $result['id'] = time();
                $result['paths'] = $a_files[0];
            }
        }
        $this->view->assign('json', json_encode($result));
    }

    /**
     * function um eine übersicht aller bilder des jeweiligen editierten
     * projektes zurück zu erhalten und es formatiert auszugeben
     */
    public function getPicturesForEditAction() {
        $req = $this->getRequest();
        $params = $req->getParams();

        $this->view->assign('previewPicturesForEditContent', $this->generatePreviewPicturesForEditContent($params['id']));
        $this->view->assign('templateDisplayType', 'none');
    }

    private function generatePreviewPicturesForEditContent($exerciseId)
    {
        $previewPictureContent = '';
        $obj_files = new CAD_File();
        $obj_files->setSourcePath(getcwd() . '/tmp/exercises');
        $obj_files->addSourcePath(getcwd() . "/images/content/dynamisch/exercises/" . $exerciseId);
        $obj_files->holeBilderAusPfad();

        $previewPicturesCollection = $obj_files->getDestFiles();

        foreach ($previewPicturesCollection as $previewPicture) {
            $sysPath = APPLICATION_PATH.'/../public/'.$previewPicture['html_pfad'];
            $thumbnailService = new Service_Generator_Thumbnail();
            $thumbnailService->setSourceFilePathName($sysPath);
            $thumbnailService->setThumbHeight(120);
            $thumbnailService->setThumbWidth(120);
            $this->view->assign('templateDisplayType', 'block');
            $this->view->assign('previewType', 'dz-image-preview');
            $this->view->assign('sourceData', $thumbnailService->generateImageString());
            $this->view->assign('sourcePath', $previewPicture['html_pfad']);
            $this->view->assign('fileName', $previewPicture['file']);
            $this->view->assign('fileSize', $this->humanFileSize(filesize($sysPath)));
            $previewPictureContent .= $this->view->render('loops/dropzone-preview-template.phtml');
        }
        $this->view->assign('previewPicturesThumbContent', $previewPictureContent);
        return $previewPictureContent;
    }

    public function deletePictureAction() {
        $req = $this->getRequest();
        $params = $req->getParams();

        if (true === isset($params['bild'])) {
            $bild_pfad = getcwd() . base64_decode($params['bild']);

            if (true === file_exists($bild_pfad)
                && true === is_file($bild_pfad)
                && true === is_readable($bild_pfad)
            ) {
                if (true === @unlink($bild_pfad)) {
                    echo "Bild erfolgreich gelöscht!<br />";
                }
            }
        } else {
            echo "Es wurde kein Bild übergeben!<br />";
        }
    }

    public function deleteAction() {
        $params = $this->getRequest()->getParams();
        $messages = array();

        if (true === isset($params['id'])
            && true === is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $exerciseId = $params['id'];
            $exercisesDb = new Model_DbTable_Exercises();
            if ($exercisesDb->deleteExercise($exerciseId)) {
                $i_count_message = count($messages);
                $messages[$i_count_message]['type'] = "meldung";
                $messages[$i_count_message]['message'] = "Übung erfolgreich gelöscht!";
                $messages[$i_count_message]['result'] = true;
                
                $bilder_pfad = getcwd() . '/images/content/dynamisch/exercises/' . $exerciseId . '/';
                
                $obj_file = new CAD_File();
                $obj_file->cleanDirRek($bilder_pfad, 2);
            } else {
                $i_count_message = count($messages);
                $messages[$i_count_message]['type'] = "fehler";
                $messages[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
                $messages[$i_count_message]['result'] = false;
            }
        } else {
            $i_count_message = count($messages);
            $messages[$i_count_message]['type'] = "fehler";
            $messages[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
            $messages[$i_count_message]['result'] = false;
        }
        $this->view->assign('json_string', json_encode($messages));
    }
    
    public function saveAction() {
        $params = $this->getRequest()->getParams();

        $userId = 1;
        $user = Zend_Auth::getInstance()->getIdentity();

        if (TRUE == is_object($user)) {
            $userId = $user->user_id;
        }

        if ($this->getRequest()->isPost()) {
            $exercisesDb = new Model_DbTable_Exercises();

            $exerciseName = '';

            $exerciseXMuscleDeletes = array();
            $exerciseXMuscleUpdates = array();
            $exerciseXMuscleInserts = array();

            $exercisePreviewPicture = '';
            $exerciseDescription = '';
            $exerciseSpecialFeatures = '';
            $exerciseId = 0;
            $hasErrors = false;
            $messages = [];
            $data = array();

            if (true === isset($params['exercise_name'])
                && 0 < strlen(trim($params['exercise_name']))
            ) {
                $exerciseName = base64_decode($params['exercise_name']);
            }

            if (true === isset($params['exercise_description'])
                && 0 < strlen(trim($params['exercise_description']))
            ) {
                $exerciseDescription = base64_decode($params['exercise_description']);
            }
            
            if (true === isset($params['exercise_special_features'])
                && 0 < strlen(trim($params['exercise_special_features']))
            ) {
                $exerciseSpecialFeatures = base64_decode($params['exercise_special_features']);
            }
            
            if (true === isset($params['exercise_preview_picture'])
                && 0 < strlen(trim($params['exercise_preview_picture']))
            ) {
                $exercisePreviewPicture = base64_decode($params['exercise_preview_picture']);
            }

            if (true === isset($params['exercise_id'])){
                $exerciseId = $params['exercise_id'];
            }
            
            if (0 == strlen(trim($exerciseName))
                && true === empty($exerciseId)
            ) {
                array_push($messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt einen Namen'));
                $hasErrors = true;
            } else if (0 < strlen(trim($exerciseName))) {
                $data['exercise_name'] = $exerciseName;
            }

            if (0 < strlen(trim($exercisePreviewPicture))) {
                $data['exercise_preview_picture'] = $exercisePreviewPicture;
            }

            if(0 < strlen(trim($exerciseSpecialFeatures))) {
                $data['exercise_special_features'] = $exerciseSpecialFeatures;
            }

            if (0 == strlen(trim($exerciseDescription))
                && true === empty($exerciseId)
            ) {
                array_push($messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt eine Beschreibung'));
                $hasErrors = true;
            } else if (0 < strlen(trim($exerciseDescription))) {
                $data['exercise_description'] = $exerciseDescription;
            }
            
            $cadSeo = new CAD_Seo();
            
            if (true === empty($exerciseId)
                && 0 == strlen(trim($exerciseName))
            ) {
                $currentExercise = $exercisesDb->findExercisesByName($exerciseName);
                if (! $currentExercise instanceof Zend_Db_Table_Row) {
                    array_push($messages, array('type' => 'fehler', 'message' => 'Übung "' . $exerciseName . '" existiert bereits!', 'result' => false));
                    $hasErrors = true;
                }
            }

            if (false === $hasErrors) {
                // updaten?
                if (is_numeric($exerciseId)
                   && 0 < $exerciseId
                   && is_array($data)
                   && 0 < count($data)
                ) {
                    $currentExercise = $exercisesDb->findExerciseById($exerciseId);
                    if (
                        (
                            isset($data['exercise_name'])
                            && 0 < strlen(trim($data['exercise_name']))
                            && $currentExercise['exercise_name'] != $data['exercise_name']
                        ) ||
                        (
                            isset($currentExercise['exercise_name'])
                            && 0 < strlen(trim($currentExercise['exercise_name']))
                            && ! strlen(trim($currentExercise['exercise_seo_link']))
                        )
                    )
                    {
                        if (isset($data['exercise_name'])
                           && 0 < strlen(trim($data['exercise_name']))
                        ) {
                            $exerciseName = $data['exercise_name'];
                        } else if (isset($currentExercise['exercise_name'])
                            && 0 < strlen(trim($currentExercise['exercise_name']))
                        ) {
                            $exerciseName = $currentExercise['exercise_name'];
                        }
                        $cadSeo->setLinkName($exerciseName);
                        $cadSeo->setDbTable($exercisesDb);
                        $cadSeo->setTableFieldName("exercise_seo_link");
                        $cadSeo->setTableFieldIdName("exercise_id");
                        $cadSeo->setTableFieldId($exerciseId);
                        $cadSeo->createSeoLink();
                        $data['exercise_seo_link'] = $cadSeo->getSeoName();
                    }
                    $data['exercise_update_date'] = date("Y-m-d H:i:s");
                    $data['exercise_update_user_fk'] = $userId;

                    $exercisesDb->updateExercise($data, $exerciseId);
                    array_push($messages, array('type' => 'meldung', 'message' => 'Diese Übung wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $exerciseId));
                // neu anlegen
                } else if (is_array($data)
                    && 0 < count($data)
                ) {
                    $cadSeo->setLinkName($data['exercise_name']);
                    $cadSeo->setDbTable($exercisesDb);
                    $cadSeo->setTableFieldName("exercise_seo_link");
                    $cadSeo->setTableFieldIdName("exercise_id");
                    $cadSeo->setTableFieldId($exerciseId);
                    $cadSeo->createSeoLink();
                    
                    $data['exercise_seo_link'] = $cadSeo->getSeoName();
                    $data['exercise_create_date'] = date("Y-m-d H:i:s");
                    $data['exercise_create_user_fk'] = $userId;

                    $exerciseId = $exercisesDb->saveExercise($data);
                    array_push($messages, array('type' => 'meldung', 'message' => 'Diese Übung wurde erfolgreich angelegt!', 'result' => true, 'id' => $exerciseId));
                } else if (0 == count($exerciseXMuscleInserts)
                    && 0 == count($exerciseXMuscleUpdates)
                    && 0 == count($exerciseXMuscleDeletes)
                ) {
                    array_push($messages, array('type' => 'meldung', 'message' => 'Die beanspruchten Muskeln für diese Übung wurden nicht geändert!', 'result' => true, 'id' => $exerciseId));
                }
                if (0 < count($exerciseXMuscleInserts)
                    || 0 < count($exerciseXMuscleUpdates)
                    || 0 < count($exerciseXMuscleDeletes)
                ) {
                    array_push($messages, array('type' => 'meldung', 'message' => 'Die Muskeln der Übung wurden erfolgreich geändert!', 'result' => true, 'id' => $exerciseId));
                }
                
                if ($exerciseId) {
                    /* bilder verschieben */
                    $cadFiles = new CAD_File();
                    
                    $sourcePath = getcwd() . '/tmp/exercises/';
                    $destinationPath = getcwd() . '/images/content/dynamisch/exercises/' . $exerciseId . '/';

                    if ($cadFiles->checkAndCreateDir($destinationPath)) {
                        $cadFiles->setSourcePath($sourcePath);
                        $cadFiles->setDestPath($destinationPath);
                        $cadFiles->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif', 'svg'));
                        $cadFiles->verschiebeFiles();
                    }

                    $this->processExerciseXDevice($params, $exerciseId);
                    $this->saveExerciseXMuscle($params, $exerciseId);
                    $this->saveExerciseXDeviceOptions($params, $exerciseId);
                    $this->saveExerciseXExerciseOptions($params, $exerciseId);
                    $this->processExerciseXExerciseType($params, $exerciseId);
                }
            } else {
                array_push($messages, array('type' => 'fehler', 'message' => 'Es gabe einen Fehler beim speichern der Übung!', 'result' => false));
            }
        } else {
            array_push($messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Übung speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($messages));
    }

    private function processExerciseXDevice($params, $exerciseId)
    {
        $exerciseXDeviceId = $params['exercise_device_fk'];

        $exerciseXDeviceDb = new Model_DbTable_ExerciseXDevice();

        // no device_id given in params? create new entry
        if (! empty($exerciseXDeviceId)) {
            $userId = 1;
            $user = Zend_Auth::getInstance()->getIdentity();

            if (TRUE == is_object($user)) {
                $userId = $user->user_id;
            }

            $exerciseXDevice = $exerciseXDeviceDb->findDeviceForExercise($exerciseId);

            if (empty($exerciseXDevice)) {
                $data = [
                    'exercise_x_device_device_fk' => $exerciseXDeviceId,
                    'exercise_x_device_exercise_fk' => $exerciseId,
                    'exercise_x_device_create_date' => date('Y-m-d H:i:s'),
                    'exercise_x_device_create_user_fk' => $userId
                ];
                $exerciseXDeviceDb->saveExerciseXDevice($data);
            }
        // empty? delete old entry!
        } else {
            $exerciseXDevice = $exerciseXDeviceDb->findDeviceForExercise($exerciseId);

            if (! empty($exerciseXDevice)) {
                $exerciseXDeviceDb->deleteExerciseXDevice($exerciseXDevice->offsetGet('exercise_x_device_id'));
            }

        }

        return $this;
    }

    /**
     * @param $params
     * @param $exerciseId
     */
    private function saveExerciseXMuscle($params, $exerciseId) {
        if (array_key_exists('exercise_muscle_groups', $params)
            && is_array($params['exercise_muscle_groups'])
        ) {
            $userId = 1;
            $user = Zend_Auth::getInstance()->getIdentity();

            if (TRUE == is_object($user)) {
                $userId = $user->user_id;
            }

            $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
            $currentMusclesInDb = $exerciseXMuscleDb->findMusclesForExercise($exerciseId);
            $musclesCollection = [];
            $muscleGroupsInDb = [];

            foreach ($currentMusclesInDb as $muscle) {
                $muscleGroupsInDb[$muscle->offsetGet('muscle_group_id')] = $muscle->offsetGet('muscle_group_id');
                $musclesCollection[$muscle->exercise_x_muscle_muscle_fk] = $muscle;
            }

            foreach ($params['exercise_muscle_groups'] as $muscleGroup) {
                $muscleGroupId = $muscleGroup['id'];

                if (0 < $muscleGroupId
                    && is_array($muscleGroup)
                    && array_key_exists('muscles', $muscleGroup)
                    && is_array($muscleGroup['muscles'])
                ) {
                    foreach ($muscleGroup['muscles'] as $muscle) {
                        // wenn der aktuelle muskel bereits in uebungMuskeln eingetragen
                        if (true === array_key_exists($muscle['id'], $musclesCollection)) {
                            // checken ob der aktuelle muskel keine beanspruchung, dann löschen
                            if (TRUE === empty($muscle['muscle_use'])) {
                                $bResult = $exerciseXMuscleDb->deleteExerciseMuscle(
                                    $musclesCollection[$muscle['id']]->exercise_x_muscle_id
                                );
                                // wenn beanspruchung und != eingetragener, dann updaten
                            } else if ($musclesCollection[$muscle['id']]->exercise_x_muscle_muscle_use != $muscle['muscle_use']) {
                                $aData = array(
                                    'exercise_x_muscle_update_date' => date('Y-m-d H:i:s'),
                                    'exercise_x_muscle_update_user_fk' => $userId,
                                    'exercise_x_muscle_muscle_use' => $muscle['muscle_use']
                                );
                                $bResult = $exerciseXMuscleDb->updateExerciseMuscle(
                                    $aData,
                                    $musclesCollection[$muscle['id']]->exercise_x_muscle_id
                                );
                            }
                            // wenn es den muskel noch nicht gibt
                        } else if (false == array_key_exists($muscle['id'], $musclesCollection)
                            && false == empty($muscle['muscle_use'])
                        ) {
                            $aData = array(
                                'exercise_x_muscle_muscle_fk' => $muscle['id'],
                                'exercise_x_muscle_exercise_fk' => $exerciseId,
                                'exercise_x_muscle_create_date' => date('Y-m-d H:i:s'),
                                'exercise_x_muscle_create_user_fk' => $userId,
                                'exercise_x_muscle_muscle_use' => $muscle['muscle_use']
                            );
                            $exerciseXMuscleId = $exerciseXMuscleDb->saveExerciseMuscle($aData);
                        }
                    }
                    /** hier vielleicht noch checken, ob überhaupt ein muskel in der gruppe verarbeitet wurde */
                    unset($muscleGroupsInDb[$muscleGroupId]);
                }
            }

            foreach ($muscleGroupsInDb as $muscleGroupId => $dummy) {
                $exerciseXMuscleDb->deleteMusclesByMuscleGroupId($muscleGroupId);
            }
        }
    }

    /**
     * @param $params
     * @param $exerciseId
     */
    private function saveExerciseXDeviceOptions($params, $exerciseId)
    {
        if (array_key_exists('exercise_device_options', $params)
            && is_array($params['exercise_device_options'])
        ) {
            $userId = 1;
            $user = Zend_Auth::getInstance()->getIdentity();

            if (TRUE == is_object($user)) {
                $userId = $user->user_id;
            }

            $exerciseXDeviceOptionDb = new Model_DbTable_ExerciseXDeviceOption();
            $currentDeviceOptionsInDb = $exerciseXDeviceOptionDb->findDeviceOptionsForExercise($exerciseId);
            $deviceOptionsCollection = array();

            foreach ($currentDeviceOptionsInDb as $deviceOption) {
                $deviceOptionsCollection[$deviceOption->exercise_x_device_option_id] = $deviceOption;
            }

            foreach ($params['exercise_device_options'] as $deviceOption) {
                // wenn der aktuelle muskel bereits in uebungMuskeln eingetragen
                if (true === array_key_exists($deviceOption['exerciseXDeviceOptionId'], $deviceOptionsCollection)
//                    && ! empty($deviceOption['deviceXDeviceOptionId'])
                ) {
                    // checken ob der aktuelle muskel keine beanspruchung, dann löschen
                    if (TRUE === empty($deviceOption['value'])) {
                        $bResult = $exerciseXDeviceOptionDb->deleteDeviceOption($deviceOption['exerciseXDeviceOptionId']);
                        // wenn beanspruchung und != eingetragener, dann updaten
                    } else if ($deviceOptionsCollection[$deviceOption['exerciseXDeviceOptionId']]->exercise_x_device_option_device_option_value != $deviceOption['value']) {
                        $aData = array(
                            'exercise_x_device_option_device_option_fk' => $deviceOption['deviceOptionId'],
                            'exercise_x_device_option_device_option_value' => $deviceOption['value'],
                            'exercise_x_device_option_update_date' => date('Y-m-d H:i:s'),
                            'exercise_x_device_option_update_user_fk' => $userId,
                        );
                        $bResult = $exerciseXDeviceOptionDb->updateExerciseXDeviceOption(
                            $aData,
                            $deviceOption['exerciseXDeviceOptionId']
                        );
                    }
                    // wenn es den muskel noch nicht gibt
                } else if (false == array_key_exists($deviceOption['exerciseXDeviceOptionId'], $deviceOptionsCollection)
//                    && empty($deviceOption['deviceXDeviceOptionId'])
                    && false == empty($deviceOption['value'])
                ) {
                    $aData = array(
                        'exercise_x_device_option_device_option_fk' => $deviceOption['deviceOptionId'],
                        'exercise_x_device_option_exercise_fk' => $exerciseId,
                        'exercise_x_device_option_device_option_value' => $deviceOption['value'],
                        'exercise_x_device_option_create_date' => date('Y-m-d H:i:s'),
                        'exercise_x_device_option_create_user_fk' => $userId,
                    );
                    $exerciseXDeviceOptionId = $exerciseXDeviceOptionDb->saveExerciseXDeviceOption($aData);
                }
            }
        }
    }

    /**
     * @param $params
     * @param $exerciseId
     */
    private function saveExerciseXExerciseOptions($params, $exerciseId)
    {
        if (array_key_exists('exercise_options', $params)
            && is_array($params['exercise_options'])
        ) {
            $userId = 1;
            $user = Zend_Auth::getInstance()->getIdentity();

            if (TRUE == is_object($user)) {
                $userId = $user->user_id;
            }

            $exerciseXExerciseOptionDb = new Model_DbTable_ExerciseXExerciseOption();
            $currentExerciseOptionsInDb = $exerciseXExerciseOptionDb->findExerciseOptionsForExercise($exerciseId);
            $exerciseOptionsCollection = array();

            foreach ($currentExerciseOptionsInDb as $exerciseOption) {
                $exerciseOptionsCollection[$exerciseOption->exercise_x_exercise_option_id] = $exerciseOption;
            }

            foreach ($params['exercise_options'] as $exerciseOption) {
                // wenn der aktuelle muskel bereits in uebungMuskeln eingetragen
                if (true === array_key_exists($exerciseOption['exerciseXExerciseOptionId'], $exerciseOptionsCollection)) {
                    // checken ob der aktuelle muskel keine beanspruchung, dann löschen
                    if (true === empty($exerciseOption['value'])) {
                        $bResult = $exerciseXExerciseOptionDb->deleteExerciseXExerciseOption($exerciseOption['exerciseXExerciseOptionId']);
                        // wenn beanspruchung und != eingetragener, dann updaten
                    } else if ($exerciseOptionsCollection[$exerciseOption['exerciseXExerciseOptionId']]->exercise_x_exercise_option_exercise_option_value != $exerciseOption['value']) {
                        $aData = array(
                            'exercise_x_exercise_option_update_date' => date('Y-m-d H:i:s'),
                            'exercise_x_exercise_option_update_user_fk' => $userId,
                            'exercise_x_exercise_option_exercise_option_fk' => $exerciseOption['exerciseOptionId'],
                            'exercise_x_exercise_option_exercise_option_value' => $exerciseOption['value'],
                        );
                        $bResult = $exerciseXExerciseOptionDb->updateExerciseXExerciseOption(
                            $aData,
                            $exerciseOption['exerciseOptionId']
                        );
                    }
                    // wenn es den muskel noch nicht gibt
                } else if (false == array_key_exists($exerciseOption['exerciseXExerciseOptionId'], $exerciseOptionsCollection)
                    && false == empty($exerciseOption['value'])
                ) {
                    $aData = array(
                        'exercise_x_exercise_option_exercise_option_fk' => $exerciseOption['exerciseOptionId'],
                        'exercise_x_exercise_option_exercise_fk' => $exerciseId,
                        'exercise_x_exercise_option_exercise_option_value' => $exerciseOption['value'],
                        'exercise_x_exercise_option_create_date' => date('Y-m-d H:i:s'),
                        'exercise_x_exercise_option_create_user_fk' => $userId,
                    );
                    $exerciseXDeviceOptionId = $exerciseXExerciseOptionDb->saveExerciseXExerciseOption($aData);
                }
            }
        }
    }

    /**
     * @param $params
     * @param $exerciseId
     *
     * @return bool|int|mixed
     * @throws \Zend_Db_Table_Rowset_Exception
     */
    private function processExerciseXExerciseType($params, $exerciseId) {
        $exerciseXExerciseTypeDb = new Model_DbTable_ExerciseXExerciseType();
        $exerciseXExerciseType = $exerciseXExerciseTypeDb->findExerciseTypeForExercise($exerciseId);
        $exerciseTypeId = $params['exercise_type_id'];

        if (! empty($exerciseTypeId)
            && empty($exerciseXExerciseType)
        ) {
            $userId = 1;
            $user = Zend_Auth::getInstance()->getIdentity();

            if (true == is_object($user)) {
                $userId = $user->user_id;
            }

            $data = [
                'exercise_x_exercise_type_exercise_type_fk' => $exerciseTypeId,
                'exercise_x_exercise_type_exercise_fk' => $exerciseId,
                'exercise_x_exercise_type_create_date' => date('Y-m-d H:i:s'),
                'exercise_x_exercise_type_create_user_fk' => $userId,
            ];

            return $exerciseXExerciseTypeDb->saveExerciseXExerciseType($data);
        } else if (! empty($exerciseTypeId)
            && ! empty($exerciseXExerciseType)
        ) {
            $userId = 1;
            $user = Zend_Auth::getInstance()->getIdentity();

            if (true == is_object($user)) {
                $userId = $user->user_id;
            }

            $data = [
                'exercise_x_exercise_type_exercise_type_fk' => $exerciseTypeId,
                'exercise_x_exercise_type_exercise_fk' => $exerciseId,
                'exercise_x_exercise_type_update_date' => date('Y-m-d H:i:s'),
                'exercise_x_exercise_type_update_user_fk' => $userId,
            ];

            return $exerciseXExerciseTypeDb->updateExerciseXExerciseType($data, $exerciseXExerciseType->offsetGet('exercise_x_exercise_type_id'));
        } else if (empty($exerciseTypeId)
            && ! empty($exerciseXExerciseType)
        ) {
            return $exerciseXExerciseTypeDb->deleteExerciseXExerciseType($exerciseXExerciseType->offsetGet('exercise_x_exercise_type_id'));
        }
        return false;
    }

    /**
     * @param Zend_Db_Table_Row $deviceOption
     *
     * @return string
     */
    public function generateDeviceOptionEditContent($deviceOption) {

        $this->view->assign('device_option_value', $deviceOption->offsetGet('device_x_device_option_device_option_value'));
        $this->view->assign($deviceOption->toArray());
        return $this->view->render('loops/device-option-edit.phtml');
    }

    /**
     * @param Zend_Db_Table_Row $deviceOption
     *
     * @return string
     */
    public function generateExerciseOptionEditContent($deviceOption) {

        $this->view->assign('exercise_option_value', $deviceOption->offsetGet('device_x_device_option_device_option_value'));
        $this->view->assign($deviceOption->toArray());
        return $this->view->render('loops/exercise-option-edit.phtml');
    }

    /**
     * generates drop down from all possible options in database
     *
     * @return string
     */
    public function generateDeviceOptionsDropDownContent() {
        $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
        return $deviceOptionsService->generateDeviceOptionsSelectContent();
    }

    /**
     * generates drop down from all possible options in database
     *
     * @return string
     */
    public function generateExerciseOptionsDropDownContent() {
        $exerciseOptionsService = new Service_Generator_View_ExerciseOptions($this->view);
        return $exerciseOptionsService->generateExerciseOptionsSelectContent();
    }

    private function humanFileSize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}

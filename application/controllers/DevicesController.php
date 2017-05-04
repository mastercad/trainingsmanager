<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class DevicesController extends AbstractController {

    public function indexAction() {

        $devicesDb = new Model_DbTable_Devices();
        $devicesCollection = $devicesDb->findAllDevices();
        $devicesContent = 'Es konnten leider keine Geräte gefunden werden!';

        if (0 < count($devicesCollection)) {
            $devicesContent = '';
            foreach ($devicesCollection as $device) {
                $this->view->assign('name', $device->offsetGet('device_name'));
                $this->view->assign('id', $device->offsetGet('device_id'));
                $devicesContent .= $this->view->render('loops/item-row.phtml');
            }
        }
        $this->view->assign('devicesContent', $devicesContent);
    }

    public function showAction() {
        $id = intval($this->getParam('id'));

        if (0 < $id) {
            $devicesDb = new Model_DbTable_Devices();
            $device = $devicesDb->findDeviceById($id);

            if ($device instanceof Zend_Db_Table_Row) {
                $this->view->assign('preview', $this->generatePreviewPictureContent($device));
                $this->view->assign('detailOptionsContent', $this->generateDetailOptionsContent($id));
                $this->view->assign('optionLabelText', 'Geräte Optionen:');
                $this->view->assign('deviceOptionsContent', $this->generateDeviceOptionsContent($device));
                $this->view->assign('previewPictureContent', $this->generatePreviewPictureContent($device));
                $this->view->assign('name', $device->offsetGet('device_name'));
                $this->view->assign('id', $device->offsetGet('device_id'));
            }

        }
    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

        $deviceId = intval($this->getRequest()->getParam('id', null));
        $deviceContent = 'Das Gerät konnte leider nicht gefunden werden!';
        $device = null;

        if (0 < $deviceId) {
            $deviceContent = '';
            $deviceId = $params['id'];
            $devicesDb = new Model_DbTable_Devices();
            $device = $devicesDb->findDeviceById($deviceId);

            if ($device instanceof Zend_Db_Table_Row) {
                $this->view->assign('deviceOptionsContent', $this->generateDeviceOptionsEditContent($device));
                $this->view->assign($device->toArray());
            }
        }
        $this->view->assign('previewPicturesContent', $this->generatePreviewPicturesForEditContent($deviceId));
        $this->view->assign('previewPictureContent', $this->generatePreviewPictureForEditContent($device));
        $this->view->assign('deviceContent', $deviceContent);
        $this->view->assign('deviceOptionsDropDownContent', $this->generateDeviceOptionsDropDownContent());
    }

    private function generatePreviewPictureContent($device)
    {
        $this->view->assign('previewPictureId', 'device_preview_picture');
        $this->view->assign('previewPicturePath', $this->generatePreviewPicturePath($device));

        return $this->view->render('globals/preview-picture.phtml');
    }

    private function generatePreviewPictureForEditContent($device)
    {
        $this->view->assign('previewPictureId', 'exercise_preview_picture');
        $previewPicturePath = $this->generatePreviewPicturePath($device);
        $this->view->assign('dropZoneBackgroundImage', $previewPicturePath);

        return $this->view->render('loops/preview-picture-for-edit.phtml');
    }

    /**
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    private function generatePreviewPicturePath($device) {

        $previewPicturePath = '/images/content/statisch/grafiken/kein_bild.png';
        if ($device instanceof Zend_Db_Table_Row) {
            $picturePath = '/images/content/dynamisch/devices/' . $device->offsetGet('device_id') . '/';
            $tempPicturePath = '/tmp/devices/';

            if (0 < strlen(trim($device->offsetGet('device_preview_picture')))
                && file_exists(getcwd() . $picturePath . $device->offsetGet('device_preview_picture'))
                && is_file(getcwd() . $picturePath . $device->offsetGet('device_preview_picture'))
                && is_readable(getcwd() . $picturePath . $device->offsetGet('device_preview_picture'))
            ) {
                $previewPicturePath = $picturePath . $device->offsetGet('device_preview_picture');
            }
            $this->view->assign('picturePath', $picturePath);
            $this->view->assign('pictureTempPath', $tempPicturePath);
        }
        return $previewPicturePath;
    }

    /**
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    public function generateDeviceOptionsContent($device) {
        $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
        $deviceOptionsService->setDeviceId($device->offsetGet('device_id'));
        return $deviceOptionsService->generate();
    }

    /**
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    public function generateDeviceOptionsEditContent($device) {
        $deviceOptionsService = new Service_Generator_View_DeviceOptions($this->view);
        $deviceOptionsService->setDeviceId($device->offsetGet('device_id'));
        $deviceOptionsService->setAllowEdit(true);
        $deviceOptionsService->setConvertDropDownValues(false);
        $deviceOptionsService->setShowDelete(true);
        return $deviceOptionsService->generate();
    }

    /**
     * @param Zend_Db_Table_Row $exercise
     */
//    public function generateDeviceOptionsEditContent($exercise) {
//        $content = '';
//
//        foreach ($this->collectDeviceOptions($exercise) as $deviceOptionId => $deviceOption) {
//            $this->view->assign($deviceOption);
//            $this->view->assign('device_option_value',
//                $deviceOption['exercise_x_device_option_device_option_value'] ?
//                    $deviceOption['exercise_x_device_option_device_option_value'] :
//                    $deviceOption['device_x_device_option_device_option_value']);
//            $content .= $this->view->render('/loops/device-option-edit.phtml');
//        }
//        return $content;
//    }

    /**
     * generates drop down from all possible options in database
     *
     * @return string
     */
    public function generateDeviceOptionsDropDownContent() {
        $content = '';
        $deviceOptions = new Model_DbTable_DeviceOptions();
        $optionsCollection = $deviceOptions->findAllOptions();
        $this->view->assign('optionLabelText', 'Geräte Optionen:');
        $optionSelectText = $this->translate('label_please_select');

        foreach ($optionsCollection as $option) {
            $this->view->assign('optionValue', $option->offsetGet('device_option_id'));
            $this->view->assign('optionText', $option->offsetGet('device_option_name'));
            $content .= $this->view->render('loops/option.phtml');
        }

        $this->view->assign('optionSelectText', $optionSelectText);
        $this->view->assign('optionsContent', $content);
        $this->view->assign('selectId', 'device_options_select');
        $this->view->assign('optionDeleteShow', false);
        return $this->view->render('globals/select.phtml');
    }

    public function uploadPictureAction() {
        $this->view->layout()->disableLayout();
        $result = [];
        if (true === isset($_FILES['file'])) {
            $temp_bild_pfad = getcwd() . '/tmp/devices/';

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

    private function generatePreviewPicturesForEditContent($deviceId)
    {
        $previewPictureContent = '';
        $obj_files = new CAD_File();
        $obj_files->setSourcePath(getcwd() . '/tmp/devices');
        $obj_files->addSourcePath(getcwd() . "/images/content/dynamisch/devices/" . $deviceId);
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
        $deviceId = intval($this->getParam('deviceId'));
        $picture = base64_decode($this->getParam('id'));

        if (0 < $deviceId
            && $picture
        ) {
            $deleted = false;
            $exercisePicturePath = getcwd() . '/images/content/dynamisch/devices/'.$deviceId.'/'.$picture;
            $tempPicturePath = getcwd() . '/tmp/devices/'.$picture;

            if (true === is_readable($exercisePicturePath)
                && @unlink($exercisePicturePath)
            ) {
                Service_GlobalMessageHandler::appendMessage("Bild erfolgreich gelöscht!", Model_Entity_Message::STATUS_OK);
                $deleted = true;
            } else if (true === is_readable($tempPicturePath)
                && @unlink($tempPicturePath)
            ) {
                Service_GlobalMessageHandler::appendMessage("Bild erfolgreich gelöscht!", Model_Entity_Message::STATUS_OK);
                $deleted = true;
            }

            if (!$deleted) {
                Service_GlobalMessageHandler::appendMessage("Bild konnte nicht gelöscht werden!", Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage("Es wurde kein Bild übergeben!", Model_Entity_Message::STATUS_ERROR);
        }
    }

    public function getDevicesForEditAction() {
        $params = $this->getRequest()->getParams();

        if (isset($params['id'])) {
            $deviceGroupId = $params['id'];
            $deviceXDeviceGroupsDb = new Model_DbTable_DeviceXDeviceGroup();
            $devicesCollection = $deviceXDeviceGroupsDb->findDevicesByDeviceGroupId($deviceGroupId);

            $this->view->assign('a_geraete', $devicesCollection);
        }
    }

    public function getDeviceProposalsAction()
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['search']))
        {
            $search = base64_decode($params['search']) . '%';
            $devicesDb = new Model_DbTable_Devices();
            $devicesCollection = $devicesDb->findDeviceByName($search);

            $this->view->assign('deviceProposals', $devicesCollection);
        }
    }

    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        $messages = array();
        $userId = 1;

        $obj_user = Zend_Auth::getInstance()->getIdentity();
        if (TRUE === is_object($obj_user )) {
            $userId = $obj_user->user_id;
        }

        if ($this->getRequest()->isPost()) {
            $devicesDb = new Model_DbTable_Devices();
            $deviceXDeviceOptionDb = new Model_DbTable_DeviceXDeviceOption();
            $deviceName = '';
            $devicePreviewPicture = '';
            $deviceId = 0;
            $hasErrors = false;
            $data = array();
            $deviceXDeviceOptionUpdates = array();
            $deviceXDeviceOptionDeletes = array();
            $deviceXDeviceOptionInserts = array();

            if (isset($params['device_name']) &&
                0 < strlen(trim($params['device_name']))
            ) {
                $deviceName = base64_decode($params['device_name']);
            }

            if (isset($params['device_preview_picture']) &&
                0 < strlen(trim($params['device_preview_picture']))
            ) {
                $devicePreviewPicture = basename(base64_decode($params['device_preview_picture']));
            }

            if (isset($params['device_id'])) {
                $deviceId = $params['device_id'];
            }

            if (0 == strlen(trim($deviceName))
               && !$deviceId
            ) {
                Service_GlobalMessageHandler::appendMessage('Dieses Geraet benötigt einen Namen', Model_Entity_Message::STATUS_ERROR);
                $hasErrors = true;
            } else if(0 < strlen(trim($deviceName))) {
                $data['device_name'] = $deviceName;
            }

            if (0 < strlen(trim($devicePreviewPicture))) {
                $data['device_preview_picture'] = $devicePreviewPicture;
            }

            $cadSeo = new CAD_Seo();
            if (isset($params['device_options'])) {
                $deviceXDeviceOptionCurrent = array();

                if (0 < $deviceId) {
                    $currentDeviceOptionsForDeviceInDB = $deviceXDeviceOptionDb->findAllDeviceXDeviceOptionsByDeviceId($deviceId);
                    $countDeviceOptions = count($currentDeviceOptionsForDeviceInDB);

                    if ((is_array($currentDeviceOptionsForDeviceInDB)
                            || $currentDeviceOptionsForDeviceInDB instanceof Zend_Db_Table_Rowset)
                        && 0 < count($currentDeviceOptionsForDeviceInDB)
                    ) {
                        foreach ($currentDeviceOptionsForDeviceInDB as $deviceOption) {
                            // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                            $deviceXDeviceOptionCurrent[$deviceOption['device_x_device_option_device_option_fk']] = array(
                                'device_x_device_option_id' => $deviceOption['device_x_device_option_id'],
                                'device_option_id' => $deviceOption['device_x_device_option_device_option_fk'],
                            );
                        }
                    }
                }

                foreach ($params['device_options'] as $deviceOption) {

                    // es wurde eine id übergeben und diese id bestand bereits
                    if (isset($deviceOption['id'])
                        && 0 < $deviceOption['id']
                        && isset($deviceXDeviceOptionCurrent[$deviceOption['id']])
                    ) {
                        array_push($deviceXDeviceOptionUpdates, array(
                                'device_x_device_option_device_option_fk' => $deviceOption['id'],
                                'device_x_device_option_device_option_value' => base64_decode($deviceOption['value']),
                                'device_x_device_option_id' => $deviceXDeviceOptionCurrent[$deviceOption['id']]['device_x_device_option_id']
                            )
                        );
                        unset($deviceXDeviceOptionCurrent[$deviceOption['id']]);
                    } else if (isset($deviceOption['id'])
                        && 0 < $deviceOption['id']
                        && ! isset($deviceXDeviceOptionCurrent[$deviceOption['id']])
                    ) {
                        array_push($deviceXDeviceOptionInserts, array(
                                'device_option_id' => $deviceOption['id'],
                                'device_option_value' => base64_decode($deviceOption['value'])
                            )
                        );
                    }
                }
                foreach ($deviceXDeviceOptionCurrent as $deviceOption) {
                    array_push($deviceXDeviceOptionDeletes, $deviceOption['device_x_device_option_id']);
                }
            }

            if  (!$deviceId
                && strlen(trim($deviceName)))
            {
                $deviceCurrent = $devicesDb->findDeviceByName($deviceName);
                if (is_array($deviceCurrent)
                    && 0 < count($deviceCurrent)
                ) {
                    Service_GlobalMessageHandler::appendMessage('Geraet existiert bereits!', Model_Entity_Message::STATUS_ERROR);
                    $hasErrors = true;
                }
            }

            if (!$hasErrors) {
                // updaten?
                if(is_numeric($deviceId)
                    && 0 < $deviceId
                    && 0 < count($data)
                ) {
                    $deviceCurrent = $devicesDb->findDeviceById($deviceId);
                    if(
                        (
                            isset($data['device_name'])
                            && 0 < strlen(trim($data['device_name']))
                            && $deviceCurrent['device_name'] != $data['device_name']
                        ) ||
                        (
                            isset($deviceCurrent['device_name'])
                            && 0 < strlen(trim($deviceCurrent['device_name']))
                            && !strlen(trim($deviceCurrent['device_name']))
                        )
                    )
                    {
                        if (isset($data['device_name'])
                            && 0 < strlen(trim($data['device_name']))
                        ) {
                            $deviceName = $data['device_name'];
                        } else if (isset($deviceCurrent['device_name'])
                            && 0 < strlen(trim($deviceCurrent['device_name']))
                        ) {
                            $deviceName = $deviceCurrent['device_name'];
                        }
                        $cadSeo->setLinkName($deviceName);
                        $cadSeo->setDbTable($devicesDb);
                        $cadSeo->setTableFieldName("device_seo_link");
                        $cadSeo->setTableFieldIdName("device_id");
                        $cadSeo->setTableFieldId($deviceId);
                        $cadSeo->createSeoLink();
                        $data['device_seo_link'] = $cadSeo->getSeoName();
                    }
                    $data['device_update_date'] = date("Y-m-d H:i:s");
                    $data['device_update_user_fk'] = $userId;

                    $devicesDb->updateDevice($data, $deviceId);
                    Service_GlobalMessageHandler::appendMessage('Dieses Gerät wurde erfolgreich bearbeitet!', Model_Entity_Message::STATUS_OK);
                // neues gerät anlegen
                } else if (0 < count($data)) {
                    $cadSeo->setLinkName($data['device_name']);
                    $cadSeo->setDbTable($devicesDb);
                    $cadSeo->setTableFieldName("device_seo_link");
                    $cadSeo->setTableFieldIdName("device_id");
                    $cadSeo->setTableFieldId($deviceId);
                    $cadSeo->createSeoLink();

                    $data['device_seo_link'] = $cadSeo->getSeoName();
                    $data['device_create_date'] = date("Y-m-d H:i:s");
                    $data['device_create_user_fk'] = $userId;

                    $deviceId = $devicesDb->saveDevice($data);
                    Service_GlobalMessageHandler::appendMessage('Dieses Gerät wurde erfolgreich angelegt!', Model_Entity_Message::STATUS_OK);
                } else {
                    Service_GlobalMessageHandler::appendMessage('Dieses Gerät wurde nicht geändert!', Model_Entity_Message::STATUS_ERROR);
                }

                if ($deviceId) {
                    /* bilder verschieben */
                    $cadFile = new CAD_File();
                    $sourcePath = getcwd() . '/tmp/devices/';
                    $destinationPath = getcwd() . '/images/content/dynamisch/devices/' . $deviceId . '/';

                    if($cadFile->checkAndCreateDir($destinationPath))
                    {
                        $cadFile->setSourcePath($sourcePath);
                        $cadFile->setDestPath($destinationPath);
                        $cadFile->setAllowedExtensions(array('jpg', 'png', 'gif', 'svg'));
                        $cadFile->verschiebeFiles();
                    }
                    /* geräte optionen anlegen / updaten */

                    if (0 === count($deviceXDeviceOptionInserts)
                        && 0 === count($deviceXDeviceOptionUpdates)
                        && 0 === count($deviceXDeviceOptionDeletes)
                    ) {
                        array_push($messages, array(
                            'type' => 'meldung', 'message' => 'Das Gerät wurde nicht geändert!', 'result' => true,
                            'id' => $deviceId
                        ));
                    } else if ($deviceId) {

                        foreach ($deviceXDeviceOptionInserts as $deviceXDeviceOption) {
                            $data = array();
                            $data['device_x_device_option_device_option_fk'] = $deviceXDeviceOption['device_option_id'];
                            $data['device_x_device_option_device_option_value'] = $deviceXDeviceOption['device_option_value'];
                            $data['device_x_device_option_device_fk'] = $deviceId;
                            $data['device_x_device_option_create_date'] = date("Y-m-d H:i:s");
                            $data['device_x_device_option_create_user_fk'] = $userId;

                            $deviceXDeviceOptionDb->saveDeviceXDeviceOption($data);
                        }

                        foreach ($deviceXDeviceOptionUpdates as $deviceXDeviceOption) {
                            $data = array();
                            $data['device_x_device_option_device_fk'] = $deviceId;
                            $data['device_x_device_option_device_option_fk'] = $deviceXDeviceOption['device_x_device_option_device_option_fk'];
                            $data['device_x_device_option_device_option_value'] = $deviceXDeviceOption['device_x_device_option_device_option_value'];
                            $data['device_x_device_option_device_fk'] = $deviceId;
                            $data['device_x_device_option_update_date'] = date("Y-m-d H:i:s");
                            $data['device_x_device_option_update_user_fk'] = $userId;

                            $deviceXDeviceOptionDb->updateDeviceXDeviceOption($data,
                                $deviceXDeviceOption['device_x_device_option_id']);
                        }

                        foreach ($deviceXDeviceOptionDeletes as $deviceXDeviceOptionId) {
                            $deviceXDeviceOptionDb->deleteDeviceXDeviceOption($deviceXDeviceOptionId);
                        }

                        if (0 < count($deviceXDeviceOptionInserts)
                            || 0 < count($deviceXDeviceOptionUpdates)
                            || 0 < count($deviceXDeviceOptionDeletes)
                        ) {
                            Service_GlobalMessageHandler::appendMessage('Die Optionen des Gerätes wurden erfolgreich geändert!', Model_Entity_Message::STATUS_OK);
                        }
                    }

                }
            } else {
                Service_GlobalMessageHandler::appendMessage('Beim Speichern des Gerätes trat ein unbekannter Fehler auf!', Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage('Falscher Aufruf von Gerät speichern!', Model_Entity_Message::STATUS_ERROR);
        }
    }

    /**
     * @TODO hier muss die logik für das löschen noch einmal validiert werden, es müssen auch übungen zu dem gerät,
     * sowie die geräteoptionen gelöscht werden, außerdem die gerätegruppe(n) und die training-plans einträge der
     * übungen mit dem gerät, hier werden aber sowohl trainingspläne als auch trainingstagebücher inkonsistent
     * hier müsste man also noch ein archiv anlegen, in das solche übungen dann verschieben werden, eventuell
     * auch geräte, damit man die pläne und tagebucheinträge trotzdem noch sauber ansehen kann
     */
    public function deleteAction()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['id'])
            && is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $deviceId = $params['id'];
            $hasErrors = false;

            $devicesDb = new Model_DbTable_Devices();
            $deviceGroupsDb = new Model_DbTable_DeviceGroups();
            $deviceXDeviceGroupDb = new Model_DbTable_DeviceXDeviceGroup();
            $exercisesDb = new Model_DbTable_Exercises();
            $exerciseXDeviceDb = new Model_DbTable_ExerciseXDevice();

            if ($devicesDb->deleteDevice($deviceId)) {
                Service_GlobalMessageHandler::appendMessage('Geraet erfolgreich gelöscht!', Model_Entity_Message::STATUS_OK);
            } else {
                Service_GlobalMessageHandler::appendMessage('Geraet konnte leider nicht gelöscht werden!', Model_Entity_Message::STATUS_ERROR);
                $hasErrors = true;
            }

            // übungen für das gerät löschen
            $exercisesCollection = $exerciseXDeviceDb->findExercisesForDevice($deviceId);

            if (is_array($exercisesCollection)
                && 0 < count($exercisesCollection)
                && !$hasErrors
            ) {
                foreach ($exercisesCollection as $exercise) {
                    $exercisesDb->deleteExercise($exercise['exercise_x_device_exercise_fk']);
                }
            }

            $deviceXDeviceGroupDb->deleteAllDeviceGroupDevicesByDeviceId($deviceId);
        } else {
            Service_GlobalMessageHandler::appendMessage('Falscher Aufruf!', Model_Entity_Message::STATUS_ERROR);
        }
    }

    public function getDeviceOptionEditAction() {
        $deviceOptionId = intval($this->getRequest()->getParam('device_option_id', 0));
        $deviceOptionsContent = '';

        if (0 < $deviceOptionId) {
            $deviceOptionsDb = new Model_DbTable_DeviceOptions();
            $deviceOption = $deviceOptionsDb->findOptionById($deviceOptionId);

            $this->view->assign('device_option_id', $deviceOption->offsetGet('device_option_id'));
            $this->view->assign('device_option_name', $deviceOption->offsetGet('device_option_name'));
            $this->view->assign('device_option_value', $deviceOption->offsetGet('device_option_default_value'));
            $deviceOptionsContent = $this->view->render('loops/device-option-edit.phtml');
        }
        $this->view->assign('deviceOptionsContent', $deviceOptionsContent);
    }

    private function humanFileSize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}

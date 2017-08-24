<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

use Model\Entity\Message;
use Service\GlobalMessageHandler;
use Model\DbTable\Devices;
use Model\DbTable\DeviceOptions;
use Service\Generator\View\DeviceOptions AS DeviceOptionsViewService;
use Service\Generator\Thumbnail;
use Model\DbTable\DeviceXDeviceGroup;
use Model\DbTable\DeviceXDeviceOption;
use Model\DbTable\Exercises;
use Model\DbTable\ExerciseXDevice;

class DevicesController extends AbstractController
{

    /**
     * initial function for controller
     */
    public function init() 
    {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile(
                $this->view->baseUrl() . '/js/trainingsmanager_accordion.js',
                'text/javascript'
            );
            $this->view->headScript()->appendFile(
                $this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript'
            );
        }
    }

    /**
     * index action
     */
    public function indexAction() 
    {

        $devicesDb = new Devices();
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

    /**
     * show action
     */
    public function showAction() 
    {
        $id = intval($this->getParam('id'));

        if (0 < $id) {
            $devicesDb = new Devices();
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

    /**
     * new action
     */
    public function newAction() 
    {
        $this->forward('edit');
    }

    /**
     * edit action
     */
    public function editAction() 
    {
        $params = $this->getRequest()->getParams();

        $deviceId = intval($this->getRequest()->getParam('id', null));
        $deviceContent = 'Das Gerät konnte leider nicht gefunden werden!';
        $device = null;

        if (0 < $deviceId) {
            $deviceContent = '';
            $deviceId = $params['id'];
            $devicesDb = new Devices();
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

    /**
     * generates preview picture content
     *
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    private function generatePreviewPictureContent($device)
    {
        $this->view->assign('previewPictureId', 'device_preview_picture');
        $this->view->assign('previewPicturePath', $this->generatePreviewPicturePath($device));

        return $this->view->render('globals/preview-picture.phtml');
    }

    /**
     * generate preview picture content for edit
     *
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    private function generatePreviewPictureForEditContent($device)
    {
        $this->view->assign('previewPictureId', 'exercise_preview_picture');
        $previewPicturePath = $this->generatePreviewPicturePath($device);
        $this->view->assign('dropZoneBackgroundImage', $previewPicturePath);

        return $this->view->render('loops/preview-picture-for-edit.phtml');
    }

    /**
     * generate preview picture path
     *
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    private function generatePreviewPicturePath($device) 
    {

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
    public function generateDeviceOptionsContent($device) 
    {
        $deviceOptionsService = new DeviceOptionsViewService($this->view);
        $deviceOptionsService->setDeviceId($device->offsetGet('device_id'));
        return $deviceOptionsService->generate();
    }

    /**
     * generate device options content for edit
     *
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    public function generateDeviceOptionsEditContent($device) 
    {
        $deviceOptionsService = new DeviceOptionsViewService($this->view);
        $deviceOptionsService->setDeviceId($device->offsetGet('device_id'));
        $deviceOptionsService->setAllowEdit(true);
        $deviceOptionsService->setConvertDropDownValues(false);
        $deviceOptionsService->setShowDelete(true);
        return $deviceOptionsService->generate();
    }

    /**
     * generates drop down from all possible options in database
     *
     * @return string
     */
    public function generateDeviceOptionsDropDownContent() 
    {
        $content = '';
        $deviceOptions = new DeviceOptions();
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

    /**
     * upload picture action
     */
    public function uploadPictureAction() 
    {
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

    /**
     * generate preview pictures content for edit
     *
     * @param int $deviceId
     *
     * @return string
     */
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
            $thumbnailService = new Thumbnail();
            $thumbnailService->setSourceFilePathName($sysPath);
            $thumbnailService->setThumbHeight(120);
            $thumbnailService->setThumbWidth(120);
            $this->view->assign('templateDisplayType', 'inline-block');
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

    /**
     * delete picture action
     */
    public function deletePictureAction() 
    {
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
                GlobalMessageHandler::appendMessage("Bild erfolgreich gelöscht!", Message::STATUS_OK);
                $deleted = true;
            } else if (true === is_readable($tempPicturePath)
                && @unlink($tempPicturePath)
            ) {
                GlobalMessageHandler::appendMessage("Bild erfolgreich gelöscht!", Message::STATUS_OK);
                $deleted = true;
            }

            if (!$deleted) {
                GlobalMessageHandler::appendMessage("Bild konnte nicht gelöscht werden!", Message::STATUS_ERROR);
            }
        } else {
            GlobalMessageHandler::appendMessage("Es wurde kein Bild übergeben!", Message::STATUS_ERROR);
        }
    }

    /**
     * get devices for edit action
     */
    public function getDevicesForEditAction() 
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['id'])) {
            $deviceGroupId = $params['id'];
            $deviceXDeviceGroupsDb = new DeviceXDeviceGroup();
            $devicesCollection = $deviceXDeviceGroupsDb->findDevicesByDeviceGroupId($deviceGroupId);

            $this->view->assign('a_geraete', $devicesCollection);
        }
    }

    /**
     * get device proposals action
     */
    public function getDeviceProposalsAction()
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['search'])) {
            $search = base64_decode($params['search']) . '%';
            $devicesDb = new Devices();
            $devicesCollection = $devicesDb->findDeviceByName($search);

            $this->view->assign('deviceProposals', $devicesCollection);
        }
    }

    /**
     * save action
     */
    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        $messages = array();
        $userId = $this->findCurrentUserId();

        if ($this->getRequest()->isPost()) {
            $devicesDb = new Devices();
            $deviceXDeviceOptionDb = new DeviceXDeviceOption();
            $deviceName = '';
            $devicePreviewPicture = '';
            $deviceId = 0;
            $hasErrors = false;
            $data = array();
            $deviceXDeviceOptionUpdates = array();
            $deviceXDeviceOptionDeletes = array();
            $deviceXDeviceOptionInserts = array();

            if (isset($params['device_name']) 
                && 0 < strlen(trim($params['device_name']))
            ) {
                $deviceName = base64_decode($params['device_name']);
            }

            if (isset($params['device_preview_picture']) 
                && 0 < strlen(trim($params['device_preview_picture']))
            ) {
                $devicePreviewPicture = basename(base64_decode($params['device_preview_picture']));
            }

            if (isset($params['device_id'])) {
                $deviceId = $params['device_id'];
            }

            if (0 == strlen(trim($deviceName))
                && !$deviceId
            ) {
                GlobalMessageHandler::appendMessage('Dieses Geraet benötigt einen Namen', Message::STATUS_ERROR);
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
                        array_push(
                            $deviceXDeviceOptionUpdates, array(
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
                        array_push(
                            $deviceXDeviceOptionInserts, array(
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
                && strlen(trim($deviceName))
            ) {
                $deviceCurrent = $devicesDb->findDeviceByName($deviceName);
                if (is_array($deviceCurrent)
                    && 0 < count($deviceCurrent)
                ) {
                    GlobalMessageHandler::appendMessage('Geraet existiert bereits!', Message::STATUS_ERROR);
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
                    if((                        isset($data['device_name'])
                        && 0 < strlen(trim($data['device_name']))
                        && $deviceCurrent['device_name'] != $data['device_name']) 
                        || (                        isset($deviceCurrent['device_name'])
                        && 0 < strlen(trim($deviceCurrent['device_name']))
                        && !strlen(trim($deviceCurrent['device_name'])))
                    ) {
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
                    GlobalMessageHandler::appendMessage('Dieses Gerät wurde erfolgreich bearbeitet!', Message::STATUS_OK);
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
                    GlobalMessageHandler::appendMessage('Dieses Gerät wurde erfolgreich angelegt!', Message::STATUS_OK);
                } else {
                    GlobalMessageHandler::appendMessage('Dieses Gerät wurde nicht geändert!', Message::STATUS_ERROR);
                }

                if ($deviceId) {
                    /* bilder verschieben */
                    $cadFile = new CAD_File();
                    $sourcePath = getcwd() . '/tmp/devices/';
                    $destinationPath = getcwd() . '/images/content/dynamisch/devices/' . $deviceId . '/';

                    if($cadFile->checkAndCreateDir($destinationPath)) {
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
                        array_push(
                            $messages, array(
                            'type' => 'meldung', 'message' => 'Das Gerät wurde nicht geändert!', 'result' => true,
                            'id' => $deviceId
                            )
                        );
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

                            $deviceXDeviceOptionDb->updateDeviceXDeviceOption(
                                $data,
                                $deviceXDeviceOption['device_x_device_option_id']
                            );
                        }

                        foreach ($deviceXDeviceOptionDeletes as $deviceXDeviceOptionId) {
                            $deviceXDeviceOptionDb->deleteDeviceXDeviceOption($deviceXDeviceOptionId);
                        }

                        if (0 < count($deviceXDeviceOptionInserts)
                            || 0 < count($deviceXDeviceOptionUpdates)
                            || 0 < count($deviceXDeviceOptionDeletes)
                        ) {
                            GlobalMessageHandler::appendMessage('Die Optionen des Gerätes wurden erfolgreich geändert!', Message::STATUS_OK);
                        }
                    }

                }
            } else {
                GlobalMessageHandler::appendMessage('Beim Speichern des Gerätes trat ein unbekannter Fehler auf!', Message::STATUS_ERROR);
            }
        } else {
            GlobalMessageHandler::appendMessage('Falscher Aufruf von Gerät speichern!', Message::STATUS_ERROR);
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

            $devicesDb = new Devices();
            $deviceXDeviceGroupDb = new DeviceXDeviceGroup();
            $exercisesDb = new Exercises();
            $exerciseXDeviceDb = new ExerciseXDevice();

            if ($devicesDb->deleteDevice($deviceId)) {
                GlobalMessageHandler::appendMessage('Geraet erfolgreich gelöscht!', Message::STATUS_OK);
            } else {
                GlobalMessageHandler::appendMessage('Geraet konnte leider nicht gelöscht werden!', Message::STATUS_ERROR);
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
            GlobalMessageHandler::appendMessage('Falscher Aufruf!', Message::STATUS_ERROR);
        }
    }

    /**
     * get device options for edit action
     */
    public function getDeviceOptionEditAction() 
    {
        $deviceOptionId = intval($this->getRequest()->getParam('device_option_id', 0));
        $deviceOptionsContent = '';

        if (0 < $deviceOptionId) {
            $deviceOptionsDb = new DeviceOptions();
            $deviceOption = $deviceOptionsDb->findOptionById($deviceOptionId);

            $this->view->assign('device_option_id', $deviceOption->offsetGet('device_option_id'));
            $this->view->assign('device_option_name', $deviceOption->offsetGet('device_option_name'));
            $this->view->assign('device_option_value', $deviceOption->offsetGet('device_option_default_value'));
            $deviceOptionsContent = $this->view->render('loops/device-option-edit.phtml');
        }
        $this->view->assign('deviceOptionsContent', $deviceOptionsContent);
    }

    /**
     * formats given bytes in human readable number
     *
     * @param $bytes
     * @param int   $decimals
     *
     * @return string
     */
    private function humanFileSize($bytes, $decimals = 2) 
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}

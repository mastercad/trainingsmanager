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

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

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
                $this->view->assign('previewPictureContent', $this->generatePreviewPicturesForEditContent());
                $this->view->assign('name', $device->offsetGet('device_name'));
                $this->view->assign('id', $device->offsetGet('device_id'));
            }

        }
    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');
        }

        $deviceContent = 'Das Gerät konnte leider nicht gefunden werden!';

        if (true === isset($params['id'])
            && true === is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $deviceContent = '';
            $deviceId = $params['id'];
            $devicesDb = new Model_DbTable_Devices();
            $device = $devicesDb->findDeviceById($deviceId);

            if ($device instanceof Zend_Db_Table_Row) {
                $this->view->assign('preview', $this->generatePreviewPictureContent($device));
                $this->view->assign('deviceOptionsContent', $this->generateDeviceOptionsEditContent($device));
                $this->view->assign('previewPictureContent', $this->generatePreviewPicturesForEditContent());
                $this->view->assign($device->toArray());
            }

        }
        $this->view->assign('deviceContent', $deviceContent);
        $this->view->assign('deviceOptionsDropDownContent', $this->generateDeviceOptionsDropDownContent());
    }

    /**
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    public function generatePreviewPictureContent($device) {
        $preview = '/images/content/statisch/grafiken/kein_bild.png';

        if (0 < strlen(trim($device->offsetGet('device_preview_picture')))
            && file_exists(getcwd() . $picturePath . $device->offsetGet('device_preview_picture'))
            && is_file(getcwd() . $picturePath . $device->offsetGet('device_preview_picture'))
            && is_readable(getcwd() . $picturePath . $device->offsetGet('device_preview_picture'))
        ) {
            $preview = $picturePath . $device->offsetGet('device_preview_picture');
        }
        return $preview;
    }

    /**
     * @param Zend_Db_Table_Row $device
     *
     * @return string
     */
    public function generateDeviceOptionsContent($device) {

        //        $deviceOptionsDb = new Model_DbTable_DeviceOptions();
        //        $deviceOptions = $deviceOptionsDb->findDeviceOptionsByDeviceId($device->offsetGet('device_id'));
        //        $deviceOptionsContent = '';

        /** generates option inputs for device **/
        //        foreach ($deviceOptions as $deviceOption) {
        //            if (0 < $deviceOption->offsetGet('device_x_device_option_device_option_fk')) {
        //                $deviceOptionsContent .= $this->generateDeviceOptionEditContent($deviceOption);
        //            }
        //        }

        //        return $deviceOptionsContent;

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
        return $deviceOptionsService->generate();
    }

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
        return $this->view->render('globals/select.phtml');
    }

    /**
     * @param Zend_Db_Table_Row $deviceOption
     *
     * @return string
     */
    private function generateDeviceOptionContent($deviceOption) {

        $optionValue = $deviceOption->offsetGet('device_x_device_option_device_option_value');

        if (preg_match('/\|/', $optionValue)) {
            $optionValue = explode('|', $optionValue);
        }
        $this->view->assign('optionValue', $optionValue);
        $this->view->assign($deviceOption->toArray());
        return $this->view->render('loops/device-option.phtml');
    }

    /**
     * @param Zend_Db_Table_Row $deviceOption
     *
     * @return string
     */
    private function generateDeviceOptionEditContent($deviceOption) {

        $this->view->assign('device_option_value', $deviceOption->offsetGet('device_x_device_option_device_option_value'));
        $this->view->assign($deviceOption->toArray());
        return $this->view->render('loops/device-option-edit.phtml');
    }

    public function uploadPictureAction() {

        if (true === isset($_FILES['cad-cms-image-file'])) {
            $temp_bild_pfad = getcwd() . '/tmp/devices/';

            $obj_file = new CAD_File();
            $obj_file->setDestPath($temp_bild_pfad);
            $obj_file->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'svg', 'gif'));
            $obj_file->setUploadedFiles($_FILES['cad-cms-image-file']);
            $obj_file->moveUploadedFiles();

            $a_files = $obj_file->getDestFiles();

            if (true === isset($a_files[0][CAD_FILE::HTML_PFAD])) {
                $a_bild_pfad = array();
                $a_bild_pfad['html_pfad'] = $a_files[0][CAD_FILE::HTML_PFAD];
                $a_bild_pfad['sys_pfad'] = $a_files[0][CAD_FILE::SYS_PFAD];
                $a_bild_pfad['file'] = $a_files[0][CAD_FILE::FILE];

                $this->view->assign('picturePaths', json_encode($a_bild_pfad));
            }
        }
    }

    /**
     * function um eine übersicht aller bilder des jeweiligen editierten
     * projektes zurück zu erhalten und es formatiert auszugeben
     */
    public function getPicturesForEditAction() {
        $this->view->assign('previewPictureContent', $this->generatePreviewPicturesForEditContent());
    }

    private function generatePreviewPicturesForEditContent()
    {
        $req = $this->getRequest();
        $params = $req->getParams();

        $a_bilder = null;
        $obj_files = new CAD_File();

        $obj_files->setSourcePath(getcwd() . '/tmp/devices');

        /**
         * wenn es eine ID des projektes gibt, bilder aus dem projektordner
         * holen und temp checken
         */
        if (true === isset($params['id'])) {
            $obj_files->addSourcePath(getcwd() . "/images/content/dynamisch/devices/" . $params['id']);
        }
        $obj_files->holeBilderAusPfad();
        $a_bilder = $obj_files->getDestFiles();
        $previewPictureContent = '';

        foreach ($a_bilder as $bild) {
            $this->view->assign('picture', $bild);
            $previewPictureContent .= $this->view->render('loops/preview-picture-for-edit.phtml');
        }

        return $previewPictureContent;
    }

    public function deletePictureAction() {
        $req = $this->getRequest();
        $params = $req->getParams();

        if (true ===isset($params['bild'])) {
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

        if (isset($params['edited_elements'])) {
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

            if (isset($params['edited_elements']['device_name']) &&
                0 < strlen(trim($params['edited_elements']['device_name']))
            ) {
                $deviceName = base64_decode($params['edited_elements']['device_name']);
            }

            if (isset($params['edited_elements']['device_preview_picture']) &&
                0 < strlen(trim($params['edited_elements']['device_preview_picture']))
            ) {
                $devicePreviewPicture = base64_decode($params['edited_elements']['device_preview_picture']);
            }

            if (isset($params['edited_elements']['device_id'])) {
                $deviceId = $params['edited_elements']['device_id'];
            }

            if (0 == strlen(trim($deviceName))
               && !$deviceId
            ) {
                array_push($messages, array('type' => 'fehler', 'message' => 'Dieses Geraet benötigt einen Namen'));
                $hasErrors = true;
            } else if(0 < strlen(trim($deviceName))) {
                $data['device_name'] = $deviceName;
            }

            if (0 < strlen(trim($devicePreviewPicture))) {
                $data['device_preview_picture'] = $devicePreviewPicture;
            }

            $cadSeo = new CAD_Seo();
            if (isset($params['edited_elements']['device_options'])) {
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

                foreach ($params['edited_elements']['device_options'] as $deviceOption) {

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
                    array_push($messages, array('type' => 'fehler', 'message' => 'Geraet existiert bereits!', 'result' => false));
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
                    array_push($messages, array('type' => 'meldung', 'message' => 'Dieses Gerät wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $deviceId));
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
                    array_push($messages, array('type' => 'meldung', 'message' => 'Dieses Gerät wurde erfolgreich angelegt!', 'result' => true, 'id' => $deviceId));
                } else {
                    array_push($messages, array('type' => 'warnung', 'message' => 'Dieses Gerät wurde nicht geändert!', 'result' => true, 'id' => $deviceId));
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
                            array_push($messages, array(
                                'type' => 'meldung', 'message' => 'Die Optionen des Gerätes wurden erfolgreich geändert!',
                                'result' => true, 'id' => $deviceId
                            ));
                        }
                    }

                }
            } else {
                array_push($messages, array('type' => 'fehler', 'message' => 'Beim Speichern des Gerätes trat ein unbekannter Fehler auf!', 'result' => false, 'id' => $deviceId));
            }
        } else {
            array_push($messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Gerät speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($messages));
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
        $messages = array();

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
                array_push($messages, array('type' => 'meldung', 'message' => 'Geraet erfolgreich gelöscht!', 'result' => true));
            } else {
                array_push($messages, array('type' => 'fehler', 'message' => 'Geraet konnte leider nicht gelöscht werden!', 'result' => false));
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
            array_push($messages, array('type' => 'meldung', 'message' => 'Geraet konnte leider nicht gelöscht werden! (Falsche Übergabe!)', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($messages));
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
}

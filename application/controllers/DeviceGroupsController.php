<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class DeviceGroupsController extends AbstractController
{
    public function indexAction() {
        $deviceGroupsDb = new Model_DbTable_DeviceGroups();
        $deviceXDeviceGroupDb = new Model_DbTable_DeviceXDeviceGroup();

        $deviceGroupsCollection = $deviceGroupsDb->findAllDeviceGroups()->toArray();

        foreach($deviceGroupsCollection as &$a_geraetegruppe) {
            $a_geraetegruppe_geraete = $deviceXDeviceGroupDb->findDevicesByDeviceGroupId($a_geraetegruppe['device_group_id']);
            $a_geraetegruppe['a_geraetegruppe_geraete'] = $a_geraetegruppe_geraete;
        }
        $this->view->assign('a_geraetegruppen', $deviceGroupsCollection);
    }

    public function showAction()
    {

    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

        if(isset($params['id']) &&
            is_numeric($params['id']) &&
            $params['id'] > 0)
        {
            $i_geraetegruppe_id = $params['id'];

            $obj_db_geraetegruppen = new Model_DbTable_DeviceGroups();
            $obj_db_geraetegruppe_geraete = new Model_DbTable_DeviceXDeviceGroup();

            $a_geraetegruppe = $obj_db_geraetegruppen->findDeviceGroup($i_geraetegruppe_id);
            $devicesCollection = $obj_db_geraetegruppe_geraete->findDevicesByDeviceGroupId($i_geraetegruppe_id);

            $deviceGroupDevicesContent = '';

            foreach ($devicesCollection as $device) {
                $this->view->assign($device->toArray());
                $deviceGroupDevicesContent .= $this->view->render('/loops/device-group-device-edit.phtml');
            }

            $this->view->assign('deviceGroupDevicesContent', $deviceGroupDevicesContent);
            $this->view->assign($a_geraetegruppe->toArray());
        }
    }

    public function deleteDeviceGroupAction() {
        $params = $this->getRequest()->getParams();
        $messages = array();

        if (isset($params['id'])
           && is_numeric($params['id'])
           && 0 < $params['id']
        ) {
            $i_geraetegruppe_id = $params['id'];

            $obj_db_geraetegruppen = new Model_DbTable_DeviceGroups();
            $obj_db_geraetegruppen_geraete = new Model_DbTable_DeviceXDeviceGroup();
            $a_geraete = $obj_db_geraetegruppen_geraete->findDevicesByDeviceGroupId($i_geraetegruppe_id);

            if ($obj_db_geraetegruppen->deleteDeviceGroup($i_geraetegruppe_id)) {
                if (is_array($a_geraete)
                    && 0 < count($a_geraete)
                ) {
                    foreach ($a_geraete as $a_geraet) {
//                        $a_uebungen = $obj_db_uebungen->findExerciseForDevice($a_geraet['geraet_id']);
//                        if(is_array($a_uebungen) &&
//                           count($a_uebungen) > 0)
//                        {
//                            foreach($a_uebungen as $a_uebung)
//                            {
//                                $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppeFuerUebung($a_uebung['uebung_id']);
//                                $obj_db_uebungen->deleteExercise($a_uebung['uebung_id']);
//                            }
//                        }
                    }
                }

                $obj_db_geraetegruppen_geraete->deleteAllDeviceGroupDevicesByDeviceGroupId($i_geraetegruppe_id);

                $i_count_message = count($messages);
                $messages[$i_count_message]['type'] = "meldung";
                $messages[$i_count_message]['message'] = "Geraetegruppe und mit Ihr verknüpfte Übungen erfolgreich gelöscht!";
                $messages[$i_count_message]['result'] = true;

                $bilder_pfad = getcwd() . '/images/content/dynamisch/device-groups/' . $i_geraetegruppe_id . '/';

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

    /**
     * eine geraetegruppe als edit feld zurück geben
     */
//    public function getGeraeteFuerEditAction()
//    {
//        $params = $this->getRequest()->getParams();
//
//        if(isset($params['id']))
//        {
//            $i_geraetegruppe_id = $params['id'];
//            $obj_db_geraetegruppe_geraete = new Model_DbTable_DeviceGroupDevices();
//            $a_geraetegruppe_geraete = $obj_db_geraetegruppe_geraete->findDevicesByDeviceGroupId($i_geraetegruppe_id);
//
//            $this->view->assign('a_geraetegruppe_geraete', $a_geraetegruppe_geraete);
//        }
//    }
    /*
    public function getGeraetVorschlaegeAction()
    {
        $params = $this->getRequest()->getParams();
        
        if(isset($params['suche']))
        {
            $str_suche = base64_decode($params['suche']) . '%';
            $obj_db_geraete = new Model_DbTable_Devices();
            
            $a_geraet_vorschlaege = $obj_db_geraete->getGeraetByName($str_suche);
            $this->view->assign('a_geraet_vorschlaege', $a_geraet_vorschlaege);
        }
    }
    */
    public function getGeraetegruppenVorschlaegeAction()
    {
        $params = $this->getRequest()->getParams();

        if(isset($params['suche']))
        {
            $str_suche = base64_decode($params['suche']) . '%';
            $obj_db_geraetegruppen = new Model_DbTable_DeviceGroups();

            $a_geraetegruppen_vorschlaege = $obj_db_geraetegruppen->findDeviceGroupByName($str_suche);
            $this->view->assign('a_geraetegruppen_vorschlaege', $a_geraetegruppen_vorschlaege);
        }
    }

    public function saveAction() {
        $params = $this->getRequest()->getParams();
        $userId = 1;
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            $userId = $user->user_id;
        }

        if (isset($params['edited_elements'])) {
            $deviceGroupsDb = new Model_DbTable_DeviceGroups();
            $deviceXDeviceGroupDb = new Model_DbTable_DeviceXDeviceGroup();

            $deviceGroupName = '';
            $deviceGroupId = 0;
            $deviceGroupDevicesUpdates = array();
            $deviceGroupDevicesDeletes = array();
            $deviceGroupDevicesInserts = array();
            $countDevicesInDeviceGroup = 0;
            $hasErrors = false;
            $messages = array();
            $data = array();

            if (isset($params['edited_elements']['device_group_name'])
                && 0 < strlen(trim($params['edited_elements']['device_group_name']))
            ) {
                $deviceGroupName = base64_decode($params['edited_elements']['device_group_name']);
            }

            if (isset($params['edited_elements']['device_group_id'])) {
                $deviceGroupId = $params['edited_elements']['device_group_id'];
            }

            /**
             * @todo hier muss noch eine möglichkeit gefunden werden, die
             * übergebenen devices für die geraetegruppe zu validieren und
             * zu checken, ob wenigstens eine am ende erhalten bleibt
             */
            if (isset($params['edited_elements']['device_group_devices'])) {
                $deviceGroupDevicesCurrent = array();
                $devicesInDeviceGroupInDb = $deviceXDeviceGroupDb->findDevicesByDeviceGroupId($deviceGroupId);
                $countDevicesInDeviceGroup = count($devicesInDeviceGroupInDb);

                if (is_array($devicesInDeviceGroupInDb)) {
                    foreach ($devicesInDeviceGroupInDb as $device) {
                        // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                        $deviceGroupDevicesCurrent[$device['device_x_device_group_device_fk']] = array(
                            'device_x_device_group_id' => $device['device_x_device_group_id'],
                            'device_id' => $device['device_x_device_group_device_fk']
                        );
                    }
                    $deviceGroupDevicesDeletes = $deviceGroupDevicesCurrent;
                }

                foreach ($params['edited_elements']['device_group_devices'] as $device) {
                    // es wurde eine id übergeben und diese id bestand bereits
                    if (isset($device['id'])
                        && 0 < $device['id']
                        && isset($deviceGroupDevicesCurrent[$device['id']])
                    ) {
                        array_push($deviceGroupDevicesUpdates, array(
                            'device_id' => $device['id'],
                            'device_x_device_group_id' => $deviceGroupDevicesCurrent[$device['id']]['device_x_device_group_id'])
                        );
                        unset($deviceGroupDevicesDeletes[$device['id']]);
                    } else if(isset($device['id'])
                        && 0 < $device['id']
                    ) {
                        array_push($deviceGroupDevicesInserts, array(
                            'device_id' => $device['id'])
                        );
                        $countDevicesInDeviceGroup++;
                    }
                }
            }

            if (0 == strlen(trim($deviceGroupName))
                && !$deviceGroupId
            ) {
                array_push($messages, array('type' => 'fehler', 'message' => 'Diese Geraetegruppe benötigt einen Namen'));
                $hasErrors = true;
            } else if (0 < strlen(trim($deviceGroupName))
                && !$deviceGroupId
            ) {
                $data['device_group_name'] = $deviceGroupName;
            }

            if ($countDevicesInDeviceGroup <= 0) {
                array_push($messages, array('type' => 'fehler', 'message' => 'Diese Geraetegruppe benötigt mindestens ein Geraet'));
                $hasErrors = true;
            }

            $cadSeo = new CAD_Seo();

            if (!$deviceGroupId
                && strlen(trim($deviceGroupName))
            ) {
                $deviceGroupDevicesCurrent = $deviceGroupsDb->findDeviceGroupByName($deviceGroupName);
                if (is_array($deviceGroupDevicesCurrent)
                    && 0 < count($deviceGroupDevicesCurrent)
                ) {
                    array_push($messages, array('type' => 'fehler', 'message' => 'Geraetegruppe "' . $deviceGroupName . '" existiert bereits!', 'result' => false));
                    $hasErrors = true;
                }
            }

            if (!$hasErrors) {
                // updaten?
                if (is_numeric($deviceGroupId)
                    && 0 < $deviceGroupId
                    && is_array($data)
                    && 0 < count($data)
                ) {
                    $deviceGroupCurrent = $deviceGroupsDb->findDeviceGroup($deviceGroupId);

                    if (
                        (
                            isset($data['device_group_name'])
                            && 0 < strlen(trim($data['device_group_name']))
                            && $deviceGroupCurrent['device_group_name'] != $data['device_group_name']
                        ) ||
                        (
                            isset($deviceGroupCurrent['device_group_name'])
                            && 0 < strlen(trim($deviceGroupCurrent['device_group_name']))
                            && !strlen(trim($deviceGroupCurrent['device_group_seo_link']))
                        )
                    ) {
                        if (isset($data['device_group_name'])
                            && 0 < strlen(trim($data['device_group_name']))
                        ) {
                            $deviceGroupName = $data['device_group_name'];
                        } else if (isset($deviceGroupCurrent['device_group_name'])
                            && 0 < strlen(trim($deviceGroupCurrent['device_group_name']))
                        ) {
                            $deviceGroupName = $deviceGroupCurrent['device_group_name'];
                        }
                        $cadSeo->setLinkName($deviceGroupName);
                        $cadSeo->setDbTable($deviceGroupsDb);
                        $cadSeo->setTableFieldName("device_group_seo_link");
                        $cadSeo->setTableFieldIdName("device_group_id");
                        $cadSeo->setTableFieldId($deviceGroupId);
                        $cadSeo->createSeoLink();
                        $data['device_group_seo_link'] = $cadSeo->getSeoName();
                    }

                    $data['device_group_update_date'] = date("Y-m-d H:i:s");
                    $data['device_group_update_user_fk'] = $userId;

                    $deviceGroupsDb->updateDeviceGroup($data, $deviceGroupId);
                    array_push($messages, array('type' => 'meldung', 'message' => 'Diese Geraetegruppe wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $deviceGroupId));
                // neu anlegen
                } else if (is_array($data)
                    && 0 < count($data)
                ) {
                    $cadSeo->setLinkName($data['device_group_name']);
                    $cadSeo->setDbTable($deviceGroupsDb);
                    $cadSeo->setTableFieldName("device_group_seo_link");
                    $cadSeo->setTableFieldIdName("device_group_id");
                    $cadSeo->setTableFieldId($deviceGroupId);
                    $cadSeo->createSeoLink();

                    $data['device_group_seo_link'] = $cadSeo->getSeoName();
                    $data['device_group_create_date'] = date("Y-m-d H:i:s");
                    $data['device_group_create_user_fk'] = $userId;

                    $deviceGroupId = $deviceGroupsDb->saveDeviceGroup($data);
                    array_push($messages, array('type' => 'meldung', 'message' => 'Diese Geraetegruppe wurde erfolgreich angelegt!', 'result' => true, 'id' => $deviceGroupId));
                }

                if (count($deviceGroupDevicesInserts)
                    && 0 == count($deviceGroupDevicesUpdates)
                    && 0 == count($deviceGroupDevicesDeletes)
                    && 0 < $deviceGroupId
                ) {
                    array_push($messages, array('type' => 'meldung', 'message' => 'Diese Geraetegruppe wurde nicht geändert!', 'result' => true, 'id' => $deviceGroupId));
                }

                if ($deviceGroupId > 0 &&
                    (
                        count($deviceGroupDevicesInserts) > 0 ||
                        count($deviceGroupDevicesUpdates) > 0 ||
                        count($deviceGroupDevicesDeletes) > 0)
                ) {
                    array_push($messages, array('type' => 'meldung', 'message' => 'Die Geraete der Geraetegruppen wurden erfolgreich geändert!', 'result' => true, 'id' => $deviceGroupId));
                }

                if ($deviceGroupId) {
                    /* bilder verschieben */
                    /*
                    $obj_files = new CAD_File();
//                    $str_src_path = getcwd() . '/tmp/device-groups/';
//                    $str_dest_path = getcwd() . '/images/content/dynamisch/device-groups/' . $i_geraetegruppe_id . '/';

                    if($obj_files->checkAndCreateDir($str_dest_path))
                    {
                        $obj_files->setSourcePath($str_src_path);
                        $obj_files->setDestPath($str_dest_path);
                        $obj_files->setAllowedExtensions(array('jpg', 'png', 'gif', 'svg'));
                        $obj_files->verschiebeFiles();
                    }
                    */
                    /* device-groups bearbeiten */

                    foreach ($deviceGroupDevicesInserts as $device) {
                        $data = array();
                        $data['device_x_device_group_device_fk'] = $device['device_id'];
                        $data['device_x_device_group_device_group_fk'] = $deviceGroupId;
                        $data['device_x_device_group_create_date'] = date("Y-m-d H:i:s");
                        $data['device_x_device_group_create_user_fk'] = $userId;

                        $deviceXDeviceGroupDb->saveDeviceGroupDevice($data);
                    }

                    /** @TODO macht irgendwie keinen sinn, was soll hier geupdatet werden ... */
                    foreach ($deviceGroupDevicesUpdates as $device) {
                        $data = array();
                        $data['device_x_device_group_device_group_fk']   = $deviceGroupId;
                        $data['device_x_device_group_update_date']     = date("Y-m-d H:i:s");
                        $data['device_x_device_group_update_user_fk']   = $userId;

                        $deviceXDeviceGroupDb->updateDeviceGroupDevice($data, $device['device_x_device_group_id']);
                    }

                    foreach ($deviceGroupDevicesDeletes as $device) {
                        $deviceXDeviceGroupDb->deleteDeviceFromDeviceGroupDevices($device['device_x_device_group_id']);
                    }
                }
            } else {
                array_push($messages, array('type' => 'fehler', 'message' => 'Es gabe einen Fehler bei Geraetegruppe speichern!', 'result' => false));
            }
        } else {
            array_push($messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Geraetegruppe speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($messages));
    }
}

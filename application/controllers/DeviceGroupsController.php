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
    public function init() {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_accordion.js',
                'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript');
        }
    }

    public function indexAction() {
        $deviceGroupsDb = new Model_DbTable_DeviceGroups();

        $deviceGroupsCollection = $deviceGroupsDb->findAllDeviceGroups()->toArray();
        $deviceGroupsContent = "Es wurden leider keine Gerätegruppen gefunden!";

        if (0 < count($deviceGroupsCollection)) {
            $deviceGroupsContent = '';
            foreach ($deviceGroupsCollection as $deviceGroup) {

                $this->view->assign('name', $deviceGroup['device_group_name']);
                $this->view->assign('id', $deviceGroup['device_group_id']);
                $deviceGroupsContent .= $this->view->render('/loops/item-row.phtml');
            }
        }
        $this->view->assign('deviceGroupsContent', $deviceGroupsContent);
    }

    public function showAction() {

        $id = intval($this->getParam('id'));
        if (0 < $id) {
            $deviceGroupDb = new Model_DbTable_DeviceGroups();
            $deviceGroup = $deviceGroupDb->findDeviceGroup($id);

            if ($deviceGroup instanceof Zend_Db_Table_Row_Abstract) {
                $this->view->assign('devicesContent', $this->generateDevicesContent($id));
                $this->view->assign('detailOptionsContent', $this->generateDetailOptionsContent($id));
                $this->view->assign('name', $deviceGroup->offsetGet('device_group_name'));
                $this->view->assign('id',  $deviceGroup->offsetGet('device_group_id'));
            }
        }
    }

    private function generateDevicesContent($id) {
        $content = '';

        $devicesDb = new Model_DbTable_Devices();
        $devicesCollection = $devicesDb->findAllDevicesByDeviceGroupId($id);

        foreach ($devicesCollection as $device) {
            $this->view->assign('name', $device->offsetGet('device_name'));
            $this->view->assign('id', $device->offsetGet('device_id'));
            $content .= $this->view->render('loops/device-row.phtml');
        }

        return $content;
    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

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

    public function deleteAction() {
        $params = $this->getRequest()->getParams();

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

                Service_GlobalMessageHandler::appendMessage("Geraetegruppe und mit Ihr verknüpfte Übungen erfolgreich gelöscht!", Model_Entity_Message::STATUS_OK);

                $bilder_pfad = getcwd() . '/images/content/dynamisch/device-groups/' . $i_geraetegruppe_id . '/';

                $obj_file = new CAD_File();
                $obj_file->cleanDirRek($bilder_pfad, 2);
            } else {
                Service_GlobalMessageHandler::appendMessage("Gerätegruppe konnte nicht gelöscht werden!", Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage("Falscher Aufruf!", Model_Entity_Message::STATUS_ERROR);
        }
    }

    public function saveAction() {
        $params = $this->getRequest()->getParams();
        $userId = 1;
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            $userId = $user->user_id;
        }

        if (isset($params)) {
            $deviceGroupsDb = new Model_DbTable_DeviceGroups();
            $deviceXDeviceGroupDb = new Model_DbTable_DeviceXDeviceGroup();

            $deviceGroupName = '';
            $deviceGroupId = 0;
            $deviceGroupDevicesDeletes = array();
            $deviceGroupDevicesInserts = array();
            $countDevicesInDeviceGroup = 0;
            $hasErrors = false;
            $data = array();

            if (isset($params['device_group_name'])
                && 0 < strlen(trim($params['device_group_name']))
            ) {
                $deviceGroupName = base64_decode($params['device_group_name']);
            }

            if (isset($params['device_group_id'])) {
                $deviceGroupId = $params['device_group_id'];
            }

            /**
             * @todo hier muss noch eine möglichkeit gefunden werden, die
             * übergebenen devices für die geraetegruppe zu validieren und
             * zu checken, ob wenigstens eine am ende erhalten bleibt
             */
            if (isset($params['device_group_devices'])) {
                $deviceGroupDevicesCurrent = array();
                $devicesInDeviceGroupInDb = $deviceXDeviceGroupDb->findDevicesByDeviceGroupId($deviceGroupId);
                $countDevicesInDeviceGroup = count($devicesInDeviceGroupInDb);

                if (is_array($devicesInDeviceGroupInDb)
                    || $devicesInDeviceGroupInDb instanceof Zend_Db_Table_Rowset
                ) {
                    foreach ($devicesInDeviceGroupInDb as $device) {
                        // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                        $deviceGroupDevicesCurrent[$device['device_x_device_group_device_fk']] = array(
                            'device_x_device_group_id' => $device['device_x_device_group_id'],
                            'device_id' => $device['device_x_device_group_device_fk']
                        );
                    }
                    $deviceGroupDevicesDeletes = $deviceGroupDevicesCurrent;
                }

                foreach ($params['device_group_devices'] as $device) {
                    // es wurde eine id übergeben und diese id bestand bereits
                    if(isset($device['id'])
                        && 0 < $device['id']
                        && !isset($deviceGroupDevicesCurrent[$device['id']])
                    ) {
                        array_push($deviceGroupDevicesInserts, array(
                            'device_id' => $device['id'])
                        );
                        $countDevicesInDeviceGroup++;
                    // update routine, but nothing to do else prevent deletion for device in group
                    } else if (isset($device['id'])
                        && 0 < $device['id']
                        && isset($deviceGroupDevicesCurrent[$device['id']])
                    ) {
                        unset($deviceGroupDevicesDeletes[$device['id']]);
                    }
                }
            }

            if (0 == strlen(trim($deviceGroupName))
                && !$deviceGroupId
            ) {
                Service_GlobalMessageHandler::appendMessage('Diese Geraetegruppe benötigt einen Namen', Model_Entity_Message::STATUS_ERROR);
                $hasErrors = true;
            } else if (0 < strlen(trim($deviceGroupName))
                && !$deviceGroupId
            ) {
                $data['device_group_name'] = $deviceGroupName;
            }

            if ($countDevicesInDeviceGroup <= 0) {
                Service_GlobalMessageHandler::appendMessage('Diese Geraetegruppe benötigt mindestens ein Geraet', Model_Entity_Message::STATUS_ERROR);
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
                    Service_GlobalMessageHandler::appendMessage('Geraetegruppe "' . $deviceGroupName . '" existiert bereits!', Model_Entity_Message::STATUS_ERROR);
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
                    Service_GlobalMessageHandler::appendMessage('Diese Geraetegruppe wurde erfolgreich bearbeitet!', Model_Entity_Message::STATUS_OK);
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
                    Service_GlobalMessageHandler::appendMessage('Diese Geraetegruppe wurde erfolgreich angelegt!', Model_Entity_Message::STATUS_OK);
                }

                if (0 == count($deviceGroupDevicesInserts)
                    && 0 == count($deviceGroupDevicesDeletes)
                    && 0 < $deviceGroupId
                ) {
                    Service_GlobalMessageHandler::appendMessage('Diese Geraetegruppe wurde nicht geändert!', Model_Entity_Message::STATUS_ERROR);
                }

                if ($deviceGroupId > 0 &&
                    (
                        count($deviceGroupDevicesInserts) > 0 ||
                        count($deviceGroupDevicesDeletes) > 0)
                ) {
                    Service_GlobalMessageHandler::appendMessage('Die Geraete der Geraetegruppen wurden erfolgreich geändert!', Model_Entity_Message::STATUS_OK);
                }

                if ($deviceGroupId) {
                    foreach ($deviceGroupDevicesInserts as $device) {
                        $data = array();
                        $data['device_x_device_group_device_fk'] = $device['device_id'];
                        $data['device_x_device_group_device_group_fk'] = $deviceGroupId;
                        $data['device_x_device_group_create_date'] = date("Y-m-d H:i:s");
                        $data['device_x_device_group_create_user_fk'] = $userId;

                        $deviceXDeviceGroupDb->saveDeviceGroupDevice($data);
                    }

                    foreach ($deviceGroupDevicesDeletes as $device) {
                        $deviceXDeviceGroupDb->deleteDeviceFromDeviceGroupDevices($device['device_x_device_group_id']);
                    }
                }
            } else {
                Service_GlobalMessageHandler::appendMessage('Es gabe einen Fehler bei Geraetegruppe speichern!', Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage('Falscher Aufruf von Geraetegruppe speichern!', Model_Entity_Message::STATUS_ERROR);
        }
    }
}

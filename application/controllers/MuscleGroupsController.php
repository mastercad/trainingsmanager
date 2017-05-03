<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class MuscleGroupsController extends AbstractController {

    public function indexAction() {
        $muscleGroupsDb = new Model_DbTable_MuscleGroups();

        $muscleGroupsCollection = $muscleGroupsDb->findAllMuscleGroups()->toArray();
        $muscleGroupsContent = "Es wurden leider keine Muskelgruppen gefunden!";

        if (0 < count($muscleGroupsCollection)) {
            $muscleGroupsContent = '';
            foreach ($muscleGroupsCollection as $muscleGroup) {

                $this->view->assign('name', $muscleGroup['muscle_group_name']);
                $this->view->assign('id', $muscleGroup['muscle_group_id']);
                $muscleGroupsContent .= $this->view->render('/loops/item-row.phtml');
            }
        }
        $this->view->assign('muscleGroupsContent', $muscleGroupsContent);
    }

    public function showAction() {

        $id = intval($this->getParam('id'));
        if (0 < $id) {
            $musclesDb = new Model_DbTable_MuscleGroups();
            $muscle = $musclesDb->findMuscleGroup($id);

            if ($muscle instanceof Zend_Db_Table_Row_Abstract) {
                $this->view->assign('musclesContent', $this->generateMusclesContent($id));
                $this->view->assign('detailOptionsContent', $this->generateDetailOptionsContent($id));
                $this->view->assign('name', $muscle->offsetGet('muscle_group_name'));
                $this->view->assign('id',  $muscle->offsetGet('muscle_group_id'));
            }
        }
    }

    private function generateMusclesContent($id) {
        $content = '';

        $musclesDb = new Model_DbTable_Muscles();
        $musclesCollection = $musclesDb->findAllMusclesByMuscleGroupId($id);

        foreach ($musclesCollection as $muscle) {
            $this->view->assign('name', $muscle->offsetGet('muscle_name'));
            $this->view->assign('id', $muscle->offsetGet('muscle_id'));
            $content .= $this->view->render('loops/muscle-row.phtml');
        }

        return $content;
    }

    /**
     *
     */
    public function deleteMuscleAction() {

        $id = intval($this->getParam('id'));
        if (0 < $id) {
            $musclesDb = new Model_DbTable_Muscles();
            $result = $musclesDb->deleteMuscle($id);
            echo $result;
        }
    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

        if (isset($params['id'])
            && is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $muscleGroupId = $params['id'];

            $muscleXMuscleGroupDb = new Model_DbTable_MuscleXMuscleGroup();
            $muscleCollection = $muscleXMuscleGroupDb->findMusclesByMuscleGroupId($muscleGroupId);
            $this->view->assign('musclesCollection', $muscleCollection);

            $content = '';
            foreach ($muscleCollection as $muscle) {
                if ($muscle instanceof Zend_Db_Table_Row) {
                    $muscle = $muscle->toArray();
                }
                $this->view->assign($muscle);
                $content .= $this->view->render('/loops/muscle-edit.phtml');
            }
            $this->view->assign('muscle_name', null);
            $this->view->assign('muscle_id', null);
            $content .= $this->view->render('/loops/muscle-edit.phtml');

            $this->view->assign('muscle_collection_content', $content);

            $muscleGroupDb = new Model_DbTable_MuscleGroups();
            $muscleGroup = $muscleGroupDb->findMuscleGroup($muscleGroupId);

            $this->view->assign($muscleGroup->toArray());
        }
    }

    public function deleteAction() {
        $a_params = $this->getRequest()->getParams();
        $messagesCollection = array();

        if (isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0
        ) {
            $i_muskelgruppe_id = $a_params['id'];

            $obj_db_uebungen = new Model_DbTable_Exercises();
            $obj_db_muskelgruppen = new Model_DbTable_MuscleGroups();
            $obj_db_uebung_muskelgruppen = new Model_DbTable_ExerciseXMuscle();
            $obj_db_muskelgruppen_muskeln = new Model_DbTable_MuscleXMuscleGroup();

            $a_uebungen = $obj_db_uebung_muskelgruppen->findExercisesForMuscleGroup($i_muskelgruppe_id);

            if ($obj_db_muskelgruppen->deleteMuscleGroup($i_muskelgruppe_id)) {
                $obj_db_muskelgruppen_muskeln->deleteAllMuscleGroupsMusclesByMuscleGroupId($i_muskelgruppe_id);
                $obj_db_uebung_muskelgruppen->deleteExerciseXMuscleByMuscleGroupId($i_muskelgruppe_id);

                if (is_array($a_uebungen) &&
                    count($a_uebungen) > 0
                ) {
                    foreach ($a_uebungen as $a_uebung) {
                        $obj_db_uebungen->deleteExercise($a_uebung['uebung_muskelgruppe_uebung_fk']);
                    }
                }

                $i_count_message = count($messagesCollection);
                $messagesCollection[$i_count_message]['type'] = "meldung";
                $messagesCollection[$i_count_message]['message'] = "Muskelgruppe und mit Ihr verknüpfte Übungen erfolgreich gelöscht!";
                $messagesCollection[$i_count_message]['result'] = true;

                $bilder_pfad = getcwd() . '/images/content/dynamisch/muscle-groups/' . $i_muskelgruppe_id . '/';

                $obj_file = new CAD_File();
                $obj_file->cleanDirRek($bilder_pfad, 2);
            } else {
                $i_count_message = count($messagesCollection);
                $messagesCollection[$i_count_message]['type'] = "fehler";
                $messagesCollection[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
                $messagesCollection[$i_count_message]['result'] = false;
            }
        } else {
            $i_count_message = count($messagesCollection);
            $messagesCollection[$i_count_message]['type'] = "fehler";
            $messagesCollection[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
            $messagesCollection[$i_count_message]['result'] = false;
        }
        $this->view->assign('json_string', json_encode($messagesCollection));
    }

    /**
     * eine liste der muscle-groups für die übung zurück geben
     *
     * @access public
     * @
     */
    public function getMuscleGroupForEditAction() {
        $aParams = $this->getAllParams();
        $aMessages = array();

        if (true === isset($aParams['id'])) {
            $iUebungId = $aParams['id'];
            $oUebungMuskelnStorage = new Model_DbTable_ExerciseXMuscle();
            $aUebungMuskeln = $oUebungMuskelnStorage->findMusclesForExercise($iUebungId);
            $aMuskelGruppen = array();

            foreach ($aUebungMuskeln as $aUebungMuskel) {
                if (FALSE === array_key_exists($aUebungMuskel['muscle_group_name'], $aMuskelGruppen)) {
                    $aMuskelGruppen[$aUebungMuskel['muscle_group_name']] = array();
                }
                $aMuskelGruppen[$aUebungMuskel['muscle_group_name']]['muscle_group_name'] = $aUebungMuskel['muscle_group_name'];
                $aMuskelGruppen[$aUebungMuskel['muscle_group_name']]['muscle_group_id'] = $aUebungMuskel['muscle_group_id'];
                $aMuskelGruppen[$aUebungMuskel['muscle_group_name']]['muscles'][] = $aUebungMuskel->toArray();
            }
            $this->view->assign('aMuskelGruppen', $aMuskelGruppen);
        }

        if (0 < count($aMessages)) {
            $this->view->assign('json_string', json_encode($aMessages));
        }
    }

//    public function getMuskelgruppeFuerEditAction() {
//        $aParams = $this->getAllParams();
//        $aMessages = array();
//
//        if (isset($aParams['id'])) {
//            $iMuskelGruppeId = $aParams['id'];
//            $oMuskelGruppenStorage = new Model_DbTable_MuscleGroups();
//            $aMuskelGruppeInclMuskeln = $oMuskelGruppenStorage->findMuscleGroup($iMuskelGruppeId);
//            $aMuskelGruppe = array();
//
//            foreach ($aMuskelGruppeInclMuskeln as $aMuskel) {
//                $aMuskelGruppe['muskelgruppe_name'] = $aMuskel->muskelgruppe_name;
//                $aMuskelGruppe['muskelgruppe_id'] = $aMuskel->muskelgruppe_id;
//                $aMuskelGruppe['muscles'][] = $aMuskel->toArray();
//            }
//            $this->view->assign('aMuskelGruppe', $aMuskelGruppe);
//        }
//
//        if (count($aMessages) > 0) {
//            $this->view->assign('json_string', json_encode($aMessages));
//        }
//    }

    /**
     * get all muscle groups by given search string
     */
    public function getMuscleGroupProposalsAction() {
        $params = $this->getRequest()->getParams();

        if (isset($params['search'])) {
            $search = base64_decode($params['search']) . '%';
            $muscleGroupsDb = new Model_DbTable_MuscleGroups();

            $muscleGroupsProposals = $muscleGroupsDb->findMuscleGroupsByName($search);
            $this->view->assign('muscleGroupProposals', $muscleGroupsProposals);
        }
    }

    /**
     * save muscle group
     */
    public function saveAction() {

        $params = $this->getRequest()->getParams();

        if ($this->getRequest()->isPost()) {

            $muscleGroupsDb = new Model_DbTable_MuscleGroups();
            $muscleXMuscleGroupsDb = new Model_DbTable_MuscleXMuscleGroup();

            $muscleGroupName = '';
            $muscleGroupId = 0;
            $muscleXMuscleGroupsUpdates = array();
            $muscleXMuscleGroupsDeletes = array();
            $muscleXMuscleGroupsInsert = array();
            $countMuscleGroups = 0;
            $hasErrors = false;
            $messagesCollection = array();
            $data = array();

            $user = Zend_Auth::getInstance()->getIdentity();

            $userId = 1;

            if (TRUE == is_object($user)) {
                $userId = $user->user_id;
            }

            if (isset($params['muscle_group_name'])
                && 0 < strlen(trim($params['muscle_group_name']))
            ) {
                $muscleGroupName = base64_decode($params['muscle_group_name']);
            }

            if (isset($params['muscle_group_id'])) {
                $muscleGroupId = intval($params['muscle_group_id']);
            }

            /**
             * @todo hier muss noch eine möglichkeit gefunden werden, die
             * übergebenen muscles für die muskelgruppe zu validieren und
             * zu checken, ob wenigstens eine am ende erhalten bleibt
             */
            if (isset($params['muscles_in_muscle_group'])) {
                $muscleXMuscleGroupsCurrent = array();

                $currentMusclesInMuscleGroupInDB = $muscleXMuscleGroupsDb->findMusclesByMuscleGroupId($muscleGroupId);
                $countMuscleGroups = count($currentMusclesInMuscleGroupInDB);

                if ((is_array($currentMusclesInMuscleGroupInDB)
                    || $currentMusclesInMuscleGroupInDB instanceof Zend_Db_Table_Rowset)
                    && 0 < count($currentMusclesInMuscleGroupInDB)
                ) {
                    foreach ($currentMusclesInMuscleGroupInDB as $muscle) {
                        // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                        $muscleXMuscleGroupsCurrent[$muscle['muscle_x_muscle_group_muscle_fk']] = array(
                            'muscle_x_muscle_group_id' => $muscle['muscle_x_muscle_group_id'],
                            'muscle_id' => $muscle['muscle_x_muscle_group_muscle_fk'],
                        );
                    }
                }

                foreach ($params['muscles_in_muscle_group'] as $muscle) {

                    // es wurde eine id übergeben und diese id bestand bereits
                    if (isset($muscle['id'])
                        && 0 < $muscle['id']
                        && isset($muscleXMuscleGroupsCurrent[$muscle['id']])
                    ) {
                        array_push($muscleXMuscleGroupsUpdates, array(
                                'muscle_x_muscle_group_muscle_fk' => $muscle['id'],
                                'muscle_x_muscle_group_id' => $muscleXMuscleGroupsCurrent[$muscle['id']]['muscle_x_muscle_group_id']
                            )
                        );
                        unset($muscleXMuscleGroupsCurrent[$muscle['id']]);
                    } else if (isset($muscle['id'])
                        && 0 < $muscle['id']
                        && ! isset($muscleXMuscleGroupsCurrent[$muscle['id']])
                    ) {
                        array_push($muscleXMuscleGroupsInsert, array(
                                'muscle_id' => $muscle['id']
                            )
                        );
                        $countMuscleGroups++;
                    }
                }
                foreach ($muscleXMuscleGroupsCurrent as $muscle) {
                    array_push($muscleXMuscleGroupsDeletes, $muscle['muscle_x_muscle_group_id']);
                }
            }

            if (0 == strlen(trim($muscleGroupName))
                && ! $muscleGroupId
            ) {
                Service_GlobalMessageHandler::appendMessage('Diese Muskelgruppe benötigt einen Namen', Model_Entity_Message::STATUS_ERROR);
                $hasErrors = true;
            } else if (0 < strlen(trim($muscleGroupName))
                && ! $muscleGroupId
            ) {
                $data['muscle_group_name'] = $muscleGroupName;
            }

            if ($countMuscleGroups <= 0) {
                Service_GlobalMessageHandler::appendMessage('Diese Muskelgruppe benötigt mindestens einen beanspruchten Muskel', Model_Entity_Message::STATUS_ERROR);
                $hasErrors = true;
            }

            $cadSeo = new CAD_Seo();

            if (! $muscleGroupId
                && strlen(trim($muscleGroupName))
            ) {
                $muscleGroupsCurrent = $muscleGroupsDb->findMuscleGroupsByName($muscleGroupName);
                if (is_array($muscleGroupsCurrent)
                    && 0 < count($muscleGroupsCurrent)
                ) {
                    Service_GlobalMessageHandler::appendMessage('Muskelgruppe "' . $muscleGroupName . '" existiert bereits!', Model_Entity_Message::STATUS_ERROR);
                    $hasErrors = true;
                }
            }

            if (! $hasErrors) {
                // muskelgruppe updaten?
                if (0 < $muscleGroupId
                    && is_array($data)
                    && 0 < count($data)
                ) {
                    $muscleGroupsCurrent = $muscleGroupsDb->findMuscleGroup($muscleGroupId);

                    if (
                        (
                            isset($data['muscle_group_name'])
                            && 0 < strlen(trim($data['muscle_group_name']))
                            && $muscleGroupsCurrent['muscle_group_name'] != $data['muscle_group_name']
                        ) ||
                        (
                            isset($muscleGroupsCurrent['muscle_group_name'])
                            && 0 < strlen(trim($muscleGroupsCurrent['muscle_group_name']))
                            && ! strlen(trim($muscleGroupsCurrent['muscle_group_name']))
                        )
                    ) {
                        if (isset($a_data['muscle_group_name'])
                            && 0 < strlen(trim($a_data['muscle_group_name']))
                        ) {
                            $muscleGroupName = $a_data['muscle_group_name'];
                        } else if (isset($muscleGroupsCurrent['muscle_group_name'])
                            && 0 < strlen(trim($muscleGroupsCurrent['muscle_group_name']))
                        ) {
                            $muscleGroupName = $muscleGroupsCurrent['muscle_group_name'];
                        }
                        $cadSeo->setLinkName($muscleGroupName);
                        $cadSeo->setDbTable($muscleGroupsDb);
                        $cadSeo->setTableFieldName("muscle_group_seo_link");
                        $cadSeo->setTableFieldIdName("muscle_group_id");
                        $cadSeo->setTableFieldId($muscleGroupId);
                        $cadSeo->createSeoLink();
                        $data['muscle_group_seo_link'] = $cadSeo->getSeoName();
                    }
                    $data['muscle_group_update_date'] = date("Y-m-d H:i:s");
                    $data['muscle_group_update_user_fk'] = $userId;

                    $muscleGroupsDb->updateMuscleGroup($data, $muscleGroupId);
                    Service_GlobalMessageHandler::appendMessage('Diese Muskelgruppe wurde erfolgreich bearbeitet!', Model_Entity_Message::STATUS_OK);
                } // muskelgruppe neu anlegen
                else if (is_array($data)
                    && 0 < count($data)
                ) {
                    $cadSeo->setLinkName($data['muscle_group_name']);
                    $cadSeo->setDbTable($muscleGroupsDb);
                    $cadSeo->setTableFieldName("muscle_group_seo_link");
                    $cadSeo->setTableFieldIdName("muscle_group_id");
                    $cadSeo->setTableFieldId($muscleGroupId);
                    $cadSeo->createSeoLink();

                    $data['muscle_group_seo_link'] = $cadSeo->getSeoName();
                    $data['muscle_group_create_date'] = date("Y-m-d H:i:s");
                    $data['muscle_group_create_user_fk'] = $userId;

                    $muscleGroupId = $muscleGroupsDb->saveMuscleGroup($data);
                    Service_GlobalMessageHandler::appendMessage('Diese Muskelgruppe wurde erfolgreich angelegt!', Model_Entity_Message::STATUS_OK);
                }

                if (0 === count($muscleXMuscleGroupsInsert)
                    && 0 === count($muscleXMuscleGroupsUpdates)
                    && 0 === count($muscleXMuscleGroupsDeletes)
                ) {
                    Service_GlobalMessageHandler::appendMessage('Diese Muskelgruppe wurde nicht geändert!', Model_Entity_Message::STATUS_ERROR);
                } else if ($muscleGroupId) {

                    foreach ($muscleXMuscleGroupsInsert as $muscleXMuscleGroupMuscle) {
                        $data = array();
                        $data['muscle_x_muscle_group_muscle_fk'] = $muscleXMuscleGroupMuscle['muscle_id'];
                        $data['muscle_x_muscle_group_muscle_group_fk'] = $muscleGroupId;
                        $data['muscle_x_muscle_group_create_date'] = date("Y-m-d H:i:s");
                        $data['muscle_x_muscle_group_create_user_fk'] = $userId;

                        $muscleXMuscleGroupsDb->saveMuscleXMuscleGroup($data);
                    }

                    foreach ($muscleXMuscleGroupsUpdates as $muscleXMuscleGroupMuscle) {
                        $data = array();
                        $data['muscle_x_muscle_group_muscle_fk'] = $muscleGroupId;
                        $data['muscle_x_muscle_group_update_date'] = date("Y-m-d H:i:s");
                        $data['muscle_x_muscle_group_update_user_fk'] = $userId;

                        $muscleXMuscleGroupsDb->updateMuscleGroupMuscle($data,
                            $muscleXMuscleGroupMuscle['muscleXMuscleGroupId']);
                    }

                    foreach ($muscleXMuscleGroupsDeletes as $muscleXMuscleGroupId) {
                        $muscleXMuscleGroupsDb->deleteMuscleGroupById($muscleXMuscleGroupId);
                    }
                }
            } else {
                Service_GlobalMessageHandler::appendMessage('Es gabe einen Fehler beim Muskelgruppe speichern!', Model_Entity_Message::STATUS_ERROR);
            }
        } else {
            Service_GlobalMessageHandler::appendMessage('Falscher Aufruf von Muskelgruppe speichern!', Model_Entity_Message::STATUS_ERROR);
        }
    }
}

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MuscleGroupsController extends Zend_Controller_Action {

    protected $breadcrumb;
    protected $schlagwoerter;
    protected $beschreibung;

    public function init() {
    }

    public function postDispatch() {
        $this->view->assign('breadcrumb', $this->breadcrumb);

        $a_params = $this->getRequest()->getParams();

        if (isset($a_params['ajax'])) {
            $this->view->layout()->disableLayout();
        }

        $this->view->headMeta()->appendName('keywords', $this->schlagwoerter);
        $this->view->headMeta()->appendName('description', $this->beschreibung);
    }

    public function indexAction() {
        $muscleGroupsDb = new Model_DbTable_MuscleGroups();
        $muscleXMuscleGroupDb = new Model_DbTable_MuscleXMuscleGroup();

        $muscleGroupsCollection = $muscleGroupsDb->findAllMuscleGroups()->toArray();
        $muscleGroupsContent = "Es wurden leider keine Muskelgruppen gefunden!";

        if (0 < count($muscleGroupsCollection)) {
            $muscleGroupsContent = '';
            foreach ($muscleGroupsCollection as $muscleGroup) {
                $musclesInMuscleGroup = $muscleXMuscleGroupDb->findMusclesByMuscleGroupId($muscleGroup['muscle_group_id']);
                $muscleGroup['musclesInMuscleGroup'] = $musclesInMuscleGroup;
                $musclesInMuscleGroupContent = 'Diese Muskelgruppe besitzt keine Muskeln!';

                if (0 < count($musclesInMuscleGroup)) {
                    $musclesInMuscleGroupContent = '';
                    foreach ($musclesInMuscleGroup as $muscle) {
                        $this->view->assign($muscle->toArray());
                        $musclesInMuscleGroupContent .= $this->view->render('loops/muscle.phtml');
                    }
                }
                $this->view->assign('musclesInMuscleGroupContent', $musclesInMuscleGroupContent);
                $this->view->assign($muscleGroup);
                $muscleGroupsContent .= $this->view->render('/loops/muscle-group.phtml');
            }
        }
        $this->view->assign('muscleGroupsContent', $muscleGroupsContent);
    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

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

    public function deleteMuscleGroupAction() {
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

        if (isset($params['edited_elements'])) {

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
//            } else {
//                  array_push($messageCollection, array('type' => 'fehler', 'message' => 'Sie müssen angemeldet sein, um diese Aktion durchführen zu können!', 'result' => false));
//                  $hasError = true;
            }

            if (isset($params['edited_elements']['muscle_group_name'])
                && 0 < strlen(trim($params['edited_elements']['muscle_group_name']))
            ) {
                $muscleGroupName = base64_decode($params['edited_elements']['muscle_group_name']);
            }

            if (isset($params['edited_elements']['muscle_group_id'])) {
                $muscleGroupId = intval($params['edited_elements']['muscle_group_id']);
            }

            /**
             * @todo hier muss noch eine möglichkeit gefunden werden, die
             * übergebenen muscles für die muskelgruppe zu validieren und
             * zu checken, ob wenigstens eine am ende erhalten bleibt
             */
            if (isset($params['edited_elements']['muscles_in_muscle_group'])) {
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

                foreach ($params['edited_elements']['muscles_in_muscle_group'] as $muscle) {

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
                array_push($messagesCollection,
                    array('type' => 'fehler', 'message' => 'Diese Muskelgruppe benötigt einen Namen'));
                $hasErrors = true;
            } else if (0 < strlen(trim($muscleGroupName))
                && ! $muscleGroupId
            ) {
                $data['muscle_group_name'] = $muscleGroupName;
            }

            if ($countMuscleGroups <= 0) {
                array_push($messagesCollection, array(
                    'type' => 'fehler', 'message' => 'Diese Muskelgruppe benötigt mindestens einen beanspruchten Muskel'
                ));
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
                    array_push($messagesCollection, array(
                        'type' => 'fehler', 'message' => 'Muskelgruppe "' . $muscleGroupName . '" existiert bereits!',
                        'result' => false
                    ));
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
                    array_push($messagesCollection, array(
                        'type' => 'meldung', 'message' => 'Diese Muskelgruppe wurde erfolgreich bearbeitet!',
                        'result' => true, 'id' => $muscleGroupId
                    ));
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
                    array_push($messagesCollection, array(
                        'type' => 'meldung', 'message' => 'Diese Muskelgruppe wurde erfolgreich angelegt!',
                        'result' => true, 'id' => $muscleGroupId
                    ));
                }

                if (0 === count($muscleXMuscleGroupsInsert)
                    && 0 === count($muscleXMuscleGroupsUpdates)
                    && 0 === count($muscleXMuscleGroupsDeletes)
                ) {
                    array_push($messagesCollection, array(
                        'type' => 'meldung', 'message' => 'Diese Muskelgruppe wurde nicht geändert!', 'result' => true,
                        'id' => $muscleGroupId
                    ));
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

                    if (0 < count($muscleXMuscleGroupsInsert)
                        || 0 < count($muscleXMuscleGroupsUpdates)
                        || 0 < count($muscleXMuscleGroupsDeletes)
                    ) {
                        array_push($messagesCollection, array(
                            'type' => 'meldung', 'message' => 'Die Muskeln der Muskelgruppe wurden erfolgreich geändert!',
                            'result' => true, 'id' => $muscleGroupId
                        ));
                    }
                }
            } else {
                array_push($messagesCollection, array(
                    'type' => 'fehler', 'message' => 'Es gabe einen Fehler bei Muskelgruppe speichern!',
                    'result' => false
                ));
            }
        } else {
            array_push($messagesCollection, array(
                'type' => 'fehler', 'message' => 'Falscher Aufruf von Muskelgruppe speichern!', 'result' => false
            ));
        }
        $this->view->assign('json_string', json_encode($messagesCollection));
    }
}
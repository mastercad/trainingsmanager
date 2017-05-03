<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class MusclesController extends AbstractController
{
    /**
     * shows overview over all stored muscles in database
     */
    public function indexAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

        $musclesDb = new Model_DbTable_Muscles();
        $musclesCollection = $musclesDb->findAllMuscles();

        $musclesContent = $this->translate('label_no_muscles_found');

        if ($musclesCollection instanceof Zend_Db_Table_Rowset
            && 0 < count($musclesCollection)
        ) {
            $musclesContent = '';
            foreach($musclesCollection as $muscle)
            {
                $this->view->assign('name', $muscle->offsetGet('muscle_name'));
                $this->view->assign('id', $muscle->offsetGet('muscle_id'));
                $musclesContent .= $this->view->render('loops/item-row.phtml');
            }
        }

        $this->view->assign('musclesContent', $musclesContent);
    }

    public function showAction() {

        $id = intval($this->getParam('id'));
        if (0 < $id) {
            $musclesDb = new Model_DbTable_Muscles();
            $muscle = $musclesDb->findMuscle($id);
            if ($muscle instanceof Zend_Db_Table_Row_Abstract) {
                $this->view->assign('name', $muscle->offsetGet('muscle_name'));
                $this->view->assign('id',  $muscle->offsetGet('muscle_id'));
                $this->view->assign('detailOptionsContent', $this->generateDetailOptionsContent($id));
            }
        }
    }

    /**
     *
     */
    public function deleteAction() {

        $id = intval($this->getParam('id'));
        if (0 < $id) {
            $musclesDb = new Model_DbTable_Muscles();
            $result = $musclesDb->deleteMuscle($id);
            echo $result;
        }
    }
    
    public function editAction()
    {
        $params = $this->getRequest()->getParams();

        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');
        }

        if (isset($params['id'])
            && is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $muscleId = $params['id'];
            $musclesDb = new Model_DbTable_Muscles();
            $muscle = $musclesDb->findMuscle($muscleId);
            
            $this->view->assign($muscle->toArray());
        }
    }
    
    public function getMuscleForEditAction() {
        $params = $this->getRequest()->getParams();
        
        if (isset($params['id'])) {
            $muscleGroupId = $params['id'];
            $muscleXMuscleGroupDb = new Model_DbTable_MuscleXMuscleGroup();
            $musclesCollection = $muscleXMuscleGroupDb->findMusclesByMuscleGroupId($muscleGroupId);
            
            $this->view->assign('musclesCollection', $musclesCollection);
        }
        $this->view->assign('musclesContent', $this->view->render('loops/muscle-edit.phtml'));
    }
    
    public function getMuscleProposalsAction() {
        $params = $this->getRequest()->getParams();
        
        if (isset($params['search'])) {
            $search = '%' . base64_decode($params['search']) . '%';
            $musclesDb = new Model_DbTable_Muscles();
            $musclesCollection = $musclesDb->findMusclesByName($search);

            $muscleProposalsContent = Zend_Registry::get('Zend_Translate')->translate('label_no_muscles_found');

            if((is_array($musclesCollection)
                    || $musclesCollection instanceof Zend_Db_Table_Rowset)
                && 0 < count($musclesCollection))
            {
                $proposalContent = '';
                foreach($musclesCollection as $muscle) {
                    $this->view->assign('proposalText', $muscle['muscle_name']);
                    $this->view->assign('proposalId', $muscle['muscle_id']);
                    $proposalContent .= $this->view->render('globals/proposal-row.phtml');
                }
                $this->view->assign('proposalContent', $proposalContent);
                $muscleProposalsContent = $this->view->render('globals/proposals.phtml');
            }
            $this->view->assign('muscleProposalsContent', $muscleProposalsContent);
        }
    }
    
    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        $messageCollection = array();

        $user = Zend_Auth::getInstance()->getIdentity();

        $userId = 1;

        if (TRUE == is_object($user)) {
            $userId = $user->user_id;
//        } else {
//            array_push($messageCollection, array('type' => 'fehler', 'message' => 'Sie müssen angemeldet sein, um diese Aktion durchführen zu können!', 'result' => false));
//            $hasError = true;
        }

        if (isset($params['edited_elements'])) {
            $musclesDb = new Model_DbTable_Muscles();
            
            $muscleName = '';
            $muscleId = 0;
            $hasError = false;
            $data = array();
            
            if (isset($params['edited_elements']['muscle_name'])
               && 0 < strlen(trim($params['edited_elements']['muscle_name']))
            ) {
                $muscleName = base64_decode($params['edited_elements']['muscle_name']);
            }
            
            if (isset($params['edited_elements']['muscle_id'])) {
                $muscleId = $params['edited_elements']['muscle_id'];
            }
            
            if (0 == strlen(trim($muscleName))
               && !$muscleId
            ) {
                array_push($messageCollection, array('type' => 'fehler', 'message' => $this->translate('tooltip_muscle_needs_name')));
                $hasError = true;
            } else if(0 < strlen(trim($muscleName))) {
                $data['muscle_name'] = $muscleName;
            }
            
            $cadSeo = new CAD_Seo();

            // keine ID aber Name => neu anlegen
            if (! $muscleId
               && strlen(trim($muscleName))
            ) {
                $muscleCurrent = $musclesDb->findMusclesByName($muscleName);
                if (0 < count($muscleCurrent)) {
                    array_push($messageCollection, array('type' => 'fehler', 'message' => $this->translate('tooltip_muscle_already_exists'), 'result' => false));
                    $hasError = true;
                }
            }
            
            if (!$hasError) {
                // updaten?
                if (is_numeric($muscleId)
                   && 0 < $muscleId
                   && 0 < count($data)
                ) {
                    $muscleCurrent = $musclesDb->findMuscle($muscleId);
                    if (
                        (
                            isset($data['muscle_name'])
                            && 0 < strlen(trim($data['muscle_name']))
                            && $muscleCurrent['muscle_name'] != $data['muscle_name']
                        ) ||
                        (
                            isset($muscleCurrent['muscle_name'])
                            && 0 < strlen(trim($muscleCurrent['muscle_name']))
                            && !strlen(trim($muscleCurrent['muscle_name']))
                        )
                    )
                    {
                            if (isset($data['muscle_name'])
                               && 0 < strlen(trim($data['muscle_name']))
                            ) {
                                $muscleName = $data['muscle_name'];
                            }
                            else if(isset($muscleCurrent['muscle_name']) &&
                                    strlen(trim($muscleCurrent['muscle_name'])) > 0)
                            {
                                $muscleName = $musclesDb['muscle_name'];
                            }
                            $cadSeo->setLinkName($muscleName);
                            $cadSeo->setDbTable($musclesDb);
                            $cadSeo->setTableFieldName("muscle_seo_link");
                            $cadSeo->setTableFieldIdName("muscle_id");
                            $cadSeo->setTableFieldId($muscleId);
                            $cadSeo->createSeoLink();
                            $data['muscle_seo_link'] = $cadSeo->getSeoName();
                    }

                    $data['muscle_update_date'] = date("Y-m-d H:i:s");
                    $data['muscle_update_user_fk'] = $userId;

                    $musclesDb->updateMuscle($data, $muscleId);
                    array_push($messageCollection, array('type' => 'meldung', 'message' => $this->translate('tooltip_muscle_edited_successfully'), 'result' => true, 'id' => $muscleId));
                }
                // neu anlegen
                else if(count($data) > 0)
                {
                    $cadSeo->setLinkName($data['muscle_name']);
                    $cadSeo->setDbTable($musclesDb);
                    $cadSeo->setTableFieldName("muscle_seo_link");
                    $cadSeo->setTableFieldIdName("muscle_id");
                    $cadSeo->setTableFieldId($muscleId);
                    $cadSeo->createSeoLink();

                    $data['muscle_seo_link'] = $cadSeo->getSeoName();
                    $data['muscle_create_date'] = date("Y-m-d H:i:s");
                    $data['muscle_create_user_fk'] = $userId;

                    $muscleId = $musclesDb->saveMuscle($data);

                    if ($muscleId) {
                        array_push($messageCollection, array('type' => 'meldung', 'message' => 'Dieser Muskel wurde erfolgreich angelegt!', 'result' => true, 'id' => $muscleId));
                    } else {
                        array_push($messageCollection, array('type' => 'fehler', 'message' => 'Beim Speichern des Muskels trat ein unbekannter Fehler auf!', 'result' => false, 'id' => $muscleId));
                    }
                } else {
                    array_push($messageCollection, array('type' => 'warnung', 'message' => 'Dieser Muskel wurde nicht geändert!', 'result' => true, 'id' => $muscleId));
                }
                
                if ($muscleId) {
                    /* bilder verschieben */
                    /*
                    $obj_files = new CAD_File();
//                    $str_src_path = getcwd() . '/tmp/muscle-groups/';
//                    $str_dest_path = getcwd() . '/images/content/dynamisch/muscle-groups/' . $i_muskelgruppe_id . '/';

                    if($obj_files->checkAndCreateDir($str_dest_path))
                    {
                        $obj_files->setSourcePath($str_src_path);
                        $obj_files->setDestPath($str_dest_path);
                        $obj_files->setAllowedExtensions(array('jpg', 'png', 'gif', 'svg'));
                        $obj_files->^verschiebeFiles();
                    }
                    */
                }
            } else {
                array_push($messageCollection, array('type' => 'fehler', 'message' => 'Beim Speichern des Muskels trat ein unbekannter Fehler auf!', 'result' => false, 'id' => $muscleId));
            }
        } else {
            array_push($messageCollection, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Muskel speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($messageCollection));
    }
    
    public function deleteMuscleAction()
    {
        $params = $this->getRequest()->getParams();
        $messageCollection = array();
        
        if(isset($params['id']) &&
           is_numeric($params['id']) &&
           $params['id'] > 0)
        {
            $i_muskel_id = $params['id'];
            $b_fehler = false;
            
            $obj_db_muskeln = new Model_DbTable_Muscles();
            $obj_db_muskelgruppen = new Model_DbTable_MuscleGroups();
            $obj_db_muskelgruppen_muskeln = new Model_DbTable_MuscleGroupMuscles();
            $obj_db_uebungen = new Model_DbTable_Exercises();
            $obj_db_uebung_muskelgruppen = new Model_DbTable_ExerciseMuscleGroups();
            
            if($obj_db_muskeln->deleteMuscle($i_muskel_id))
            {
                array_push($messageCollection, array('type' => 'meldung', 'message' => 'Muskel erfolgreich gelöscht!', 'result' => true));
            }
            else
            {
                array_push($messageCollection, array('type' => 'fehler', 'message' => 'Muskel konnte leider nicht gelöscht werden!', 'result' => false));
                $b_fehler = true;
            }
            
            // muscle-groups für muskel holen
            $a_muskelgruppen = $obj_db_muskelgruppen_muskeln->findMuscleGroupsByMuscleId($i_muskel_id);
            
            if(is_array($a_muskelgruppen) &&
               count($a_muskelgruppen) > 0 &&
               !$b_fehler)
            {
                foreach($a_muskelgruppen as $a_muskelgruppe)
                {
                    // exercises für muskelgruppe holen
                    $a_uebungen = $obj_db_uebung_muskelgruppen->getUebungenFuerMuskelgruppe($a_muskelgruppe['muskelgruppe_muskel_muskelgruppe_fk']);
                    
                    // betroffene übungen löschen
                    if(is_array($a_uebungen))
                    {
                        foreach($a_uebungen as $a_uebung)
                        {
                            $obj_db_uebungen->deleteExercise($a_uebung['uebung_muskelgruppe_uebung_fk']);
                        }
                    }
                    $obj_db_muskelgruppen->deleteMuscleGroup($a_muskelgruppe['muskelgruppe_muskel_muskelgruppe_fk']);
                    // uebung_muskelgruppen löschen
                    $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppeVonMuskelgruppe($a_muskelgruppe['muskelgruppe_muskel_muskelgruppe_fk']);
                }
            }
            $obj_db_muskelgruppen_muskeln->deleteAllMuscleGroupsMusclesByMuscleId($i_muskel_id);
        }
        else
        {
            
        }
        $this->view->assign('json_string', json_encode($messageCollection));
    }
}

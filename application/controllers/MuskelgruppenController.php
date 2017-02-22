<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MuskelgruppenController extends Zend_Controller_Action {

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
        $obj_db_muskelgruppen = new Application_Model_DbTable_MuscleGroups();
        $obj_db_muskelgruppe_muskeln = new Application_Model_DbTable_MuscleGroupMuscles();

        $a_muskelgruppen = $obj_db_muskelgruppen->findAllMuscleGroups()->toArray();

        foreach ($a_muskelgruppen as &$a_muskelgruppe) {
            $a_muskelgruppe_muskeln = $obj_db_muskelgruppe_muskeln->findMusclesByMuscleGroupId($a_muskelgruppe['muskelgruppe_id']);
            $a_muskelgruppe['a_muskelgruppe_muskeln'] = $a_muskelgruppe_muskeln;
        }
        $this->view->assign('a_muskelgruppen', $a_muskelgruppen);
    }

    public function showAction() {
    }

    public function editAction() {
        $a_params = $this->getRequest()->getParams();

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

        if (isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0
        ) {
            $i_muskelgruppe_id = $a_params['id'];

            $obj_db_muskelgruppen = new Application_Model_DbTable_MuscleGroups();
            $obj_db_muskeln = new Application_Model_DbTable_Muscles();

            $a_muskelgruppe = $obj_db_muskelgruppen->findMuscleGroup($i_muskelgruppe_id);
            $this->view->assign($a_muskelgruppe->toArray());
        }
    }

    public function uebersichtAction() {
    }

    public function loescheMuskelgruppeAction() {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        if (isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0
        ) {
            $i_muskelgruppe_id = $a_params['id'];

            $obj_db_uebungen = new Application_Model_DbTable_Exercises();
            $obj_db_muskelgruppen = new Application_Model_DbTable_MuscleGroups();
            $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_ExerciseMuscleGroups();
            $obj_db_muskelgruppen_muskeln = new Application_Model_DbTable_MuscleGroupMuscles();

            $a_uebungen = $obj_db_uebung_muskelgruppen->getUebungenFuerMuskelgruppe($i_muskelgruppe_id);

            if ($obj_db_muskelgruppen->deleteMuscleGroup($i_muskelgruppe_id)) {
                $obj_db_muskelgruppen_muskeln->deleteAllMuscleGroupsMusclesByMuscleGroupId($i_muskelgruppe_id);
                $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppeVonMuskelgruppe($i_muskelgruppe_id);

                if (is_array($a_uebungen) &&
                    count($a_uebungen) > 0
                ) {
                    foreach ($a_uebungen as $a_uebung) {
                        $obj_db_uebungen->deleteExercise($a_uebung['uebung_muskelgruppe_uebung_fk']);
                    }
                }

                $i_count_message = count($a_messages);
                $a_messages[$i_count_message]['type'] = "meldung";
                $a_messages[$i_count_message]['message'] = "Muskelgruppe und mit Ihr verknüpfte Übungen erfolgreich gelöscht!";
                $a_messages[$i_count_message]['result'] = true;

                $bilder_pfad = getcwd() . '/images/content/dynamisch/muskelgruppen/' . $i_muskelgruppe_id . '/';

                $obj_file = new CAD_File();
                $obj_file->cleanDirRek($bilder_pfad, 2);
            } else {
                $i_count_message = count($a_messages);
                $a_messages[$i_count_message]['type'] = "fehler";
                $a_messages[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
                $a_messages[$i_count_message]['result'] = false;
            }
        } else {
            $i_count_message = count($a_messages);
            $a_messages[$i_count_message]['type'] = "fehler";
            $a_messages[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
            $a_messages[$i_count_message]['result'] = false;
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }

    /**
     * eine muskelgruppe als edit feld zurück geben
     */
    public function getMuskelnFuerEditAction() {
        $a_params = $this->getRequest()->getParams();

        if (isset($a_params['id'])) {
            $i_muskelgruppe_id = $a_params['id'];
            $obj_db_muskeln = new Application_Model_DbTable_Muscles();
            $obj_db_muskelgruppe_muskeln = new Application_Model_DbTable_MuscleGroupMuscles();

            $a_muskelgruppe_muskeln = $obj_db_muskelgruppe_muskeln->findMusclesByMuscleGroupId($i_muskelgruppe_id);

            $this->view->assign('a_muskelgruppe_muskeln', $a_muskelgruppe_muskeln);
        }
    }

    public function getMuskelgruppeFuerEditAction() {
        $aParams = $this->getAllParams();
        $aMessages = array();

        if (isset($aParams['id'])) {
            $iMuskelGruppeId = $aParams['id'];
            $oMuskelGruppenStorage = new Application_Model_DbTable_MuscleGroups();
            $aMuskelGruppeInclMuskeln = $oMuskelGruppenStorage->findMuscleGroup($iMuskelGruppeId);
            $aMuskelGruppe = array();

            foreach ($aMuskelGruppeInclMuskeln as $aMuskel) {
                $aMuskelGruppe['muskelgruppe_name'] = $aMuskel->muskelgruppe_name;
                $aMuskelGruppe['muskelgruppe_id'] = $aMuskel->muskelgruppe_id;
                $aMuskelGruppe['muskeln'][] = $aMuskel->toArray();
            }
            $this->view->assign('aMuskelGruppe', $aMuskelGruppe);
        }

        if (count($aMessages) > 0) {
            $this->view->assign('json_string', json_encode($aMessages));
        }
    }

    /*
    public function getMuskelVorschlaegeAction()
    {
        $a_params = $this->getRequest()->getParams();
        
        if(isset($a_params['suche']))
        {
            $str_suche = base64_decode($a_params['suche']) . '%';
            $obj_db_muskeln = new Application_Model_DbTable_Muscles();
            
            $a_muskel_vorschlaege = $obj_db_muskeln->getMuskelByName($str_suche);
            $this->view->assign('a_muskel_vorschlaege', $a_muskel_vorschlaege);
        }
    }
    */
    public function getMuskelgruppenVorschlaegeAction() {
        $a_params = $this->getRequest()->getParams();

        if (isset($a_params['suche'])) {
            $str_suche = base64_decode($a_params['suche']) . '%';
            $obj_db_muskelgruppen = new Application_Model_DbTable_MuscleGroups();

            $a_muskelgruppen_vorschlaege = $obj_db_muskelgruppen->findMuscleGroupsByName($str_suche);
            $this->view->assign('a_muskelgruppen_vorschlaege', $a_muskelgruppen_vorschlaege);
        }
    }

    public function speichernAction() {
        $a_params = $this->getRequest()->getParams();

        $obj_user = Zend_Auth::getInstance()->getIdentity();

        if (isset($a_params['edited_elements'])) {
            $obj_db_muskelgruppen = new Application_Model_DbTable_MuscleGroups();
            $obj_db_muskelgruppe_muskeln = new Application_Model_DbTable_MuscleGroupMuscles();

            $muskelgruppe_name = '';
            $a_muskelgruppe_muskeln = array();
            $a_muskelgruppe_muskeln_updaten = array();
            $a_muskelgruppe_muskeln_loeschen = array();
            $a_muskelgruppe_muskeln_hinzufuegen = array();
            $i_muskelgruppe_id = 0;
            $i_count_muskelgruppen = 0;
            $b_fehler = false;
            $a_messages = array();
            $a_data = array();

            if (isset($a_params['edited_elements']['muskelgruppe_name'])
                && 0 < strlen(trim($a_params['edited_elements']['muskelgruppe_name']))
            ) {
                $muskelgruppe_name = base64_decode($a_params['edited_elements']['muskelgruppe_name']);
            }

            if (isset($a_params['edited_elements']['muskelgruppe_id'])) {
                $i_muskelgruppe_id = $a_params['edited_elements']['muskelgruppe_id'];
            }

            /**
             * @todo hier muss noch eine möglichkeit gefunden werden, die
             * übergebenen muskeln für die muskelgruppe zu validieren und
             * zu checken, ob wenigstens eine am ende erhalten bleibt
             */
            if (isset($a_params['edited_elements']['muskelgruppe_muskeln'])) {
                $a_muskelgruppe_muskeln_aktuell = array();

                $obj_db_muskelgruppe_muskeln = new Application_Model_DbTable_MuscleGroupMuscles();
                $a_muskelgruppe_muskeln_aktuell_roh = $obj_db_muskelgruppe_muskeln->findMusclesByMuscleGroupId($i_muskelgruppe_id);
                $a_muskelgruppe_muskeln_uebergeben = $a_muskelgruppe_muskeln;
                $i_count_muskelgruppen = count($a_muskelgruppe_muskeln_aktuell_roh);

                if (is_array($a_muskelgruppe_muskeln_aktuell_roh)) {
                    foreach ($a_muskelgruppe_muskeln_aktuell_roh as $a_muskel) {
                        // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                        $a_muskelgruppe_muskeln_aktuell[$a_muskel['muskelgruppe_muskel_muskel_fk']] = array(
                            'muskelgruppe_muskel_id' => $a_muskel['muskelgruppe_muskel_id'],
                            'muskel_id' => $a_muskel['muskelgruppe_muskel_muskel_fk'],
                            'muskel_beanspruchung' => $a_muskel['muskelgruppe_muskel_beanspruchung']
                        );
                    }
                }

                foreach ($a_params['edited_elements']['muskelgruppe_muskeln'] as $a_muskel) {
                    $i_muskel_beanspruchung = 0;

                    if (isset($a_muskel['beanspruchung'])) {
                        $i_muskel_beanspruchung = $a_muskel['beanspruchung'];
                    }
                    // es wurde eine id übergeben und diese id bestand bereits
                    if (isset($a_muskel['id']) &&
                        $a_muskel['id'] > 0 &&
                        isset($a_muskelgruppe_muskeln_aktuell[$a_muskel['id']])
                    ) {
                        // erstmal 1 damit auch leere beanspruchungen verwendet werden
                        if (true ||
                            isset($a_muskel['beanspruchung']) &&
                            $i_muskel_beanspruchung > 0 &&
                            $i_muskel_beanspruchung != $a_muskelgruppe_muskeln_aktuell[$a_muskel['id']]['muskel_beanspruchung']
                        ) {
                            array_push($a_muskelgruppe_muskeln_updaten, array(
                                    'muskel_id' => $a_muskel['id'],
                                    'muskelgruppe_muskel_id' => $a_muskelgruppe_muskeln_aktuell[$a_muskel['id']]['muskelgruppe_muskel_id'],
                                    'muskel_beanspruchung' => $i_muskel_beanspruchung
                                )
                            );
                        } else if (! $i_muskel_beanspruchung) {
                            //                            array_push($a_muskelgruppe_muskeln_loeschen, $a_muskelgruppe_muskeln_aktuell[$a_muskel['id']]);
                            array_push($a_muskelgruppe_muskeln_loeschen,
                                $a_muskelgruppe_muskeln_aktuell[$a_muskel['id']]['muskelgruppe_muskel_id']);
                            $i_count_muskelgruppen--;
                        }
                    } else if (isset($a_muskel['id']) &&
                        $a_muskel['id'] > 0
                    )
                        //                            $a_muskel['id'] > 0 &&
                        //                            isset($a_muskel['beanspruchung']) &&
                        //                            $a_muskel['beanspruchung'] > 0)
                    {
                        array_push($a_muskelgruppe_muskeln_hinzufuegen, array(
                                'muskel_id' => $a_muskel['id'],
                                'muskel_beanspruchung' => $i_muskel_beanspruchung
                            )
                        );
                        $i_count_muskelgruppen++;
                    }
                }
            }

            if (0 == strlen(trim($muskelgruppe_name)) &&
                ! $i_muskelgruppe_id
            ) {
                array_push($a_messages,
                    array('type' => 'fehler', 'message' => 'Diese Muskelgruppe benötigt einen Namen'));
                $b_fehler = true;
            } else if (0 < strlen(trim($muskelgruppe_name)) &&
                ! $i_muskelgruppe_id
            ) {
                $a_data['muskelgruppe_name'] = $muskelgruppe_name;
            }

            if ($i_count_muskelgruppen <= 0) {
                array_push($a_messages, array(
                    'type' => 'fehler', 'message' => 'Diese Muskelgruppe benötigt mindestens einen beanspruchten Muskel'
                ));
                $b_fehler = true;
            }

            $obj_cad_seo = new CAD_Seo();

            if (! $i_muskelgruppe_id &&
                strlen(trim($muskelgruppe_name))
            ) {
                $a_muskelgruppe_aktuell = $obj_db_muskelgruppen->findMuscleGroupsByName($muskelgruppe_name);
                if (is_array($a_muskelgruppe_aktuell) &&
                    count($a_muskelgruppe_aktuell) > 0
                ) {
                    array_push($a_messages, array(
                        'type' => 'fehler', 'message' => 'Muskelgruppe "' . $muskelgruppe_name . '" existiert bereits!',
                        'result' => false
                    ));
                    $b_fehler = true;
                }
            }

            if (! $b_fehler) {
                // updaten?
                if (is_numeric($i_muskelgruppe_id) &&
                    0 < $i_muskelgruppe_id &&
                    is_array($a_data) &&
                    count($a_data) > 0
                ) {

                    $a_muskelgruppe_aktuell = $obj_db_muskelgruppen->findMuscleGroup($i_muskelgruppe_id);

                    if (
                        (
                            isset($a_data['muskelgruppe_name']) &&
                            strlen(trim($a_data['muskelgruppe_name'])) > 0 &&
                            $a_muskelgruppe_aktuell['muskelgruppe_name'] !=
                            $a_data['muskelgruppe_name']
                        ) ||
                        (
                            isset($a_muskelgruppe_aktuell['muskelgruppe_name']) &&
                            strlen(trim($a_muskelgruppe_aktuell['muskelgruppe_name'])) > 0 &&
                            ! strlen(trim($a_muskelgruppe_aktuell['muskelgruppe_seo_link']))
                        )
                    ) {
                        if (isset($a_data['muskelgruppe_name']) &&
                            strlen(trim($a_data['muskelgruppe_name'])) > 0
                        ) {
                            $muskelgruppe_name = $a_data['muskelgruppe_name'];
                        } else if (isset($a_muskelgruppe_aktuell['muskelgruppe_name']) &&
                            strlen(trim($a_muskelgruppe_aktuell['muskelgruppe_name'])) > 0
                        ) {
                            $muskelgruppe_name = $a_muskelgruppe_aktuell['muskelgruppe_name'];
                        }
                        $obj_cad_seo->setLinkName($muskelgruppe_name);
                        $obj_cad_seo->setDbTable($obj_db_muskelgruppen);
                        $obj_cad_seo->setTableFieldName("muskelgruppe_seo_link");
                        $obj_cad_seo->setTableFieldIdName("muskelgruppe_id");
                        $obj_cad_seo->setTableFieldId($i_muskelgruppe_id);
                        $obj_cad_seo->createSeoLink();
                        $a_data['muskelgruppe_seo_link'] = $obj_cad_seo->getSeoName();
                    }
                    $a_data['muskelgruppe_aenderung_datum'] = date("Y-m-d H:i:s");
                    $a_data['muskelgruppe_aenderung_user_fk'] = $obj_user->user_id;

                    $obj_db_muskelgruppen->updateMuscleGroup($a_data, $i_muskelgruppe_id);
                    array_push($a_messages, array(
                        'type' => 'meldung', 'message' => 'Diese Muskelgruppe wurde erfolgreich bearbeitet!',
                        'result' => true, 'id' => $i_muskelgruppe_id
                    ));
                } // neu anlegen
                else if (is_array($a_data) &&
                    count($a_data) > 0
                ) {
                    $obj_cad_seo->setLinkName($a_data['muskelgruppe_name']);
                    $obj_cad_seo->setDbTable($obj_db_muskelgruppen);
                    $obj_cad_seo->setTableFieldName("muskelgruppe_seo_link");
                    $obj_cad_seo->setTableFieldIdName("muskelgruppe_id");
                    $obj_cad_seo->setTableFieldId($i_muskelgruppe_id);
                    $obj_cad_seo->createSeoLink();

                    $a_data['muskelgruppe_seo_link'] = $obj_cad_seo->getSeoName();
                    $a_data['muskelgruppe_eintrag_datum'] = date("Y-m-d H:i:s");
                    $a_data['muskelgruppe_eintrag_user_fk'] = $obj_user->user_id;

                    $i_muskelgruppe_id = $obj_db_muskelgruppen->saveMuscleGroup($a_data);
                    array_push($a_messages, array(
                        'type' => 'meldung', 'message' => 'Diese Muskelgruppe wurde erfolgreich angelegt!',
                        'result' => true, 'id' => $i_muskelgruppe_id
                    ));
                }
                if (count($a_muskelgruppe_muskeln_hinzufuegen) == 0 &&
                    count($a_muskelgruppe_muskeln_updaten) == 0 &&
                    count($a_muskelgruppe_muskeln_loeschen) == 0
                ) {
                    array_push($a_messages, array(
                        'type' => 'meldung', 'message' => 'Diese Muskelgruppe wurde nicht geändert!', 'result' => true,
                        'id' => $i_muskelgruppe_id
                    ));
                }

                if (count($a_muskelgruppe_muskeln_hinzufuegen) > 0 ||
                    count($a_muskelgruppe_muskeln_updaten) > 0 ||
                    count($a_muskelgruppe_muskeln_loeschen) > 0
                ) {
                    array_push($a_messages, array(
                        'type' => 'meldung', 'message' => 'Die Muskeln der Muskelgruppen wurden erfolgreich geändert!',
                        'result' => true, 'id' => $i_muskelgruppe_id
                    ));
                }

                if ($i_muskelgruppe_id) {
                    /* bilder verschieben */
                    /*
                    $obj_files = new CAD_File();
//                    $str_src_path = getcwd() . '/tmp/muskelgruppen/';
//                    $str_dest_path = getcwd() . '/images/content/dynamisch/muskelgruppen/' . $i_muskelgruppe_id . '/';

                    if($obj_files->checkAndCreateDir($str_dest_path))
                    {
                        $obj_files->setSourcePath($str_src_path);
                        $obj_files->setDestPath($str_dest_path);
                        $obj_files->setAllowedExtensions(array('jpg', 'png', 'gif', 'svg'));
                        $obj_files->verschiebeFiles();
                    }
                    */
                    /* muskelgruppen bearbeiten */

                    foreach ($a_muskelgruppe_muskeln_hinzufuegen as $a_muskelgruppe_muskel) {
                        $a_data = array();
                        $a_data['muskelgruppe_muskel_muskel_fk'] = $a_muskelgruppe_muskel['muskel_id'];
                        $a_data['muskelgruppe_muskel_beanspruchung'] = $a_muskelgruppe_muskel['muskel_beanspruchung'];
                        $a_data['muskelgruppe_muskel_muskelgruppe_fk'] = $i_muskelgruppe_id;
                        $a_data['muskelgruppe_muskel_eintrag_datum'] = date("Y-m-d H:i:s");
                        $a_data['muskelgruppe_muskel_eintrag_user_fk'] = $obj_user->user_id;

                        $obj_db_muskelgruppe_muskeln->saveMuscleGroupMuscle($a_data);
                    }

                    foreach ($a_muskelgruppe_muskeln_updaten as $a_muskelgruppe_muskel) {
                        $a_data = array();
                        $a_data['muskelgruppe_muskel_muskelgruppe_fk'] = $i_muskelgruppe_id;
                        $a_data['muskelgruppe_muskel_beanspruchung'] = $a_muskelgruppe_muskel['muskel_beanspruchung'];
                        $a_data['muskelgruppe_muskel_eintrag_datum'] = date("Y-m-d H:i:s");
                        $a_data['muskelgruppe_muskel_eintrag_user_fk'] = $obj_user->user_id;

                        $obj_db_muskelgruppe_muskeln->updateMuscleGroupMuscle($a_data,
                            $a_muskelgruppe_muskel['muskelgruppe_muskel_id']);
                    }

                    foreach ($a_muskelgruppe_muskeln_loeschen as $i_muskelgruppe_muskel_id) {
                        $obj_db_muskelgruppe_muskeln->deleteMuscleGroupMuscle($i_muskelgruppe_muskel_id);
                    }
                }
            } else {
                array_push($a_messages, array(
                    'type' => 'fehler', 'message' => 'Es gabe einen Fehler bei Muskelgruppe speichern!',
                    'result' => false
                ));
            }
        } else {
            array_push($a_messages, array(
                'type' => 'fehler', 'message' => 'Falscher Aufruf von Muskelgruppe speichern!', 'result' => false
            ));
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }
}

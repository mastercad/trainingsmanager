<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

class GeraetegruppenController extends Zend_Controller_Action
{
    protected $breadcrumb;
    protected $schlagwoerter;
    protected $beschreibung;

    public function init()
    {
    }

    public function postDispatch()
    {
        $this->view->assign('breadcrumb', $this->breadcrumb);

        $a_params = $this->getRequest()->getParams();

        if(isset($a_params['ajax']))
        {
            $this->view->layout()->disableLayout();
        }

        $this->view->headMeta()->appendName('keywords', $this->schlagwoerter);
        $this->view->headMeta()->appendName('description', $this->beschreibung);
    }

    public function indexAction()
    {
        $obj_db_geraetegruppen = new Application_Model_DbTable_Geraetegruppen();
        $obj_db_geraetegruppe_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();

        $a_geraetegruppen = $obj_db_geraetegruppen->getGeraetegruppen();

        foreach($a_geraetegruppen as &$a_geraetegruppe)
        {
            $a_geraetegruppe_geraete = $obj_db_geraetegruppe_geraete->getGeraeteFuerGeraetegruppe($a_geraetegruppe['geraetegruppe_id']);
            $a_geraetegruppe['a_geraetegruppe_geraete'] = $a_geraetegruppe_geraete;
        }
        $this->view->assign('a_geraetegruppen', $a_geraetegruppen);
    }

    public function showAction()
    {

    }

    public function editAction()
    {
        $a_params = $this->getRequest()->getParams();

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');

        if(isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0)
        {
            $i_geraetegruppe_id = $a_params['id'];

            $obj_db_geraetegruppen = new Application_Model_DbTable_Geraetegruppen();
//            $obj_db_geraetegruppe_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();

            $a_geraetegruppe = $obj_db_geraetegruppen->getGeraetegruppe($i_geraetegruppe_id);
//            $a_geraete = $obj_db_geraetegruppe_geraete->getGeraeteFuerGeraetegruppe($i_geraetegruppe_id);

//            $this->view->assign('a_geraete', $a_geraete);
            $this->view->assign($a_geraetegruppe);
        }
    }

    public function uebersichtAction()
    {

    }

    public function loescheGeraetegruppeAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        if(isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0)
        {
            $i_geraetegruppe_id = $a_params['id'];

            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $obj_db_geraetegruppen = new Application_Model_DbTable_Geraetegruppen();
            $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
            $obj_db_geraetegruppen_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();
            $a_geraete = $obj_db_geraetegruppen_geraete->getGeraeteFuerGeraetegruppe($i_geraetegruppe_id);

            if($obj_db_geraetegruppen->loescheGeraetegruppe($i_geraetegruppe_id))
            {
                if(is_array($a_geraete) &&
                    count($a_geraete) > 0)
                {
                    foreach($a_geraete as $a_geraet)
                    {
                        $a_uebungen = $obj_db_uebungen->getUebungenFuerGeraet($a_geraet['geraet_id']);
                        if(is_array($a_uebungen) &&
                           count($a_uebungen) > 0)
                        {
                            foreach($a_uebungen as $a_uebung)
                            {
                                $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppeFuerUebung($a_uebung['uebung_id']);
                                $obj_db_uebungen->loescheUebung($a_uebung['uebung_id']);
                            }
                        }
                        $obj_db_geraete->loescheGeraet($a_geraet['geraet_id']);
                    }
                }

                $obj_db_geraetegruppen_geraete->loescheAlleGeraetegruppeGeraeteFuerGeraetegruppe($i_geraetegruppe_id);

                $i_count_message = count($a_messages);
                $a_messages[$i_count_message]['type'] = "meldung";
                $a_messages[$i_count_message]['message'] = "Geraetegruppe und mit Ihr verknüpfte Übungen erfolgreich gelöscht!";
                $a_messages[$i_count_message]['result'] = true;

                $bilder_pfad = getcwd() . '/images/content/dynamisch/geraetegruppen/' . $i_geraetegruppe_id . '/';

                $obj_file = new CAD_File();
                $obj_file->cleanDirRek($bilder_pfad, 2);
            }
            else
            {
                $i_count_message = count($a_messages);
                $a_messages[$i_count_message]['type'] = "fehler";
                $a_messages[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
                $a_messages[$i_count_message]['result'] = false;
            }
        }
        else
        {
            $i_count_message = count($a_messages);
            $a_messages[$i_count_message]['type'] = "fehler";
            $a_messages[$i_count_message]['message'] = "Übung konnte nicht gelöscht werden!";
            $a_messages[$i_count_message]['result'] = false;
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }

    /**
     * eine geraetegruppe als edit feld zurück geben
     */
    public function getGeraeteFuerEditAction()
    {
        $a_params = $this->getRequest()->getParams();

        if(isset($a_params['id']))
        {
            $i_geraetegruppe_id = $a_params['id'];
            $obj_db_geraetegruppe_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();
            $a_geraetegruppe_geraete = $obj_db_geraetegruppe_geraete->getGeraeteFuerGeraetegruppe($i_geraetegruppe_id);

            $this->view->assign('a_geraetegruppe_geraete', $a_geraetegruppe_geraete);
        }
    }
    /*
    public function getGeraetVorschlaegeAction()
    {
        $a_params = $this->getRequest()->getParams();
        
        if(isset($a_params['suche']))
        {
            $str_suche = base64_decode($a_params['suche']) . '%';
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            
            $a_geraet_vorschlaege = $obj_db_geraete->getGeraetByName($str_suche);
            $this->view->assign('a_geraet_vorschlaege', $a_geraet_vorschlaege);
        }
    }
    */
    public function getGeraetegruppenVorschlaegeAction()
    {
        $a_params = $this->getRequest()->getParams();

        if(isset($a_params['suche']))
        {
            $str_suche = base64_decode($a_params['suche']) . '%';
            $obj_db_geraetegruppen = new Application_Model_DbTable_Geraetegruppen();

            $a_geraetegruppen_vorschlaege = $obj_db_geraetegruppen->getGeraetegruppenByName($str_suche);
            $this->view->assign('a_geraetegruppen_vorschlaege', $a_geraetegruppen_vorschlaege);
        }
    }

    public function speichernAction()
    {
        $a_params = $this->getRequest()->getParams();

        $obj_user = Zend_Auth::getInstance()->getIdentity();

        if(isset($a_params['edited_elements']))
        {
            $obj_db_geraetegruppen = new Application_Model_DbTable_Geraetegruppen();
            $obj_db_geraetegruppe_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();

            $geraetegruppe_name = '';
            $a_geraetegruppe_geraete = array();
            $a_geraetegruppe_geraete_updaten = array();
            $a_geraetegruppe_geraete_loeschen = array();
            $a_geraetegruppe_geraete_hinzufuegen = array();
            $i_geraetegruppe_id = 0;
            $i_count_geraetegruppen = 0;
            $b_fehler = false;
            $a_messages = array();
            $a_data = array();

            if(isset($a_params['edited_elements']['geraetegruppe_name']) &&
                0 < strlen(trim($a_params['edited_elements']['geraetegruppe_name'])))
            {
                $geraetegruppe_name = base64_decode($a_params['edited_elements']['geraetegruppe_name']);
            }

            if(isset($a_params['edited_elements']['geraetegruppe_id']))
            {
                $i_geraetegruppe_id = $a_params['edited_elements']['geraetegruppe_id'];
            }

            /**
             * @todo hier muss noch eine möglichkeit gefunden werden, die
             * übergebenen geraete für die geraetegruppe zu validieren und
             * zu checken, ob wenigstens eine am ende erhalten bleibt
             */
            if(isset($a_params['edited_elements']['geraetegruppe_geraete']))
            {
                $a_geraetegruppe_geraete_aktuell = array();

                $obj_db_geraetegruppe_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();
                $a_geraetegruppe_geraete_aktuell_roh = $obj_db_geraetegruppe_geraete->getGeraeteFuerGeraetegruppe($i_geraetegruppe_id);
                $a_geraetegruppe_geraete_uebergeben = $a_geraetegruppe_geraete;
                $i_count_geraetegruppen = count($a_geraetegruppe_geraete_aktuell_roh);

                if(is_array($a_geraetegruppe_geraete_aktuell_roh))
                {
                    foreach($a_geraetegruppe_geraete_aktuell_roh as $a_geraet)
                    {
                        // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                        $a_geraetegruppe_geraete_aktuell[$a_geraet['geraetegruppe_geraet_geraet_fk']] = array(
                            'geraetegruppe_geraet_id' => $a_geraet['geraetegruppe_geraet_id'],
                            'geraet_id' => $a_geraet['geraetegruppe_geraet_geraet_fk']
                        );
                    }
                    $a_geraetegruppe_geraete_loeschen = $a_geraetegruppe_geraete_aktuell;
                }
                foreach($a_params['edited_elements']['geraetegruppe_geraete'] as $a_geraet)
                {
                    // es wurde eine id übergeben und diese id bestand bereits
                    if(isset($a_geraet['id']) &&
                        $a_geraet['id'] > 0 &&
                        isset($a_geraetegruppe_geraete_aktuell[$a_geraet['id']]))
                    {
                        array_push($a_geraetegruppe_geraete_updaten, array(
                                'geraet_id'                 => $a_geraet['id'],
                                'geraetegruppe_geraet_id'    => $a_geraetegruppe_geraete_aktuell[$a_geraet['id']]['geraetegruppe_geraet_id'])
                        );
                        unset($a_geraetegruppe_geraete_loeschen[$a_geraet['id']]);
                    }
                    else if(isset($a_geraet['id']) &&
                        $a_geraet['id'] > 0)
                    {
                        array_push($a_geraetegruppe_geraete_hinzufuegen, array(
                                'geraet_id'                 => $a_geraet['id'])
                        );
                        $i_count_geraetegruppen++;
                    }
                }
            }

            if(0 == strlen(trim($geraetegruppe_name)) &&
                !$i_geraetegruppe_id)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Geraetegruppe benötigt einen Namen'));
                $b_fehler = true;
            }
            else if(0 < strlen(trim($geraetegruppe_name)) &&
                !$i_geraetegruppe_id)
            {
                $a_data['geraetegruppe_name'] = $geraetegruppe_name;
            }

            if($i_count_geraetegruppen <= 0)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Geraetegruppe benötigt mindestens ein Geraet'));
                $b_fehler = true;
            }

            $obj_cad_seo = new CAD_Seo();

            if(!$i_geraetegruppe_id &&
                strlen(trim($geraetegruppe_name)))
            {
                $a_geraetegruppe_aktuell = $obj_db_geraetegruppen->getGeraetegruppenByName($geraetegruppe_name);
                if(is_array($a_geraetegruppe_aktuell) &&
                    count($a_geraetegruppe_aktuell) > 0)
                {
                    array_push($a_messages, array('type' => 'fehler', 'message' => 'Geraetegruppe "' . $geraetegruppe_name . '" existiert bereits!', 'result' => false));
                    $b_fehler = true;
                }
            }

            if(!$b_fehler) {
                // updaten?
                if(is_numeric($i_geraetegruppe_id) &&
                    0 < $i_geraetegruppe_id &&
                    is_array($a_data) &&
                    count($a_data) > 0) {

                    $a_geraetegruppe_aktuell = $obj_db_geraetegruppen->getGeraetegruppe($i_geraetegruppe_id);

                    if(
                        (
                            isset($a_data['geraetegruppe_name']) &&
                            strlen(trim($a_data['geraetegruppe_name'])) > 0 &&
                            $a_geraetegruppe_aktuell['geraetegruppe_name'] !=
                            $a_data['geraetegruppe_name']
                        ) ||
                        (
                            isset($a_geraetegruppe_aktuell['geraetegruppe_name']) &&
                            strlen(trim($a_geraetegruppe_aktuell['geraetegruppe_name'])) > 0 &&
                            !strlen(trim($a_geraetegruppe_aktuell['geraetegruppe_seo_link']))
                        )
                    )
                    {
                        if(isset($a_data['geraetegruppe_name']) &&
                            strlen(trim($a_data['geraetegruppe_name'])) > 0)
                        {
                            $geraetegruppe_name = $a_data['geraetegruppe_name'];
                        }
                        else if(isset($a_geraetegruppe_aktuell['geraetegruppe_name']) &&
                            strlen(trim($a_geraetegruppe_aktuell['geraetegruppe_name'])) > 0)
                        {
                            $geraetegruppe_name = $a_geraetegruppe_aktuell['geraetegruppe_name'];
                        }
                        $obj_cad_seo->setLinkName($geraetegruppe_name);
                        $obj_cad_seo->setDbTable($obj_db_geraetegruppen);
                        $obj_cad_seo->setTableFieldName("geraetegruppe_seo_link");
                        $obj_cad_seo->setTableFieldIdName("geraetegruppe_id");
                        $obj_cad_seo->setTableFieldId($i_geraetegruppe_id);
                        $obj_cad_seo->createSeoLink();
                        $a_data['geraetegruppe_seo_link'] = $obj_cad_seo->getSeoName();
                    }

                    $a_data['geraetegruppe_aenderung_datum'] = date("Y-m-d H:i:s");
                    $a_data['geraetegruppe_aenderung_user_fk'] = $obj_user->user_id;

                    $obj_db_geraetegruppen->updateGeraetegruppe($a_data, $i_geraetegruppe_id);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Diese Geraetegruppe wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $i_geraetegruppe_id));
                }
                // neu anlegen
                else if(is_array($a_data) &&
                    count($a_data) > 0) {
                    $obj_cad_seo->setLinkName($a_data['geraetegruppe_name']);
                    $obj_cad_seo->setDbTable($obj_db_geraetegruppen);
                    $obj_cad_seo->setTableFieldName("geraetegruppe_seo_link");
                    $obj_cad_seo->setTableFieldIdName("geraetegruppe_id");
                    $obj_cad_seo->setTableFieldId($i_geraetegruppe_id);
                    $obj_cad_seo->createSeoLink();

                    $a_data['geraetegruppe_seo_link'] = $obj_cad_seo->getSeoName();
                    $a_data['geraetegruppe_eintrag_datum'] = date("Y-m-d H:i:s");
                    $a_data['geraetegruppe_eintrag_user_fk'] = $obj_user->user_id;

                    $i_geraetegruppe_id = $obj_db_geraetegruppen->setGeraetegruppe($a_data);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Diese Geraetegruppe wurde erfolgreich angelegt!', 'result' => true, 'id' => $i_geraetegruppe_id));
                }
                if(count($a_geraetegruppe_geraete_hinzufuegen) == 0 &&
                    count($a_geraetegruppe_geraete_updaten) == 0 &&
                    count($a_geraetegruppe_geraete_loeschen) == 0 &&
                    $i_geraetegruppe_id > 0)
                {
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Diese Geraetegruppe wurde nicht geändert!', 'result' => true, 'id' => $i_geraetegruppe_id));
                }

                if( $i_geraetegruppe_id > 0 &&
                    (
                        count($a_geraetegruppe_geraete_hinzufuegen) > 0 ||
                        count($a_geraetegruppe_geraete_updaten) > 0 ||
                        count($a_geraetegruppe_geraete_loeschen) > 0)
                )
                {
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Die Geraete der Geraetegruppen wurden erfolgreich geändert!', 'result' => true, 'id' => $i_geraetegruppe_id));
                }

                if($i_geraetegruppe_id)
                {
                    /* bilder verschieben */
                    /*
                    $obj_files = new CAD_File();
//                    $str_src_path = getcwd() . '/tmp/geraetegruppen/';
//                    $str_dest_path = getcwd() . '/images/content/dynamisch/geraetegruppen/' . $i_geraetegruppe_id . '/';

                    if($obj_files->checkAndCreateDir($str_dest_path))
                    {
                        $obj_files->setSourcePath($str_src_path);
                        $obj_files->setDestPath($str_dest_path);
                        $obj_files->setAllowedExtensions(array('jpg', 'png', 'gif', 'svg'));
                        $obj_files->verschiebeFiles();
                    }
                    */
                    /* geraetegruppen bearbeiten */

                    foreach($a_geraetegruppe_geraete_hinzufuegen as $a_geraetegruppe_geraet)
                    {
                        $a_data = array();
                        $a_data['geraetegruppe_geraet_geraet_fk']            = $a_geraetegruppe_geraet['geraet_id'];
                        $a_data['geraetegruppe_geraet_geraetegruppe_fk']      = $i_geraetegruppe_id;
                        $a_data['geraetegruppe_geraet_eintrag_datum']        = date("Y-m-d H:i:s");
                        $a_data['geraetegruppe_geraet_eintrag_user_fk']      = $obj_user->user_id;

                        $obj_db_geraetegruppe_geraete->setGeraetegruppeGeraet($a_data);
                    }

                    foreach($a_geraetegruppe_geraete_updaten as $a_geraetegruppe_geraet)
                    {
                        $a_data = array();
                        $a_data['geraetegruppe_geraet_geraetegruppe_fk']   = $i_geraetegruppe_id;
                        $a_data['geraetegruppe_geraet_eintrag_datum']     = date("Y-m-d H:i:s");
                        $a_data['geraetegruppe_geraet_eintrag_user_fk']   = $obj_user->user_id;

                        $obj_db_geraetegruppe_geraete->updateGeraetegruppeGeraet($a_data, $a_geraetegruppe_geraet['geraetegruppe_geraet_id']);
                    }

                    foreach($a_geraetegruppe_geraete_loeschen as $a_geraet)
                    {
                        $obj_db_geraetegruppe_geraete->loescheGeraetAusGeraetegruppe($a_geraet['geraetegruppe_geraet_id']);
                    }
                }
            }
            else
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Es gabe einen Fehler bei Geraetegruppe speichern!', 'result' => false));
            }
        }
        else
        {
            array_push($a_messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Geraetegruppe speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }
}

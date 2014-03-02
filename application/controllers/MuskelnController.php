<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MuskelnController extends Zend_Controller_Action
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
        $obj_db_muskeln = new Application_Model_DbTable_Muskeln();
        $a_muskeln = $obj_db_muskeln->getMuskeln();
        
        $this->view->assign('a_muskeln', $a_muskeln);
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
            $i_muskel_id = $a_params['id'];
            $obj_db_muskeln = new Application_Model_DbTable_Muskeln();
            $a_muskel = $obj_db_muskeln->getMuskel($i_muskel_id);
            
            $this->view->assign($a_muskel);
        }
    }
    
    public function uebersichtAction()
    {
        
    }
    
    public function getMuskelnFuerEditAction()
    {
        $a_params = $this->getRequest()->getParams();
        
        if(isset($a_params['id']))
        {
            $i_muskelgruppe_id = $a_params['id'];
            $obj_db_muskeln = new Application_Model_DbTable_Muskeln();
            $a_muskeln = $obj_db_muskeln->getMuskelnFuerMuskelgruppe($i_muskelgruppe_id);
            
            $this->view->assign('a_muskeln', $a_muskeln);
        }
    }
    
    public function getMuskelVorschlaegeAction()
    {
        $a_params = $this->getRequest()->getParams();
        
        if(isset($a_params['suche']))
        {
            $str_suche = base64_decode($a_params['suche']) . '%';
            $obj_db_muskeln = new Application_Model_DbTable_Muskeln();
            $a_muskeln = $obj_db_muskeln->getMuskelByName($str_suche);
            
            $this->view->assign('a_muskeln_vorschlaege', $a_muskeln);
        }
    }
    
    public function speichernAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        $obj_user = Zend_Auth::getInstance()->getIdentity();

        if(isset($a_params['edited_elements'])) {
            $obj_db_muskeln = new Application_Model_DbTable_Muskeln();
            
            $muskel_name = '';
            $i_muskel_id = 0;
            $b_fehler = false;
            $a_data = array();
            
            if(isset($a_params['edited_elements']['muskel_name']) &&
               0 < strlen(trim($a_params['edited_elements']['muskel_name'])))
            {
                $muskel_name = base64_decode($a_params['edited_elements']['muskel_name']);
            }
            
            if(isset($a_params['edited_elements']['muskel_id']))
            {
                $i_muskel_id = $a_params['edited_elements']['muskel_id'];
            }
            
            if(0 == strlen(trim($muskel_name)) &&
               !$i_muskel_id)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Dieser Muskel benötigt einen Namen'));
                $b_fehler = true;
            }
            else if(0 < strlen(trim($muskel_name)))
            {
                $a_data['muskel_name'] = $muskel_name;
            }
            
            $obj_cad_seo = new CAD_Seo();
            
            if(!$i_muskel_id &&
               strlen(trim($muskel_name)))
            {
                $a_muskel_aktuell = $obj_db_muskeln->getMuskelByName($muskel_name);
                if(is_array($a_muskel_aktuell) &&
                   count($a_muskel_aktuell) > 0)
                {
                    array_push($a_messages, array('type' => 'fehler', 'message' => 'Muskel existiert bereits!', 'result' => false));
                    $b_fehler = true;
                }
            }
            
            if(!$b_fehler)
            {
                // updaten?
                if(is_numeric($i_muskel_id) &&
                   0 < $i_muskel_id &&
                   count($a_data) > 0)
                {
                    $a_muskel_aktuell = $obj_db_muskeln->getMuskel($i_muskel_id);
                    if(
                        (
                            isset($a_data['muskel_name']) &&
                            strlen(trim($a_data['muskel_name'])) > 0 &&
                            $a_muskel_aktuell['muskel_name'] !=
                            $a_data['muskel_name']
                        ) ||
                        (
                            isset($a_muskel_aktuell['muskel_name']) &&
                            strlen(trim($a_muskel_aktuell['muskel_name'])) > 0 &&
                            !strlen(trim($a_muskel_aktuell['muskel_name']))
                        )
                    )
                    {
                            if(isset($a_data['muskel_name']) &&
                               strlen(trim($a_data['muskel_name'])) > 0)
                            {
                                $muskel_name = $a_data['muskel_name'];
                            }
                            else if(isset($a_muskel_aktuell['muskel_name']) &&
                                    strlen(trim($a_muskel_aktuell['muskel_name'])) > 0)
                            {
                                $muskel_name = $a_muskel_aktuell['muskel_name'];
                            }
                            $obj_cad_seo->setLinkName($muskel_name);
                            $obj_cad_seo->setDbTable($obj_db_muskeln);
                            $obj_cad_seo->setTableFieldName("muskel_seo_link");
                            $obj_cad_seo->setTableFieldIdName("muskel_id");
                            $obj_cad_seo->setTableFieldId($i_muskel_id);
                            $obj_cad_seo->createSeoLink();
                            $a_data['muskel_seo_link'] = $obj_cad_seo->getSeoName();
                    }

                    $a_data['muskel_aenderung_datum'] = date("Y-m-d H:i:s");
                    $a_data['muskel_aenderung_user_fk'] = $obj_user->user_id;

                    $obj_db_muskeln->updateMuskel($a_data, $i_muskel_id);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Dieser Muskel wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $i_muskel_id));
                }
                // neu anlegen
                else if(count($a_data) > 0)
                {
                    $obj_cad_seo->setLinkName($a_data['muskel_name']);
                    $obj_cad_seo->setDbTable($obj_db_muskeln);
                    $obj_cad_seo->setTableFieldName("muskel_seo_link");
                    $obj_cad_seo->setTableFieldIdName("muskel_id");
                    $obj_cad_seo->setTableFieldId($i_muskel_id);
                    $obj_cad_seo->createSeoLink();
                    
                    $a_data['muskel_seo_link'] = $obj_cad_seo->getSeoName();
                    $a_data['muskel_eintrag_datum'] = date("Y-m-d H:i:s");
                    $a_data['muskel_eintrag_user_fk'] = $obj_user->user_id;

                    $i_muskel_id = $obj_db_muskeln->setMuskel($a_data);

                    if($i_muskel_id)
                    {
                        array_push($a_messages, array('type' => 'meldung', 'message' => 'Dieser Muskel wurde erfolgreich angelegt!', 'result' => true, 'id' => $i_muskel_id));
                    }
                    else
                    {
                        array_push($a_messages, array('type' => 'fehler', 'message' => 'Beim Speichern des Muskels trat ein unbekannter Fehler auf!', 'result' => false, 'id' => $i_muskel_id));
                    }
                }
                else
                {
                    array_push($a_messages, array('type' => 'warnung', 'message' => 'Dieser Muskel wurde nicht geändert!', 'result' => true, 'id' => $i_muskel_id));
                }
                
                if($i_muskel_id)
                {
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
                }
            }
            else
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Beim Speichern des Muskels trat ein unbekannter Fehler auf!', 'result' => false, 'id' => $i_muskel_id));
            }
        }
        else
        {
            array_push($a_messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Muskel speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }
    
    public function loescheMuskelAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();
        
        if(isset($a_params['id']) &&
           is_numeric($a_params['id']) &&
           $a_params['id'] > 0)
        {
            $i_muskel_id = $a_params['id'];
            $b_fehler = false;
            
            $obj_db_muskeln = new Application_Model_DbTable_Muskeln();
            $obj_db_muskelgruppen = new Application_Model_DbTable_Muskelgruppen();
            $obj_db_muskelgruppen_muskeln = new Application_Model_DbTable_MuskelgruppeMuskeln();
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
            
            if($obj_db_muskeln->loescheMuskel($i_muskel_id))
            {
                array_push($a_messages, array('type' => 'meldung', 'message' => 'Muskel erfolgreich gelöscht!', 'result' => true));
            }
            else
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Muskel konnte leider nicht gelöscht werden!', 'result' => false));
                $b_fehler = true;
            }
            
            // muskelgruppen für muskel holen
            $a_muskelgruppen = $obj_db_muskelgruppen_muskeln->getMuskelgruppenFuerMuskel($i_muskel_id);
            
            if(is_array($a_muskelgruppen) &&
               count($a_muskelgruppen) > 0 &&
               !$b_fehler)
            {
                foreach($a_muskelgruppen as $a_muskelgruppe)
                {
                    // uebungen für muskelgruppe holen
                    $a_uebungen = $obj_db_uebung_muskelgruppen->getUebungenFuerMuskelgruppe($a_muskelgruppe['muskelgruppe_muskel_muskelgruppe_fk']);
                    
                    // betroffene übungen löschen
                    if(is_array($a_uebungen))
                    {
                        foreach($a_uebungen as $a_uebung)
                        {
                            $obj_db_uebungen->loescheUebung($a_uebung['uebung_muskelgruppe_uebung_fk']);
                        }
                    }
                    $obj_db_muskelgruppen->loescheMuskelgruppe($a_muskelgruppe['muskelgruppe_muskel_muskelgruppe_fk']);
                    // uebung_muskelgruppen löschen
                    $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppeVonMuskelgruppe($a_muskelgruppe['muskelgruppe_muskel_muskelgruppe_fk']);
                }
            }
            $obj_db_muskelgruppen_muskeln->loescheAlleMuskelgruppeMuskelnFuerMuskel($i_muskel_id);
        }
        else
        {
            
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }
}

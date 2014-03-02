<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class UebungenController extends Zend_Controller_Action
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
        $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
        $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
        
        $a_uebungen = $obj_db_uebungen->getUebungen();
        
        foreach($a_uebungen as &$a_uebung)
        {
            $a_uebung_muskelgruppen = $obj_db_uebung_muskelgruppen->getMuskelgruppenFuerUebung($a_uebung['uebung_id']);
            $a_uebung['uebung_muskelgruppen'] = $a_uebung_muskelgruppen;
        }
        
        $this->view->assign('a_uebungen', $a_uebungen);
    }
    
    public function showAction()
    {
        
    }
    
    public function editAction()
    {
        $a_params = $this->getRequest()->getParams();
        $vorschaubild_pfad = '/images/content/statisch/grafiken/kein_bild.png';
        
        if(isset($a_params['id']) &&
           is_numeric($a_params['id']) &&
           $a_params['id'] > 0)
        {
            $i_uebung_id = $a_params['id'];
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            /*
            $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
            $uebung_muskelgruppen_content = '';
            $a_uebung_muskelgruppen = $obj_db_uebung_muskelgruppen->getMuskelgruppenFuerUebung($i_uebung_id);
            */
            $a_uebung = $obj_db_uebungen->getUebung($i_uebung_id);
            
            if(is_array($a_uebung))
            {
                $this->view->assign($a_uebung);
                $a_treffer = null;
                
                if(isset($a_uebung['geraet_moegliche_einstellungen']))
                {
                    if(preg_match('/\|/', $a_uebung['geraet_moegliche_einstellungen']))
                    {
                        $this->view->assign('a_geraet_moegliche_einstellungen', explode('|', $a_uebung['geraet_moegliche_einstellungen']));
                    }
                    else if(strlen(trim($a_uebung['geraet_moegliche_einstellungen'])) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_einstellungen', array(trim($a_uebung['geraet_moegliche_einstellungen'])));
                    }
                }
                
                if(isset($a_uebung['geraet_moegliche_sitzpositionen']))
                {
                    if(preg_match('/\|/', $a_uebung['geraet_moegliche_sitzpositionen']))
                    {
                        $this->view->assign('a_geraet_moegliche_sitzpositionen', explode('|', $a_uebung['geraet_moegliche_sitzpositionen']));
                    }
                    else if(strlen(trim($a_uebung['geraet_moegliche_sitzpositionen'])) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_sitzpositionen', array(trim($a_uebung['geraet_moegliche_sitzpositionen'])));
                    }
                }
                
                if(isset($a_uebung['geraet_moegliche_gewichte']))
                {
                    if(preg_match('/\|/', $a_uebung['geraet_moegliche_gewichte']))
                    {
                        $this->view->assign('a_geraet_moegliche_gewichte', explode('|', $a_uebung['geraet_moegliche_gewichte']));
                    }
                    else if(strlen(trim($a_uebung['geraet_moegliche_gewichte'])) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_gewichte', array(trim($a_uebung['geraet_moegliche_gewichte'])));
                    }
                }
            }
        }
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');
	    	
    }
    
    public function uebersichtAction()
    {
        
    }
    
    public function uploadBildAction()
    {
        $req = $this->getRequest();
        $a_params = $req->getParams();

        if(isset($_FILES['cad-cms-image-file']))
        {
            $temp_bild_pfad = getcwd() . '/tmp/uebungen/';

            $obj_file = new CAD_File();
            $obj_file->setDestPath($temp_bild_pfad);
            $obj_file->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'svg', 'gif'));
            $obj_file->setUploadetFiles($_FILES['cad-cms-image-file']);
            $obj_file->moveUploadetFiles();

            $a_files = $obj_file->getDestFiles();

// 	    $this->view->assign('a_files', $a_files);

            if(isset($a_files[0][CAD_FILE::HTML_PFAD]))
            {
                $a_bild_pfad = array();
                $a_bild_pfad['html_pfad'] = $a_files[0][CAD_FILE::HTML_PFAD];
                $a_bild_pfad['sys_pfad'] = $a_files[0][CAD_FILE::SYS_PFAD];
                $a_bild_pfad['file'] = $a_files[0][CAD_FILE::FILE];

                $this->view->assign('bild_pfade', json_encode($a_bild_pfad));
            }
        }
    }

    /**
     * function um eine übersicht aller bilder des jeweiligen editierten
     * projektes zurück zu erhalten und es formatiert auszugeben
     */
    public function holeBilderFuerEditAction()
    {
        $req = $this->getRequest();
        $a_params = $req->getParams();

        $a_bilder = null;
        $obj_files = new CAD_File();

        $obj_files->setSourcePath(getcwd() . '/tmp/uebungen');

        /**
         * wenn es eine ID des projektes gibt, bilder aus dem projektordner
         * holen und temp checken
         */
        if(isset($a_params['id']))
        {
                $obj_files->addSourcePath(getcwd() . "/images/content/dynamisch/uebungen/" . $a_params['id']);
        }
        $obj_files->holeBilderAusPfad();
        $a_bilder = $obj_files->getDestFiles();

        $this->view->assign('a_bilder', $a_bilder);
    }

    public function loescheBildAction()
    {
        $req = $this->getRequest();
        $a_params = $req->getParams();

        if(isset($a_params['bild']))
        {
            $bild_pfad = getcwd() . base64_decode($a_params['bild']);

            if(file_exists($bild_pfad) &&
               is_file($bild_pfad) &&
               is_readable($bild_pfad))
            {
                if(true === @unlink($bild_pfad))
                {
                    echo "Bild erfolgreich gelöscht!<br />";
                }
            }
        }
        else
        {
            echo "Es wurde kein Bild übergeben!<br />";
        }
    }

    public function loescheUebungAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        if(isset($a_params['id']) &&
           is_numeric($a_params['id']) &&
           $a_params['id'] > 0)
        {
            $i_uebung_id = $a_params['id'];
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            if($obj_db_uebungen->loescheUebung($i_uebung_id))
            {
                $i_count_message = count($a_messages);
                $a_messages[$i_count_message]['type'] = "meldung";
                $a_messages[$i_count_message]['message'] = "Übung erfolgreich gelöscht!";
                $a_messages[$i_count_message]['result'] = true;
                
                $bilder_pfad = getcwd() . '/images/content/dynamisch/uebungen/' . $i_uebung_id . '/';
                
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
     * eine liste der muskelgruppen für die übung zurück geben
     * 
     * @access public
     * @
     */
    public function getMuskelgruppenFuerEditAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();
        
        if(isset($a_params['id']))
        {
           $i_uebung_id = $a_params['id'];
           $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
           $a_uebung_muskelgruppen = $obj_db_uebung_muskelgruppen->getMuskelgruppenFuerUebung($i_uebung_id);
           
           $this->view->assign('a_uebung_muskelgruppen', $a_uebung_muskelgruppen);
        }
        
        if(count($a_messages) > 0)
        {
            $this->view->assign('json_string', json_encode($a_messages));
        }
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
    
    public function speichernAction()
    {
        $a_params = $this->getRequest()->getParams();

        $obj_user = Zend_Auth::getInstance()->getIdentity();
        
        if(isset($a_params['edited_elements']))
        {
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            
            $uebung_name = '';
            $a_uebung_muskelgruppen = array();
            $a_uebung_muskelgruppen_loeschen = array();
            $a_uebung_muskelgruppen_updaten = array();
            $a_uebung_muskelgruppen_hinzufuegen = array();
            $i_count_uebung_muskelgruppen = 0;
            $uebung_vorschaubild = '';
            $uebung_beschreibung = '';
            $uebung_besonderheiten = '';
            $uebung_geraet_gewicht = '';
            $uebung_geraet_einstellung = '';
            $uebung_geraet_sitzposition = '';
            $i_uebung_geraet_id = '';
            $i_uebung_id = 0;
            $b_fehler = false;
            $a_messages = array();
            $a_data = array();
            
            if(isset($a_params['edited_elements']['uebung_name']) &&
               0 < strlen(trim($a_params['edited_elements']['uebung_name'])))
            {
                $uebung_name = base64_decode($a_params['edited_elements']['uebung_name']);
            }
            
            if(isset($a_params['edited_elements']['uebung_geraet_gewicht_name']) &&
               0 < strlen(trim($a_params['edited_elements']['uebung_geraet_gewicht_name'])))
            {
                $uebung_geraet_gewicht = base64_decode($a_params['edited_elements']['uebung_geraet_gewicht_name']);
            }
            
            if(isset($a_params['edited_elements']['uebung_geraet_einstellung_name']) &&
               0 < strlen(trim($a_params['edited_elements']['uebung_geraet_einstellung_name'])))
            {
                $uebung_geraet_einstellung = base64_decode($a_params['edited_elements']['uebung_geraet_einstellung_name']);
            }
            
            if(isset($a_params['edited_elements']['uebung_geraet_sitzposition_name']) &&
               0 < strlen(trim($a_params['edited_elements']['uebung_geraet_sitzposition_name'])))
            {
                $uebung_geraet_sitzposition = base64_decode($a_params['edited_elements']['uebung_geraet_sitzposition_name']);
            }

            if(isset($a_params['edited_elements']['uebung_beschreibung']) &&
                0 < strlen(trim($a_params['edited_elements']['uebung_beschreibung'])))
            {
                $uebung_beschreibung = base64_decode($a_params['edited_elements']['uebung_beschreibung']);
            }

            if(isset($a_params['edited_elements']['uebung_geraet_fk']) &&
                0 < strlen(trim($a_params['edited_elements']['uebung_geraet_fk'])))
            {
                $i_uebung_geraet_id = $a_params['edited_elements']['uebung_geraet_fk'];
            }
            
            if(isset($a_params['edited_elements']['uebung_besonderheiten']) &&
               0 < strlen(trim($a_params['edited_elements']['uebung_besonderheiten'])))
            {
                $uebung_besonderheiten = base64_decode($a_params['edited_elements']['uebung_besonderheiten']);
            }
            
            if(isset($a_params['edited_elements']['uebung_vorschaubild']) &&
               0 < strlen(trim($a_params['edited_elements']['uebung_vorschaubild'])))
            {
                $uebung_vorschaubild = base64_decode($a_params['edited_elements']['uebung_vorschaubild']);
            }
            
            if(isset($a_params['edited_elements']['uebung_id']))
            {
                $i_uebung_id = $a_params['edited_elements']['uebung_id'];
            }
            
            if(isset($a_params['edited_elements']['uebung_muskelgruppen']))
            {
                foreach($a_params['edited_elements']['uebung_muskelgruppen'] as $a_muskelgruppe)
                {
                    array_push($a_uebung_muskelgruppen, $a_muskelgruppe);
                }
            }
            if(isset($a_params['edited_elements']['uebung_muskelgruppen']))
            {
                $a_uebung_muskelgruppen_aktuell = array();
                
                $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
                $a_uebung_muskelgruppen_aktuell_roh = $obj_db_uebung_muskelgruppen->getMuskelgruppenFuerUebung($i_uebung_id);
                
                $i_count_uebung_muskelgruppen = count($a_uebung_muskelgruppen_aktuell_roh);
                
                if(is_array($a_uebung_muskelgruppen_aktuell_roh))
                {
                    foreach($a_uebung_muskelgruppen_aktuell_roh as $uebung_muskelgruppe)
                    {
                        // an die stelle der tag id wird der projekt tag id eintrag gesetzt
                        $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['uebung_muskelgruppe_muskelgruppe_fk']] = array(
                            'uebung_muskelgruppe_id'                => $uebung_muskelgruppe['uebung_muskelgruppe_id'],
                            'uebung_muskelgruppe_muskelgruppe_fk'   => $uebung_muskelgruppe['uebung_muskelgruppe_muskelgruppe_fk'],
                            'uebung_muskelgruppe_beanspruchung'     => $uebung_muskelgruppe['uebung_muskelgruppe_beanspruchung']
                        );
                    }
                }
                foreach($a_params['edited_elements']['uebung_muskelgruppen'] as $uebung_muskelgruppe)
                {
                    // es wurde eine id übergeben und diese id bestand bereits
                    if(isset($uebung_muskelgruppe['id']) &&
                       $uebung_muskelgruppe['id'] > 0 &&
                       isset($a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]))
                    {
                        if(isset($uebung_muskelgruppe['beanspruchung']) &&
                           $uebung_muskelgruppe['beanspruchung'] > 0 &&
                           $uebung_muskelgruppe['beanspruchung'] != $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]['uebung_muskelgruppe_beanspruchung'])
                        {
                            array_push($a_uebung_muskelgruppen_updaten, array(
                                'uebung_muskelgruppe_muskelgruppe_fk'    => $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]['uebung_muskelgruppe_id'],
                                'uebung_muskelgruppe_id'                 => $uebung_muskelgruppe['id'],
                                'uebung_muskelgruppe_beanspruchung'      => $uebung_muskelgruppe['beanspruchung'])
                            );
                        }
                        else if(!isset($uebung_muskelgruppe['beanspruchung']) ||
                                !$uebung_muskelgruppe['beanspruchung'])
                        {
//                            array_push($a_muskelgruppe_muskeln_loeschen, $a_muskelgruppe_muskeln_aktuell[$a_muskel['id']]);
                            array_push($a_uebung_muskelgruppen_loeschen, $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]['uebung_muskelgruppe_id']);
                            $i_count_uebung_muskelgruppen--;
                        }
                    }
                    else if(isset($uebung_muskelgruppe['id']) &&
                            $uebung_muskelgruppe['id'] > 0 &&
                            isset($uebung_muskelgruppe['beanspruchung']) &&
                            $uebung_muskelgruppe['beanspruchung'] > 0)
                    {
                        array_push($a_uebung_muskelgruppen_hinzufuegen, array(
                            'uebung_muskelgruppe_id'                 => $uebung_muskelgruppe['id'],
                            'uebung_muskelgruppe_beanspruchung'      => $uebung_muskelgruppe['beanspruchung'])
                        );
                        $i_count_uebung_muskelgruppen++;
                    }
                }
            }
            
            if(0 == strlen(trim($uebung_name)) &&
               !$i_uebung_id)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt einen Namen'));
                $b_fehler = true;
            }
            else if(0 < strlen(trim($uebung_name)))
            {
                $a_data['uebung_name'] = $uebung_name;
            }
            
            if(0 < strlen(trim($uebung_geraet_einstellung)))
            {
                $a_data['uebung_geraet_einstellung'] = $uebung_geraet_einstellung;
            }
            
            if(0 < strlen(trim($uebung_geraet_gewicht)))
            {
                $a_data['uebung_geraet_gewicht'] = $uebung_geraet_gewicht;
            }
            
            if(0 < strlen(trim($uebung_geraet_sitzposition)))
            {
                $a_data['uebung_geraet_sitzposition'] = $uebung_geraet_sitzposition;
            }
            
            if(0 < strlen(trim($uebung_vorschaubild)))
            {
                $a_data['uebung_vorschaubild'] = $uebung_vorschaubild;
            }

            if(0 < strlen(trim($uebung_besonderheiten)))
            {
                $a_data['uebung_besonderheiten'] = $uebung_besonderheiten;
            }

            if(0 >= $i_uebung_geraet_id &&
                !$i_uebung_id)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt ein Gerät'));
                $b_fehler = true;
            }
            else if(0 <= $i_uebung_geraet_id)
            {
                $a_data['uebung_geraet_fk'] = $i_uebung_geraet_id;
            }
            if(0 < strlen(trim($uebung_besonderheiten)))
            {
                $a_data['uebung_besonderheiten'] = $uebung_besonderheiten;
            }

            if(0 == strlen(trim($uebung_beschreibung)) &&
                !$i_uebung_id)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt eine Beschreibung'));
                $b_fehler = true;
            }
            else if(0 < strlen(trim($uebung_beschreibung)))
            {
                $a_data['uebung_beschreibung'] = $uebung_beschreibung;
            }
            
            if($i_count_uebung_muskelgruppen <= 0)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt mindestens eine vollständig ausgefüllte beanspruchte Muskelgruppe'));
                $b_fehler = true;
            }
            
            $obj_cad_seo = new CAD_Seo();
            
            if(!$i_uebung_id &&
               strlen(trim($uebung_name)))
            {
                $a_uebung_aktuell = $obj_db_uebungen->getUebungenByName($uebung_name);
                if(is_array($a_uebung_aktuell) &&
                   count($a_uebung_aktuell) > 0)
                {
                    array_push($a_messages, array('type' => 'fehler', 'message' => 'Übung "' . $uebung_name . '" existiert bereits!', 'result' => false));
                    $b_fehler = true;
                }
            }

            if(!$b_fehler) {
                // updaten?
                if(is_numeric($i_uebung_id) &&
                   0 < $i_uebung_id &&
                   is_array($a_data) &&
                   count($a_data) > 0)
                {
                    $a_uebung_aktuell = $obj_db_uebungen->getUebung($i_uebung_id);
                    if(
                        (
                            isset($a_data['uebung_name']) &&
                            strlen(trim($a_data['uebung_name'])) > 0 &&
                            $a_uebung_aktuell['uebung_name'] !=
                            $a_data['uebung_name']
                        ) ||
                        (
                            isset($a_uebung_aktuell['uebung_name']) &&
                            strlen(trim($a_uebung_aktuell['uebung_name'])) > 0 &&
                            !strlen(trim($a_uebung_aktuell['uebung_seo_link']))
                        )
                    )
                    {
                        if(isset($a_data['uebung_name']) &&
                           strlen(trim($a_data['uebung_name'])) > 0)
                        {
                            $uebung_name = $a_data['uebung_name'];
                        }
                        else if(isset($a_uebung_aktuell['uebung_name']) &&
                                strlen(trim($a_uebung_aktuell['uebung_name'])) > 0)
                        {
                            $uebung_name = $a_uebung_aktuell['uebung_name'];
                        }
                        $obj_cad_seo->setLinkName($uebung_name);
                        $obj_cad_seo->setDbTable($obj_db_uebungen);
                        $obj_cad_seo->setTableFieldName("uebung_seo_link");
                        $obj_cad_seo->setTableFieldIdName("uebung_id");
                        $obj_cad_seo->setTableFieldId($i_uebung_id);
                        $obj_cad_seo->createSeoLink();
                        $a_data['uebung_seo_link'] = $obj_cad_seo->getSeoName();
                    }
                    $a_data['uebung_aenderung_datum'] = date("Y-m-d H:i:s");
                    $a_data['uebung_aenderung_user_fk'] = $obj_user->user_id;

                    $obj_db_uebungen->updateUebung($a_data, $i_uebung_id);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Diese Übung wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $i_uebung_id));
                }
                // neu anlegen
                else if(is_array($a_data) &&
                        count($a_data) > 0)
                {
                    $obj_cad_seo->setLinkName($a_data['uebung_name']);
                    $obj_cad_seo->setDbTable($obj_db_uebungen);
                    $obj_cad_seo->setTableFieldName("uebung_seo_link");
                    $obj_cad_seo->setTableFieldIdName("uebung_id");
                    $obj_cad_seo->setTableFieldId($i_uebung_id);
                    $obj_cad_seo->createSeoLink();
                    
                    $a_data['uebung_seo_link'] = $obj_cad_seo->getSeoName();
                    $a_data['uebung_eintrag_datum'] = date("Y-m-d H:i:s");
                    $a_data['uebung_eintrag_user_fk'] = $obj_user->user_id;

                    $i_uebung_id = $obj_db_uebungen->setUebung($a_data);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Diese Übung wurde erfolgreich angelegt!', 'result' => true, 'id' => $i_uebung_id));
                }
                else if(count($a_uebung_muskelgruppen_hinzufuegen) == 0 &&
                        count($a_uebung_muskelgruppen_updaten) == 0 &&
                        count($a_uebung_muskelgruppen_loeschen) == 0)
                {
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Diese Übung wurde nicht geändert!', 'result' => true, 'id' => $i_uebung_id));
                }
                if(count($a_uebung_muskelgruppen_hinzufuegen) > 0 ||
                        count($a_uebung_muskelgruppen_updaten) > 0 ||
                        count($a_uebung_muskelgruppen_loeschen) > 0)
                {
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Die Muskelgruppen der Übung wurden erfolgreich geändert!', 'result' => true, 'id' => $i_uebung_id));
                }
                
                if($i_uebung_id)
                {
                    /* bilder verschieben */
                    $obj_files = new CAD_File();
                    
                    $str_src_path = getcwd() . '/tmp/uebungen/';
                    $str_dest_path = getcwd() . '/images/content/dynamisch/uebungen/' . $i_uebung_id . '/';

                    if($obj_files->checkAndCreateDir($str_dest_path))
                    {
                        $obj_files->setSourcePath($str_src_path);
                        $obj_files->setDestPath($str_dest_path);
                        $obj_files->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif', 'svg'));
                        $obj_files->verschiebeFiles();
                    }
                    
                    foreach($a_uebung_muskelgruppen_hinzufuegen as $a_uebung_muskelgruppe)
                    {
                        $a_data = array();
                        $a_data['uebung_muskelgruppe_muskelgruppe_fk']   = $a_uebung_muskelgruppe['uebung_muskelgruppe_id'];
                        $a_data['uebung_muskelgruppe_beanspruchung']     = $a_uebung_muskelgruppe['uebung_muskelgruppe_beanspruchung'];
                        $a_data['uebung_muskelgruppe_uebung_fk']         = $i_uebung_id;
                        $a_data['uebung_muskelgruppe_eintrag_datum']     = date("Y-m-d H:i:s");
                        $a_data['uebung_muskelgruppe_eintrag_user_fk']   = $obj_user->user_id;

                        $obj_db_uebung_muskelgruppen->setUebungMuskelgruppen($a_data);
                    }
                    
                    foreach($a_uebung_muskelgruppen_updaten as $a_uebung_muskelgruppe)
                    {
                        $a_data = array();
                        $a_data['uebung_muskelgruppe_beanspruchung']     = $a_uebung_muskelgruppe['uebung_muskelgruppe_beanspruchung'];
                        $a_data['uebung_muskelgruppe_eintrag_datum']     = date("Y-m-d H:i:s");
                        $a_data['uebung_muskelgruppe_eintrag_user_fk']   = $obj_user->user_id;

                        $obj_db_uebung_muskelgruppen->updateUebungMuskelgruppen($a_data, $a_uebung_muskelgruppe['uebung_muskelgruppe_muskelgruppe_fk']);
                    }
                    
                    foreach($a_uebung_muskelgruppen_loeschen as $i_uebung_muskelgruppe_id)
                    {
                        $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppe($i_uebung_muskelgruppe_id);
                    }
                }
            }
            else
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Es gabe einen Fehler beim speichern der Übung!', 'result' => false));
            }
        }
        else
        {
            array_push($a_messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Übung speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }
}

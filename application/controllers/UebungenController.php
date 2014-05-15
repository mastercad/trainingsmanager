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
            $iUebungId = $a_params['id'];
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            /*
            $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
            $uebung_muskelgruppen_content = '';
            $oUebung_muskelgruppen = $obj_db_uebung_muskelgruppen->getMuskelgruppenFuerUebung($i_uebung_id);
            */
            $oUebung = $obj_db_uebungen->getUebung($iUebungId);
            
            if($oUebung instanceof Zend_Db_Table_Row)
            {
                $this->view->assign($oUebung->toArray());
                $aMatches = null;
                
                if(isset($oUebung->geraet_moegliche_einstellungen))
                {
                    if(preg_match('/\|/', $oUebung->geraet_moegliche_einstellungen))
                    {
                        $this->view->assign('a_geraet_moegliche_einstellungen', explode('|', $oUebung->geraet_moegliche_einstellungen));
                    }
                    else if(strlen(trim($oUebung->geraet_moegliche_einstellungen)) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_einstellungen', array(trim($oUebung->geraet_moegliche_einstellungen)));
                    }
                }

                if(isset($oUebung->geraet_moegliche_sitzpositionen))
                {
                    if(preg_match('/\|/', $oUebung->geraet_moegliche_sitzpositionen))
                    {
                        $this->view->assign('a_geraet_moegliche_sitzpositionen', explode('|', $oUebung->geraet_moegliche_sitzpositionen));
                    }
                    else if(strlen(trim($oUebung->geraet_moegliche_sitzpositionen)) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_sitzpositionen', array(trim($oUebung->geraet_moegliche_sitzpositionen)));
                    }
                }

                if(isset($oUebung->geraet_moegliche_rueckenpolster))
                {
                    if(preg_match('/\|/', $oUebung->geraet_moegliche_rueckenpolster))
                    {
                        $this->view->assign('a_geraet_moegliche_rueckenpolster', explode('|', $oUebung->geraet_moegliche_rueckenpolster));
                    }
                    else if(strlen(trim($oUebung->geraet_moegliche_rueckenpolster)) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_rueckenpolster', array(trim($oUebung->geraet_moegliche_rueckenpolster)));
                    }
                }

                if(isset($oUebung->geraet_moegliche_beinpolster))
                {
                    if(preg_match('/\|/', $oUebung->geraet_moegliche_beinpolster))
                    {
                        $this->view->assign('a_geraet_moegliche_beinpolster', explode('|', $oUebung->geraet_moegliche_beinpolster));
                    }
                    else if(strlen(trim($oUebung->geraet_moegliche_beinpolster)) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_beinpolster', array(trim($oUebung->geraet_moegliche_beinpolster)));
                    }
                }
                
                if(isset($oUebung->geraet_moegliche_gewichte))
                {
                    if(preg_match('/\|/', $oUebung->geraet_moegliche_gewichte))
                    {
                        $this->view->assign('a_geraet_moegliche_gewichte', explode('|', $oUebung->geraet_moegliche_gewichte));
                    }
                    else if(strlen(trim($oUebung->geraet_moegliche_gewichte)) > 0)
                    {
                        $this->view->assign('a_geraet_moegliche_gewichte', array(trim($oUebung->geraet_moegliche_gewichte)));
                    }
                }
            } else {
                echo "Probleme beim Laden der Übung!";
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
        $aParams = $this->getAllParams();
        $aMessages = array();
        
        if(isset($aParams['id']))
        {
            $iUebungId = $aParams['id'];
            $oUebungMuskelnStorage = new Application_Model_DbTable_UebungMuskeln();
            $aUebungMuskeln = $oUebungMuskelnStorage->getMuskelnFuerUebung($iUebungId);
            $aMuskelGruppen = array();
            foreach ($aUebungMuskeln as $aUebungMuskel) {
                if (FALSE === array_key_exists($aUebungMuskel['muskelgruppe_name'], $aMuskelGruppen)) {
                    $aMuskelGruppen[$aUebungMuskel['muskelgruppe_name']] = array();
                }
                $aMuskelGruppen[$aUebungMuskel['muskelgruppe_name']]['muskelgruppe_name'] = $aUebungMuskel['muskelgruppe_name'];
                $aMuskelGruppen[$aUebungMuskel['muskelgruppe_name']]['muskelgruppe_id'] = $aUebungMuskel['muskelgruppe_id'];
                $aMuskelGruppen[$aUebungMuskel['muskelgruppe_name']]['muskeln'][] = $aUebungMuskel->toArray();
            }
            $this->view->assign('aMuskelGruppen', $aMuskelGruppen);
        }
        
        if(count($aMessages) > 0)
        {
            $this->view->assign('json_string', json_encode($aMessages));
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

        $iUserId = 1;
        $obj_user = Zend_Auth::getInstance()->getIdentity();

        if (TRUE == is_object($obj_user)) {
            $iUserId = $obj_user->user_id;
        }

        if(isset($a_params['edited_elements']))
        {
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();

            $uebung_name = '';
            $a_uebung_muskelgruppen = array();
            $a_uebung_muskelgruppen_loeschen = array();
            $a_uebung_muskelgruppen_updaten = array();
            $a_uebung_muskelgruppen_hinzufuegen = array();
            $a_uebung_muskeln_loeschen = array();
            $a_uebung_muskeln_updaten = array();
            $a_uebung_muskeln_hinzufuegen = array();
            $i_count_uebung_muskelgruppen = 0;
            $uebung_vorschaubild = '';
            $uebung_beschreibung = '';
            $uebung_besonderheiten = '';
            $uebung_geraet_gewicht = '';
            $uebung_geraet_einstellung = '';
            $uebung_geraet_sitzposition = '';
            $uebung_geraet_rueckenpolster = '';
            $uebung_geraet_beinpolster = '';
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

            if(isset($a_params['edited_elements']['uebung_geraet_rueckenpolster_name']) &&
                0 < strlen(trim($a_params['edited_elements']['uebung_geraet_rueckenpolster_name'])))
            {
                $uebung_geraet_rueckenpolster = base64_decode($a_params['edited_elements']['uebung_geraet_rueckenpolster_name']);
            }

            if(isset($a_params['edited_elements']['uebung_geraet_beinpolster_name']) &&
                0 < strlen(trim($a_params['edited_elements']['uebung_geraet_beinpolster_name'])))
            {
                $uebung_geraet_beinpolster = base64_decode($a_params['edited_elements']['uebung_geraet_beinpolster_name']);
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
            
//            if(isset($a_params['edited_elements']['uebung_muskelgruppen']))
//            {
//                foreach($a_params['edited_elements']['uebung_muskelgruppen'] as $a_muskelgruppe)
//                {
//                    array_push($a_uebung_muskelgruppen, $a_muskelgruppe);
//                }
//            }
//            die();
//            if(isset($a_params['edited_elements']['uebung_muskelgruppen']))
//            {
//                $a_uebung_muskelgruppen_aktuell = array();
//
//                $obj_db_uebung_muskelgruppen = new Application_Model_DbTable_UebungMuskelgruppen();
//                $a_uebung_muskelgruppen_aktuell_roh = $obj_db_uebung_muskelgruppen->getMuskelgruppenFuerUebung($i_uebung_id);
//
//                $i_count_uebung_muskelgruppen = count($a_uebung_muskelgruppen_aktuell_roh);
//
//                if(is_array($a_uebung_muskelgruppen_aktuell_roh))
//                {
//                    foreach($a_uebung_muskelgruppen_aktuell_roh as $uebung_muskelgruppe)
//                    {
//                        $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['uebung_muskelgruppe_muskelgruppe_fk']] =
//                            array(
//                                'uebung_muskelgruppe_id'                => $uebung_muskelgruppe['uebung_muskelgruppe_id'],
//                                'uebung_muskelgruppe_muskelgruppe_fk'   => $uebung_muskelgruppe['uebung_muskelgruppe_muskelgruppe_fk'],
////                                'uebung_muskelgruppe_beanspruchung'     => $uebung_muskelgruppe['uebung_muskelgruppe_beanspruchung']
//                        );
//                    }
//                }
//                foreach($a_params['edited_elements']['uebung_muskelgruppen'] as $uebung_muskelgruppe)
//                {
//                    // es wurde eine id übergeben und diese id bestand bereits
//                    if(isset($uebung_muskelgruppe['id']) &&
//                       $uebung_muskelgruppe['id'] > 0 &&
//                       isset($a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]))
//                    {
//                        if(isset($uebung_muskelgruppe['beanspruchung']) &&
//                           $uebung_muskelgruppe['beanspruchung'] > 0 &&
//                           $uebung_muskelgruppe['beanspruchung'] != $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]['uebung_muskelgruppe_beanspruchung'])
//                        {
//                            array_push($a_uebung_muskelgruppen_updaten, array(
//                                'uebung_muskelgruppe_muskelgruppe_fk'    => $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]['uebung_muskelgruppe_id'],
//                                'uebung_muskelgruppe_id'                 => $uebung_muskelgruppe['id'],
//                                'uebung_muskelgruppe_beanspruchung'      => $uebung_muskelgruppe['beanspruchung'])
//                            );
//                        }
//                        else if(!isset($uebung_muskelgruppe['beanspruchung']) ||
//                                !$uebung_muskelgruppe['beanspruchung'])
//                        {
////                            array_push($a_muskelgruppe_muskeln_loeschen, $a_muskelgruppe_muskeln_aktuell[$a_muskel['id']]);
//                            array_push($a_uebung_muskelgruppen_loeschen, $a_uebung_muskelgruppen_aktuell[$uebung_muskelgruppe['id']]['uebung_muskelgruppe_id']);
//                            $i_count_uebung_muskelgruppen--;
//                        }
//                    }
//                    else if(isset($uebung_muskelgruppe['id']) &&
//                            $uebung_muskelgruppe['id'] > 0 &&
//                            isset($uebung_muskelgruppe['beanspruchung']) &&
//                            $uebung_muskelgruppe['beanspruchung'] > 0)
//                    {
//                        array_push($a_uebung_muskelgruppen_hinzufuegen, array(
//                            'uebung_muskelgruppe_id'                 => $uebung_muskelgruppe['id'],
//                            'uebung_muskelgruppe_beanspruchung'      => $uebung_muskelgruppe['beanspruchung'])
//                        );
//                        $i_count_uebung_muskelgruppen++;
//                    }
//                }
//            }
            
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

            if(0 < strlen(trim($uebung_geraet_rueckenpolster)))
            {
                $a_data['uebung_geraet_rueckenpolster'] = $uebung_geraet_rueckenpolster;
            }

            if(0 < strlen(trim($uebung_geraet_beinpolster)))
            {
                $a_data['uebung_geraet_beinpolster'] = $uebung_geraet_beinpolster;
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
            
//            if($i_count_uebung_muskelgruppen <= 0)
//            {
//                array_push($a_messages, array('type' => 'fehler', 'message' => 'Diese Übung benötigt mindestens eine vollständig ausgefüllte beanspruchte Muskelgruppe'));
//                $b_fehler = true;
//            }
            
            $obj_cad_seo = new CAD_Seo();
            
            if(!$i_uebung_id &&
               strlen(trim($uebung_name)))
            {
                $oUebungenRows = $obj_db_uebungen->getUebungenByName($uebung_name);
                if( FALSE === $oUebungenRows)
                {
                    array_push($a_messages, array('type' => 'fehler', 'message' => 'Übung "' . $uebung_name . '" existiert bereits!', 'result' => false));
                    $b_fehler = true;
                } else {
                    $a_uebung_aktuell = $oUebungenRows->toArray();
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
                    $a_data['uebung_aenderung_user_fk'] = $iUserId;

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
                    $a_data['uebung_eintrag_user_fk'] = $iUserId;

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
                    
//                    foreach($a_uebung_muskelgruppen_hinzufuegen as $a_uebung_muskelgruppe)
//                    {
//                        $a_data = array();
//                        $a_data['uebung_muskelgruppe_muskelgruppe_fk']   = $a_uebung_muskelgruppe['uebung_muskelgruppe_id'];
//                        $a_data['uebung_muskelgruppe_beanspruchung']     = $a_uebung_muskelgruppe['uebung_muskelgruppe_beanspruchung'];
//                        $a_data['uebung_muskelgruppe_uebung_fk']         = $i_uebung_id;
//                        $a_data['uebung_muskelgruppe_eintrag_datum']     = date("Y-m-d H:i:s");
//                        $a_data['uebung_muskelgruppe_eintrag_user_fk']   = $iUserId;
//
//                        $obj_db_uebung_muskelgruppen->setUebungMuskelgruppen($a_data);
//                    }
//
//                    foreach($a_uebung_muskelgruppen_updaten as $a_uebung_muskelgruppe)
//                    {
//                        $a_data = array();
//                        $a_data['uebung_muskelgruppe_beanspruchung']     = $a_uebung_muskelgruppe['uebung_muskelgruppe_beanspruchung'];
//                        $a_data['uebung_muskelgruppe_eintrag_datum']     = date("Y-m-d H:i:s");
//                        $a_data['uebung_muskelgruppe_eintrag_user_fk']   = $iUserId;
//
//                        $obj_db_uebung_muskelgruppen->updateUebungMuskelgruppen($a_data, $a_uebung_muskelgruppe['uebung_muskelgruppe_muskelgruppe_fk']);
//                    }
//
//                    foreach($a_uebung_muskelgruppen_loeschen as $i_uebung_muskelgruppe_id)
//                    {
//                        $obj_db_uebung_muskelgruppen->loescheUebungMuskelgruppe($i_uebung_muskelgruppe_id);
//                    }
//                    ($a_params['edited_elements']['uebung_muskelgruppen']
                    if (array_key_exists('uebung_muskelgruppen', $a_params['edited_elements'])
                        && is_array($a_params['edited_elements']['uebung_muskelgruppen'])
                    ) {
                        $oUebungMuskelGruppen = new Application_Model_DbTable_UebungMuskelgruppen();
                        $oUebungMuskeln = new Application_Model_DbTable_UebungMuskeln();
                        $oMuskelnAktuellRowSet = $oUebungMuskeln->getMuskelnFuerUebung($i_uebung_id);
                        $aMuskelnAktuell = array();
                        foreach ($oMuskelnAktuellRowSet as $oMuskelAktuellRow) {
                            if (FALSE === array_key_exists($oMuskelAktuellRow->uebung_muskel_muskelgruppe_fk, $aMuskelnAktuell)) {
                                $aMuskelnAktuell[$oMuskelAktuellRow->uebung_muskel_muskelgruppe_fk] = array();
                            }
                            $aMuskelnAktuell[$oMuskelAktuellRow->uebung_muskel_muskelgruppe_fk][$oMuskelAktuellRow->uebung_muskel_muskel_fk] = $oMuskelAktuellRow;
                        }

                        foreach ($a_params['edited_elements']['uebung_muskelgruppen'] as $aUebungMuskelGruppe) {
                            if (is_array($aUebungMuskelGruppe)
                                && array_key_exists('muskeln', $aUebungMuskelGruppe)
                                && is_array($aUebungMuskelGruppe['muskeln'])
                            ) {
                                $iMuskelGruppeId = $aUebungMuskelGruppe['id'];
//                                echo "MuskelGruppe : " . $iMuskelGruppeId . PHP_EOL;
                                foreach ($aUebungMuskelGruppe['muskeln'] as $aMuskel) {
                                    // wenn der aktuelle muskel bereits in uebungMuskeln eingetragen
                                    if (array_key_exists($iMuskelGruppeId, $aMuskelnAktuell)
                                        && is_array($aMuskelnAktuell[$iMuskelGruppeId])
                                        && array_key_exists($aMuskel['id'], $aMuskelnAktuell[$iMuskelGruppeId])
                                    ) {
                                        // checken ob der aktuelle muskel keine beanspruchung, dann löschen
                                        if (TRUE === empty($aMuskel['beanspruchung'])) {
//                                            echo "Lösche!" . PHP_EOL;
                                            $bResult =
                                                $oUebungMuskeln->loescheUebungMuskel($aMuskelnAktuell[$iMuskelGruppeId][$aMuskel['id']]->uebung_muskel_id);
                                        // wenn beanspruchung und != eingetragener, dann updaten
                                        } elseif ($aMuskelnAktuell[$iMuskelGruppeId][$aMuskel['id']]->uebung_muskel_beanspruchung != $aMuskel['beanspruchung']) {
//                                            echo "Update!" . PHP_EOL;
                                            $aData = array(
                                                'uebung_muskel_aenderung_datum' => date('Y-m-d H:i:s'),
                                                'uebung_muskel_aenderung_user_fk' => $iUserId,
                                                'uebung_muskel_beanspruchung' => $aMuskel['beanspruchung']
                                            );
                                            $bResult =
                                                $oUebungMuskeln->updateUebungMuskel($aData, $aMuskelnAktuell[$iMuskelGruppeId][$aMuskel['id']]->uebung_muskel_id);
                                        }
                                    }
                                    // wenn es die muskelgruppe schon gibt, aber nicht den muskel
                                    elseif (array_key_exists($iMuskelGruppeId, $aMuskelnAktuell)
                                            && is_array($aMuskelnAktuell[$iMuskelGruppeId])
                                            && FALSE == array_key_exists($aMuskel['id'], $aMuskelnAktuell[$iMuskelGruppeId])
                                            && FALSE == empty($aMuskel['id']['beanspruchung'])
                                    ) {
//                                        echo "Trage ein!" . PHP_EOL;
                                        $aData = array(
                                            'uebung_muskel_muskelgruppe_fk' => $iMuskelGruppeId,
                                            'uebung_muskel_muskel_fk' => $aMuskel['id'],
                                            'uebung_muskel_uebung_fk' => $i_uebung_id,
                                            'uebung_muskel_eintrag_datum' => date('Y-m-d H:i:s'),
                                            'uebung_muskel_eintrag_user_fk' => $iUserId,
                                            'uebung_muskel_beanspruchung' => $aMuskel['beanspruchung']
                                        );
                                        $iUebungMuskelId = $oUebungMuskeln->setUebungMuskel($aData);
                                    }
                                    // wenn es die muskelgruppe noch nicht gibt
                                    elseif (FALSE === array_key_exists($iMuskelGruppeId, $aMuskelnAktuell)) {
//                                        echo "Lege Muskelgruppe und Muskel neu an !" . PHP_EOL;
                                        $aData = array(
                                            'uebung_muskel_muskelgruppe_fk' => $iMuskelGruppeId,
                                            'uebung_muskel_muskel_fk' => $aMuskel['id'],
                                            'uebung_muskel_uebung_fk' => $i_uebung_id,
                                            'uebung_muskel_eintrag_datum' => date('Y-m-d H:i:s'),
                                            'uebung_muskel_eintrag_user_fk' => $iUserId,
                                            'uebung_muskel_beanspruchung' => $aMuskel['beanspruchung']
                                        );
                                        $iUebungMuskelId = $oUebungMuskeln->setUebungMuskel($aData);
                                    }
                                }
                                // wenn es die muskelgruppe noch nicht gibt
                                if (FALSE === array_key_exists($iMuskelGruppeId, $aMuskelnAktuell)) {
//                                    echo "Lege Muskelgruppe und Muskel neu an !" . PHP_EOL;
                                    $aData = array(
                                        'uebung_muskelgruppe_muskelgruppe_fk' => $iMuskelGruppeId,
                                        'uebung_muskelgruppe_uebung_fk' => $i_uebung_id,
                                        'uebung_muskelgruppe_eintrag_datum' => date('Y-m-d H:i:s'),
                                        'uebung_muskelgruppe_eintrag_user_fk' => $iUserId
                                    );
                                    $iUebungMuselGruppeId = $oUebungMuskelGruppen->setUebungMuskelgruppen($aData);
                                }
                            }
                        }
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

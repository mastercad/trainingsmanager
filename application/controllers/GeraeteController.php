<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

class GeraeteController extends Zend_Controller_Action
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
        $obj_db_geraete = new Application_Model_DbTable_Geraete();
        $a_geraete = $obj_db_geraete->getGeraete();

        $this->view->assign('a_geraete', $a_geraete);
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
            $i_geraet_id = $a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraet = $obj_db_geraete->getGeraet($i_geraet_id);

            $this->view->assign($a_geraet);
        }
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
            $temp_bild_pfad = getcwd() . '/tmp/geraete/';

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

        $obj_files->setSourcePath(getcwd() . '/tmp/geraete');

        /**
         * wenn es eine ID des projektes gibt, bilder aus dem projektordner
         * holen und temp checken
         */
        if(isset($a_params['id']))
        {
            $obj_files->addSourcePath(getcwd() . "/images/content/dynamisch/geraete/" . $a_params['id']);
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

    public function getGeraeteFuerEditAction()
    {
        $a_params = $this->getRequest()->getParams();

        if(isset($a_params['id']))
        {
            $i_geraetegruppe_id = $a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraete = $obj_db_geraete->getGeraeteFuerGeraetegruppe($i_geraetegruppe_id);

            $this->view->assign('a_geraete', $a_geraete);
        }
    }

    public function getGeraetVorschlaegeAction()
    {
        $a_params = $this->getRequest()->getParams();

        if(isset($a_params['suche']))
        {
            $str_suche = base64_decode($a_params['suche']) . '%';
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraete = $obj_db_geraete->getGeraeteByName($str_suche);

            $this->view->assign('a_geraete_vorschlaege', $a_geraete);
        }
    }

    public function speichernAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();
        $iUserId = 1;

        $obj_user = Zend_Auth::getInstance()->getIdentity();
        if (TRUE === is_object($obj_user )) {
            $iUserId = $obj_user->user_id;
        }
        if(isset($a_params['edited_elements'])) {
            $obj_db_geraete = new Application_Model_DbTable_Geraete();

            $geraet_name = '';
            $geraet_vorschaubild = '';
            $geraet_moegliche_einstellungen = '';
            $geraet_moegliche_sitzpositionen = '';
            $geraet_moegliche_gewichte = '';
            $i_geraet_id = 0;
            $b_fehler = false;
            $a_data = array();

            if(isset($a_params['edited_elements']['geraet_name']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_name'])))
            {
                $geraet_name = base64_decode($a_params['edited_elements']['geraet_name']);
            }
            
            if(isset($a_params['edited_elements']['geraet_moegliche_einstellungen']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_moegliche_einstellungen'])))
            {
                $geraet_moegliche_einstellungen = base64_decode($a_params['edited_elements']['geraet_moegliche_einstellungen']);
            }

            if(isset($a_params['edited_elements']['geraet_moegliche_sitzpositionen']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_moegliche_sitzpositionen'])))
            {
                $geraet_moegliche_sitzpositionen = base64_decode($a_params['edited_elements']['geraet_moegliche_sitzpositionen']);
            }

            if(isset($a_params['edited_elements']['geraet_moegliche_rueckenpolster']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_moegliche_rueckenpolster'])))
            {
                $geraet_moegliche_rueckenpolster = base64_decode($a_params['edited_elements']['geraet_moegliche_rueckenpolster']);
            }

            if(isset($a_params['edited_elements']['geraet_moegliche_beinpolster']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_moegliche_beinpolster'])))
            {
                $geraet_moegliche_beinpolster = base64_decode($a_params['edited_elements']['geraet_moegliche_beinpolster']);
            }
            
            if(isset($a_params['edited_elements']['geraet_moegliche_gewichte']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_moegliche_gewichte'])))
            {
                $geraet_moegliche_gewichte = base64_decode($a_params['edited_elements']['geraet_moegliche_gewichte']);
            }

            if(isset($a_params['edited_elements']['geraet_vorschaubild']) &&
                0 < strlen(trim($a_params['edited_elements']['geraet_vorschaubild'])))
            {
                $geraet_vorschaubild = base64_decode($a_params['edited_elements']['geraet_vorschaubild']);
            }

            if(isset($a_params['edited_elements']['geraet_id']))
            {
                $i_geraet_id = $a_params['edited_elements']['geraet_id'];
            }

            if(0 == strlen(trim($geraet_name)) &&
               !$i_geraet_id)
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Dieses Geraet benötigt einen Namen'));
                $b_fehler = true;
            }
            else if(0 < strlen(trim($geraet_name)))
            {
                $a_data['geraet_name'] = $geraet_name;
            }

            if(0 < strlen(trim($geraet_moegliche_einstellungen)))
            {
                $a_data['geraet_moegliche_einstellungen'] = $geraet_moegliche_einstellungen;
            }

            if(0 < strlen(trim($geraet_moegliche_sitzpositionen)))
            {
                $a_data['geraet_moegliche_sitzpositionen'] = $geraet_moegliche_sitzpositionen;
            }

            if(0 < strlen(trim($geraet_moegliche_rueckenpolster)))
            {
                $a_data['geraet_moegliche_rueckenpolster'] = $geraet_moegliche_rueckenpolster;
            }

            if(0 < strlen(trim($geraet_moegliche_beinpolster)))
            {
                $a_data['geraet_moegliche_beinpolster'] = $geraet_moegliche_beinpolster;
            }

            if(0 < strlen(trim($geraet_moegliche_gewichte)))
            {
                $a_data['geraet_moegliche_gewichte'] = $geraet_moegliche_gewichte;
            }

            if(0 < strlen(trim($geraet_vorschaubild)))
            {
                $a_data['geraet_vorschaubild'] = $geraet_vorschaubild;
            }

            $obj_cad_seo = new CAD_Seo();

            if(!$i_geraet_id &&
                strlen(trim($geraet_name)))
            {
                $a_geraet_aktuell = $obj_db_geraete->getGeraeteByName($geraet_name);
                if(is_array($a_geraet_aktuell) &&
                    count($a_geraet_aktuell) > 0)
                {
                    array_push($a_messages, array('type' => 'fehler', 'message' => 'Geraet existiert bereits!', 'result' => false));
                    $b_fehler = true;
                }
            }

            if(!$b_fehler)
            {
                // updaten?
                if(is_numeric($i_geraet_id) &&
                    0 < $i_geraet_id &&
                    count($a_data) > 0)
                {
                    $a_geraet_aktuell = $obj_db_geraete->getGeraet($i_geraet_id);
                    if(
                        (
                            isset($a_data['geraet_name']) &&
                            strlen(trim($a_data['geraet_name'])) > 0 &&
                            $a_geraet_aktuell['geraet_name'] !=
                            $a_data['geraet_name']
                        ) ||
                        (
                            isset($a_geraet_aktuell['geraet_name']) &&
                            strlen(trim($a_geraet_aktuell['geraet_name'])) > 0 &&
                            !strlen(trim($a_geraet_aktuell['geraet_name']))
                        )
                    )
                    {
                        if(isset($a_data['geraet_name']) &&
                            strlen(trim($a_data['geraet_name'])) > 0)
                        {
                            $geraet_name = $a_data['geraet_name'];
                        }
                        else if(isset($a_geraet_aktuell['geraet_name']) &&
                            strlen(trim($a_geraet_aktuell['geraet_name'])) > 0)
                        {
                            $geraet_name = $a_geraet_aktuell['geraet_name'];
                        }
                        $obj_cad_seo->setLinkName($geraet_name);
                        $obj_cad_seo->setDbTable($obj_db_geraete);
                        $obj_cad_seo->setTableFieldName("geraet_seo_link");
                        $obj_cad_seo->setTableFieldIdName("geraet_id");
                        $obj_cad_seo->setTableFieldId($i_geraet_id);
                        $obj_cad_seo->createSeoLink();
                        $a_data['geraet_seo_link'] = $obj_cad_seo->getSeoName();
                    }
                    $a_data['geraet_aenderung_datum'] = date("Y-m-d H:i:s");
                    $a_data['geraet_aenderung_user_fk'] = $iUserId;

                    $obj_db_geraete->updateGeraet($a_data, $i_geraet_id);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Dieses Gerät wurde erfolgreich bearbeitet!', 'result' => true, 'id' => $i_geraet_id));
                }
                // neu anlegen
                else if(count($a_data) > 0)
                {
                    $obj_cad_seo->setLinkName($a_data['geraet_name']);
                    $obj_cad_seo->setDbTable($obj_db_geraete);
                    $obj_cad_seo->setTableFieldName("geraet_seo_link");
                    $obj_cad_seo->setTableFieldIdName("geraet_id");
                    $obj_cad_seo->setTableFieldId($i_geraet_id);
                    $obj_cad_seo->createSeoLink();

                    $a_data['geraet_seo_link'] = $obj_cad_seo->getSeoName();
                    $a_data['geraet_eintrag_user_fk'] = $iUserId;
                    $a_data['geraet_eintrag_datum'] = date("Y-m-d H:i:s");

                    $i_geraet_id = $obj_db_geraete->setGeraet($a_data);
                    array_push($a_messages, array('type' => 'meldung', 'message' => 'Dieses Gerät wurde erfolgreich angelegt!', 'result' => true, 'id' => $i_geraet_id));
                }
                else
                {
                    array_push($a_messages, array('type' => 'warnung', 'message' => 'Dieses Gerät wurde nicht geändert!', 'result' => true, 'id' => $i_geraet_id));
                }

                if($i_geraet_id)
                {
                    /* bilder verschieben */
                    $obj_files = new CAD_File();
                    $str_src_path = getcwd() . '/tmp/geraete/';
                    $str_dest_path = getcwd() . '/images/content/dynamisch/geraete/' . $i_geraet_id . '/';

                    if($obj_files->checkAndCreateDir($str_dest_path))
                    {
                        $obj_files->setSourcePath($str_src_path);
                        $obj_files->setDestPath($str_dest_path);
                        $obj_files->setAllowedExtensions(array('jpg', 'png', 'gif', 'svg'));
                        $obj_files->verschiebeFiles();
                    }
                }
            }
            else
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Beim Speichern des Gerätes trat ein unbekannter Fehler auf!', 'result' => false, 'id' => $i_geraet_id));
            }
        }
        else
        {
            array_push($a_messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf von Gerät speichern!', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($a_messages));
    }

    public function loescheGeraetAction()
    {
        $a_params = $this->getRequest()->getParams();
        $a_messages = array();

        if(isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0)
        {
            $i_geraet_id = $a_params['id'];
            $b_fehler = false;

            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $obj_db_geraetegruppen = new Application_Model_DbTable_Geraetegruppen();
            $obj_db_geraetegruppen_geraete = new Application_Model_DbTable_GeraetegruppeGeraete();
            $obj_db_uebungen = new Application_Model_DbTable_Uebungen();
            $obj_db_uebung_geraetegruppen = new Application_Model_DbTable_UebungGeraetegruppen();

            if($obj_db_geraete->loescheGeraet($i_geraet_id))
            {
                array_push($a_messages, array('type' => 'meldung', 'message' => 'Geraet erfolgreich gelöscht!', 'result' => true));
            }
            else
            {
                array_push($a_messages, array('type' => 'fehler', 'message' => 'Geraet konnte leider nicht gelöscht werden!', 'result' => false));
                $b_fehler = true;
            }

            // geraetegruppen für geraet holen
            $a_geraetegruppen = $obj_db_geraetegruppen_geraete->getGeraetegruppenFuerGeraet($i_geraet_id);

            if(is_array($a_geraetegruppen) &&
                count($a_geraetegruppen) > 0 &&
                !$b_fehler)
            {
                foreach($a_geraetegruppen as $a_geraetegruppe)
                {
                    // uebungen für geraetegruppe holen
                    $a_uebungen = $obj_db_uebung_geraetegruppen->getUebungenFuerGeraetegruppe($a_geraetegruppe['geraetegruppe_geraet_geraetegruppe_fk']);

                    // betroffene übungen löschen
                    if(is_array($a_uebungen))
                    {
                        foreach($a_uebungen as $a_uebung)
                        {
                            $obj_db_uebungen->loescheUebung($a_uebung['uebung_geraetegruppe_uebung_fk']);
                        }
                    }
                    $obj_db_geraetegruppen->loescheGeraetegruppe($a_geraetegruppe['geraetegruppe_geraet_geraetegruppe_fk']);
                    // uebung_geraetegruppen löschen
                    $obj_db_uebung_geraetegruppen->loescheUebungGeraetegruppeVonGeraetegruppe($a_geraetegruppe['geraetegruppe_geraet_geraetegruppe_fk']);
                }
            }
            $obj_db_geraetegruppen_geraete->loescheAlleGeraetegruppeGeraeteFuerGeraet($i_geraet_id);
        }
        else
        {

        }
        $this->view->assign('json_string', json_encode($a_messages));
    }
    
    public function optionenMoeglicheEinstellungenAction()
    {
        $a_params = $this->getRequest()->getParams();
        if(isset($a_params['id']) &&
           is_numeric($a_params['id']) &&
           $a_params['id'] > 0)
        {
            $i_geraet_id = (int)$a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraet = $obj_db_geraete->getGeraet($i_geraet_id);
            
            if(isset($a_geraet['geraet_moegliche_einstellungen']))
            {
                if(preg_match('/\|/', $a_geraet['geraet_moegliche_einstellungen']))
                {
                    $this->view->assign('a_geraet_moegliche_einstellungen', explode('|', $a_geraet['geraet_moegliche_einstellungen']));
                }
                else if(strlen(trim($a_geraet['geraet_moegliche_einstellungen'])) > 0)
                {
                    $this->view->assign('a_geraet_moegliche_einstellungen', array(trim($a_geraet['geraet_moegliche_einstellungen'])));
                }
            }
        }
    }

    public function optionenMoeglicheSitzpositionenAction()
    {
        $a_params = $this->getRequest()->getParams();
        if(isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0)
        {
            $i_geraet_id = (int)$a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraet = $obj_db_geraete->getGeraet($i_geraet_id);

            if(isset($a_geraet['geraet_moegliche_sitzpositionen']))
            {
                if(preg_match('/\|/', $a_geraet['geraet_moegliche_sitzpositionen']))
                {
                    $this->view->assign('a_geraet_moegliche_sitzpositionen', explode('|', $a_geraet['geraet_moegliche_sitzpositionen']));
                }
                else if(strlen(trim($a_geraet['geraet_moegliche_sitzpositionen'])) > 0)
                {
                    $this->view->assign('a_geraet_moegliche_sitzpositionen', array(trim($a_geraet['geraet_moegliche_sitzpositionen'])));
                }
            }
        }
    }

    public function optionenMoeglicheBeinpolsterAction()
    {
        $a_params = $this->getRequest()->getParams();
        if(isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0)
        {
            $i_geraet_id = (int)$a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraet = $obj_db_geraete->getGeraet($i_geraet_id);

            if(isset($a_geraet['geraet_moegliche_beinpolster']))
            {
                if(preg_match('/\|/', $a_geraet['geraet_moegliche_beinpolster']))
                {
                    $this->view->assign('a_geraet_moegliche_beinpolster', explode('|', $a_geraet['geraet_moegliche_beinpolster']));
                }
                else if(strlen(trim($a_geraet['geraet_moegliche_beinpolster'])) > 0)
                {
                    $this->view->assign('a_geraet_moegliche_beinpolster', array(trim($a_geraet['geraet_moegliche_beinpolster'])));
                }
            }
        }
    }

    public function optionenMoeglicheRueckenpolsterAction()
    {
        $a_params = $this->getRequest()->getParams();
        if(isset($a_params['id']) &&
            is_numeric($a_params['id']) &&
            $a_params['id'] > 0)
        {
            $i_geraet_id = (int)$a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraet = $obj_db_geraete->getGeraet($i_geraet_id);

            if(isset($a_geraet['geraet_moegliche_rueckenpolster']))
            {
                if(preg_match('/\|/', $a_geraet['geraet_moegliche_rueckenpolster']))
                {
                    $this->view->assign('a_geraet_moegliche_rueckenpolster', explode('|', $a_geraet['geraet_moegliche_rueckenpolster']));
                }
                else if(strlen(trim($a_geraet['geraet_moegliche_rueckenpolster'])) > 0)
                {
                    $this->view->assign('a_geraet_moegliche_rueckenpolster', array(trim($a_geraet['geraet_moegliche_rueckenpolster'])));
                }
            }
        }
    }
    
    public function optionenMoeglicheGewichteAction()
    {
        $a_params = $this->getRequest()->getParams();
        if(isset($a_params['id']) &&
           is_numeric($a_params['id']) &&
           $a_params['id'] > 0)
        {
            $i_geraet_id = (int)$a_params['id'];
            $obj_db_geraete = new Application_Model_DbTable_Geraete();
            $a_geraet = $obj_db_geraete->getGeraet($i_geraet_id);

            if(isset($a_geraet['geraet_moegliche_gewichte']))
            {
                if(preg_match('/\|/', $a_geraet['geraet_moegliche_gewichte']))
                {
                    $this->view->assign('a_geraet_moegliche_gewichte', explode('|', $a_geraet['geraet_moegliche_gewichte']));
                }
                else if(strlen(trim($a_geraet['geraet_moegliche_gewichte'])) > 0)
                {
                    $this->view->assign('a_geraet_moegliche_gewichte', array(trim($a_geraet['geraet_moegliche_gewichte'])));
                }
            }
        }
    }
}

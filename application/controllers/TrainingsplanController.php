<?php
	class TrainingsplanController extends Zend_Controller_Action
	{
		public function __init()
		{
		}

	    public function postDispatch()
	    {
	    	$a_params = $this->getRequest()->getParams();

	    	if(isset($a_params['ajax']))
	    	{
	    		$this->view->layout()->disableLayout();
	    	}
	    }

        public function indexAction()
        {

        }

        public function showAction()
        {
            $aParams = $this->getAllParams();

            // wenn ein vorhandener trainingsplan abgerufen werden soll
            if (array_key_exists('id', $aParams)
                && is_numeric($aParams['id'])
                && 0 < $aParams['id']
            ) {
                $iTrainingsplanId = $aParams['id'];
                $oTrainingsplaeneStorage = new Application_Model_DbTable_Trainingsplaene();
                $oTrainingsplanRow = $oTrainingsplaeneStorage->getTrainingsplan($iTrainingsplanId);
            } else {

            }
        }

        public function editAction()
        {
            $aParams = $this->getAllParams();

            // wenn ein vorhandener trainingsplan abgerufen werden soll
            if (array_key_exists('id', $aParams)
                && is_numeric($aParams['id'])
                && 0 < $aParams['id']
            ) {
                $sContent = '';
                $iTrainingsplanId = $aParams['id'];
                $oTrainingsplaeneStorage = new Application_Model_DbTable_Trainingsplaene();
                $oTrainingsplanUebungen = new Application_Model_DbTable_TrainingsplanUebungen();
                $oTrainingsplanRow = $oTrainingsplaeneStorage->getTrainingsplan($iTrainingsplanId);
//                $oTrainingsplaeneChildRows = $oTrainingsplaeneStorage->getChildTrainingsplaene(
//                    $iTrainingsplanId);
                $oUebungen = $oTrainingsplanUebungen->getUebungenFuerParentTrainingsplan($iTrainingsplanId);

                foreach ($oUebungen as $oUebungRow) {
                    $this->view->assign($oUebungRow->toArray());
                    $this->generateMoeglicheGewichte($oUebungRow);
                    $this->generateMoeglicheSitzpositionen($oUebungRow);
                    $this->generateMoeglicheRueckenpolster($oUebungRow);
                    $this->generateMoeglicheBeinpolster($oUebungRow);
//                    $sContent .= $this->renderScript('trainingsplan/partials/trainingsplan_partial.phtml');
                    $sContent .= $this->renderScript('trainingsplan/get-uebung.phtml');
                }
                $this->view->assign('sContent', $sContent);
            } else {

            }
        }

        public function speichernAction()
        {

        }

        /**
         * zum auswählen des grundlayouts des trainingsplanes
         *  - normal
         *  - split
         */
        public function selectLayoutAction()
        {
            $oUsersStorage = new Application_Model_DbTable_Users();
            $oUsers = $oUsersStorage->getActiveUsers();
            $aParams = $this->getAllParams();

            if (FALSE !== $oUsers) {
                $this->view->assign('aUsers', $oUsers->toArray());
            }

            if ($this->getRequest()->isPost()) {
                $iTrainingsplanId = $this->createLayoutAction();
                if (is_numeric($iTrainingsplanId)
                    && 0 < $iTrainingsplanId
                ) {
                    $this->redirect('/trainingsplan/edit/id/' . $iTrainingsplanId);
                } else {
                    echo "Konnte aktuellen Trainingsplan nicht anlegen!";
                }
            }
        }

        public function createLayoutAction()
        {
            $aParams = $this->getAllParams();
            $iTrainingsplanId = NULL;

            if (TRUE === array_key_exists('layout', $aParams)
                && TRUE === array_key_exists('user_id', $aParams)
            ) {
                $iUserId = $aParams['user_id'];
                switch ($aParams['layout'])
                {
                    case 1:
                        $iTrainingsplanId = $this->createBaseTrainingsplan($iUserId);
                        break;
                    case 2:
                        $iTrainingsplanId = $this->createSplitTrainingsplan($iUserId);
                        break;
                    default:
                }
            } else {
                echo "Funktion falsch aufgerufen!<br />";
            }
            return $iTrainingsplanId;
        }

        protected function createBaseTrainingsplan($iUserId)
        {
            $oTrainingsplanLayouts = new Application_Model_DbTable_TrainingsplanLayouts();
            $oTrainingsplanLayout = $oTrainingsplanLayouts->getTrainingsplanLayoutByName('Normal');
            $iTrainingsplanLayoutId = $oTrainingsplanLayout->trainingsplan_layout_id;

            if (is_numeric($iTrainingsplanLayoutId)
                && 0 < $iTrainingsplanLayoutId
            ) {
                $aData = array(
                    'trainingsplan_layout_fk' => $iTrainingsplanLayoutId,
                    'trainingsplan_user_fk' => $iUserId,
                );
                $iTrainingsplanId = $this->createTrainingsplan($aData);
                return $iTrainingsplanId;
            } else {
                echo "Es konnte kein Layout für Normale Trainingspläne gefunden werden!";
                return FALSE;
            }
        }

        protected function createSplitTrainingsplan($iUserId)
        {
            $oTrainingsplanLayouts = new Application_Model_DbTable_TrainingsplanLayouts();
            $oTrainingsplanLayout = $oTrainingsplanLayouts->getTrainingsplanLayoutByName('Split');
            $iTrainingsplanLayoutId = $oTrainingsplanLayout->trainingsplan_layout_id;

            if (is_numeric($iTrainingsplanLayoutId)
                && 0 < $iTrainingsplanLayoutId
            ) {
                $aData = array(
                    'trainingsplan_layout_fk' => $iTrainingsplanLayoutId,
                    'trainingsplan_user_fk' => $iUserId,
                );
                $iTrainingsplanParentId = $this->createTrainingsplan($aData);

                if (is_numeric($iTrainingsplanParentId)
                    && 0 < $iTrainingsplanParentId
                ) {
                    $oTrainingsplanLayout = $oTrainingsplanLayouts->getTrainingsplanLayoutByName('Normal');
                    $iTrainingsplanLayoutId = $oTrainingsplanLayout->trainingsplan_layout_id;
                    $aData = array(
                        'trainingsplan_layout_fk' => $iTrainingsplanLayoutId,
                        'trainingsplan_parent_fk' => $iTrainingsplanParentId,
                        'trainingsplan_user_fk' => $iUserId
                    );
                    $iTrainingsplanId = $this->createTrainingsplan($aData);
                }
                return $iTrainingsplanParentId;
            } else {
                echo "Es konnte kein Layout für Split Trainingspläne gefunden werden!";
                return FALSE;
            }
        }

        public function createTrainingsplan($aData)
        {
            $oUser = Zend_Auth::getInstance()->getIdentity();
            $oTrainingsplaene = new Application_Model_DbTable_Trainingsplaene();

            $iUserId = $oUser->user_id;

            if (is_numeric($iUserId)
                && 0 < $iUserId
            ) {
                $aData['trainingsplan_eintrag_datum'] = date('Y-m-d H:i:s');
                $aData['trainingsplan_eintrag_user_fk'] = $iUserId;

                $iTrainingsplanId = $oTrainingsplaene->insert($aData);

                return $iTrainingsplanId;
            } else {
                throw new Exception('Sie müssen angemeldet sein, um diese Aktion durchzuführen!');
            }
        }

        public function getUebungAction()
        {
            $aParams = $this->getAllParams();

            if (array_key_exists('id', $aParams)) {
                $iUebungId = $aParams['id'];
                $oUebungen = new Application_Model_DbTable_Uebungen();
                $oUebungRow = $oUebungen->getUebung($iUebungId);
                $this->view->assign($oUebungRow->toArray());
                $this->generateMoeglicheGewichte($oUebungRow);
                $this->generateMoeglicheSitzpositionen($oUebungRow);
                $this->generateMoeglicheRueckenpolster($oUebungRow);
                $this->generateMoeglicheBeinpolster($oUebungRow);
            }
        }

        public function generateMoeglicheGewichte($oUebungRow)
        {
            if ($oUebungRow instanceof Zend_Db_Table_Row) {
                $aMoeglicheGewichte = NULL;
                if (isset($oUebungRow->uebung_geraet_gewicht)
                    && 0 < strlen(trim($oUebungRow->uebung_geraet_gewicht))
                ) {
                    $aMoeglicheGewichte = explode('|', $oUebungRow->{'uebung_geraet_gewicht'});
                } else {
                    $aMoeglicheGewichte = explode('|', $oUebungRow->{'geraet_moegliche_gewichte'});
                }
                if (1 == count($aMoeglicheGewichte)) {
                    $aMoeglicheGewichte = $aMoeglicheGewichte[0];
                }
                $this->view->assign('aMoeglicheGewichte', $aMoeglicheGewichte);
            }
            return $this;
        }

        public function generateMoeglicheSitzpositionen($oUebungRow)
        {
            if ($oUebungRow instanceof Zend_Db_Table_Row) {
                $aMoeglicheSitzpositionen = NULL;
                if (isset($oUebungRow->uebung_geraet_sitzposition)
                    && 0 < strlen(trim($oUebungRow->uebung_geraet_sitzposition))
                ) {
                    $aMoeglicheSitzpositionen = explode('|', $oUebungRow->{'uebung_geraet_sitzposition'});
                } else {
                    $aMoeglicheSitzpositionen = explode('|', $oUebungRow->{'geraet_moegliche_sitzpositionen'});
                }
                if (1 == count($aMoeglicheSitzpositionen)) {
                    $aMoeglicheSitzpositionen = $aMoeglicheSitzpositionen[0];
                }
                $this->view->assign('aMoeglicheSitzpositionen', $aMoeglicheSitzpositionen);
            }
            return $this;
        }

        public function generateMoeglicheRueckenpolster($oUebungRow)
        {
            if ($oUebungRow instanceof Zend_Db_Table_Row) {
                $aMoeglicheRueckenpolster = NULL;
                if (isset($oUebungRow->uebung_geraet_rueckenpolster)
                    && 0 < strlen(trim($oUebungRow->uebung_geraet_rueckenpolster))
                ) {
                    $aMoeglicheRueckenpolster = explode('|', $oUebungRow->{'uebung_geraet_rueckenpolster'});
                } else {
                    $aMoeglicheRueckenpolster = explode('|', $oUebungRow->{'geraet_moegliche_rueckenpolster'});
                }
                if (1 == count($aMoeglicheRueckenpolster)) {
                    $aMoeglicheRueckenpolster = $aMoeglicheRueckenpolster[0];
                }
                $this->view->assign('aMoeglicheRueckenpolster', $aMoeglicheRueckenpolster);
            }
            return $this;
        }

        public function generateMoeglicheBeinpolster($oUebungRow)
        {
            if ($oUebungRow instanceof Zend_Db_Table_Row) {
                $aMoeglicheBeinpolster = NULL;
                if (isset($oUebungRow->uebung_geraet_beinpolster)
                    && 0 < strlen(trim($oUebungRow->uebung_geraet_beinpolster))
                ) {
                    $aMoeglicheBeinpolster = explode('|', $oUebungRow->{'uebung_geraet_beinpolster'});
                } else {
                    $aMoeglicheBeinpolster = explode('|', $oUebungRow->{'geraet_moegliche_beinpolster'});
                }
                if (1 == count($aMoeglicheBeinpolster)) {
                    $aMoeglicheBeinpolster = $aMoeglicheBeinpolster[0];
                }
                $this->view->assign('aMoeglicheBeinpolster', $aMoeglicheBeinpolster);
            }
            return $this;
        }


        public function getUebungenVorschlaegeAction()
        {
            $aParams = $this->getAllParams();

            if (array_key_exists('suche', $aParams)) {
                $sSearch = base64_decode($aParams['suche']);
                $oUebungenStorage = new Application_Model_DbTable_Uebungen();
                $oUebungenRows = $oUebungenStorage->getUebungenByName('%' . $sSearch . '%');
                $sContent = '';
                if ($oUebungenRows instanceof Zend_Db_Table_Rowset) {
                    foreach ($oUebungenRows as $oUebungRow) {
                        $this->view->assign($oUebungRow->toArray());
                        $sContent .= $this->renderScript('trainingsplan/partials/uebung-vorschlag.phtml');
                    }
                }
                echo $sContent;
            }
        }
    }

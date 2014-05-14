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

        /**
         * wenn admin und höher, liste aller aktiven trainingspläne
         * wenn nur user, seinen aktiven trainingsplan
         */
        public function indexAction()
        {
            $oTrainingsplaeneStorage = new Application_Model_DbTable_Trainingsplaene();
            $oTrainingsplaeneRowSet = $oTrainingsplaeneStorage->getTrainingsplaeneAllActive();

            if (FALSE !== $oTrainingsplaeneRowSet) {
                $sContent = '';
                foreach ($oTrainingsplaeneRowSet as $oTrainingsplanRow) {
                    $sTrainingsplanName = $oTrainingsplanRow->user_vorname . ' - ' . date('d.m.Y', strtotime($oTrainingsplanRow->trainingsplan_eintrag_datum));
                    if (FALSE === empty($oTrainingsplanRow->trainingsplan_name)) {
                        $sTrainingsplanName = $oTrainingsplanRow->trainingsplan_name;
                    }

                    $sContent .= '<a href="/trainingsplan/show/id/' . $oTrainingsplanRow->trainingsplan_id . '">' .
                        $sTrainingsplanName . '</a><br />';
                }
                $this->view->assign('sContent', $sContent);
            }

        }

        /**
         * wenn admin und höher, liste aller aktiven trainingspläne
         * wenn nur user, seinen aktiven trainingsplan
         */
        public function archivAction()
        {
            $oTrainingsplaeneStorage = new Application_Model_DbTable_Trainingsplaene();
            $oTrainingsplaeneRowSet = $oTrainingsplaeneStorage->getTrainingsplaeneAllInActive();

            if (FALSE !== $oTrainingsplaeneRowSet) {
                $sContent = '';
                foreach ($oTrainingsplaeneRowSet as $oTrainingsplanRow) {
                    $sTrainingsplanName = $oTrainingsplanRow->user_vorname . ' - ' . date('d.m.Y', strtotime($oTrainingsplanRow->trainingsplan_eintrag_datum));
                    if (FALSE === empty($oTrainingsplanRow->trainingsplan_name)) {
                        $sTrainingsplanName = $oTrainingsplanRow->trainingsplan_name;
                    }

                    $sContent .= '<a href="/trainingsplan/show/id/' . $oTrainingsplanRow->trainingsplan_id . '">' .
                        $sTrainingsplanName . '</a><br />';
                }
                $this->view->assign('sContent', $sContent);
            }

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
                $oTrainingstagebuecherStorage = new Application_Model_DbTable_Trainingstagebuecher();
                $oTrainingsplanUebungenStorage = new Application_Model_DbTable_TrainingsplanUebungen();
                $oTrainingsplanRow = $oTrainingsplaeneStorage->getTrainingsplan($iTrainingsplanId);
                $sTrainingsplanLayout = '';
                $sUebungen = '';
                if (2 == $oTrainingsplanRow->trainingsplan_layout_fk) {
                    $oTrainingsplanChildren =
                        $oTrainingsplaeneStorage->getChildTrainingsplaene($iTrainingsplanId);

                    foreach ($oTrainingsplanChildren as $oTrainingsplanChild) {
                        $sUebungen = '';
                        $oTrainingsplanUebungen = $oTrainingsplanUebungenStorage->getUebungenFuerTrainingsplan($oTrainingsplanChild->trainingsplan_id);
                        foreach ($oTrainingsplanUebungen as $oTrainingsplanUebung) {
                            $oTrainingstagebuchUebung = $oTrainingstagebuecherStorage->getActualTrainingstagebuchFuerUebung($oTrainingsplanUebung->trainingsplan_uebung_id);
//                            $this->view->assign($oTrainingsplanUebung->toArray());
                            $this->considerTrainingstagebuchEntryFuerUebung($oTrainingsplanUebung, $oTrainingstagebuchUebung);
                            $this->view->assign($oTrainingsplanRow->toArray());
                            $sUebungen .= $this->view->render('trainingsplan/partials/trainingsplan-uebung-partial.phtml');
                        }
                        $this->view->assign('iTrainingsplanId', $oTrainingsplanChild->trainingsplan_id);
                        $this->view->assign('sUebungen', $sUebungen);
                        $sTrainingsplanLayout .= $this->view->render('trainingsplan/partials/trainingsplan_partial.phtml');
                    }
                } else {
                    $oTrainingsplanUebungen =
                        $oTrainingsplanUebungenStorage->getUebungenFuerParentTrainingsplan($iTrainingsplanId);

                    foreach ($oTrainingsplanUebungen as $oTrainingsplanUebung) {
                        $this->view->assign($oTrainingsplanUebung->toArray());
                        $sUebungen .= $this->view->render('trainingsplan/partials/trainingsplan-uebung-partial.phtml');
                    }
                    $this->view->assign('sUebungen', $sUebungen);
                    $this->view->assign($oTrainingsplanRow->toArray());
                    $this->view->assign('iTrainingsplanId', $oTrainingsplanRow->trainingsplan_id);
                    $sTrainingsplanLayout = $this->view->render('trainingsplan/partials/trainingsplan_partial.phtml');
                }
                $this->view->assign($oTrainingsplanRow->toArray());
                $this->view->assign('sTrainingsplanLayout', $sTrainingsplanLayout);
            } else {

            }
        }

        public function considerTrainingstagebuchEntryFuerUebung($oTrainingsplanUebung, $oTrainingstagebuchUebung)
        {
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_wiederholungen)) {
                $this->view->assign('actual_uebung_wiederholungen', $oTrainingstagebuchUebung->trainingstagebuch_wiederholungen);
            } else {
                $this->view->assign('actual_uebung_wiederholungen', $oTrainingsplanUebung->trainingsplan_uebung_wiederholungen);
            }
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_saetze)) {
                $this->view->assign('actual_uebung_saetze', $oTrainingstagebuchUebung->trainingstagebuch_saetze);
            } else {
                $this->view->assign('actual_uebung_saetze', $oTrainingsplanUebung->trainingsplan_uebung_saetze);
            }
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_gewicht)) {
                $this->view->assign('actual_uebung_gewicht', $oTrainingstagebuchUebung->trainingstagebuch_gewicht);
            } else {
                $this->view->assign('actual_uebung_gewicht', $oTrainingsplanUebung->trainingsplan_uebung_gewicht);
            }
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_sitzposition)) {
                $this->view->assign('actual_uebung_sitzposition', $oTrainingstagebuchUebung->trainingstagebuch_sitzposition);
            } else {
                $this->view->assign('actual_uebung_sitzposition', $oTrainingsplanUebung->trainingsplan_uebung_sitzposition);
            }
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_rueckenpolster)) {
                $this->view->assign('actual_uebung_rueckenpolster', $oTrainingstagebuchUebung->trainingstagebuch_rueckenpolster);
            } else {
                $this->view->assign('actual_uebung_rueckenpolster', $oTrainingsplanUebung->trainingsplan_uebung_rueckenpolster);
            }
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_beinpolster)) {
                $this->view->assign('actual_uebung_beinpolster', $oTrainingstagebuchUebung->trainingstagebuch_beinpolster);
            } else {
                $this->view->assign('actual_uebung_beinpolster', $oTrainingsplanUebung->trainingsplan_uebung_beinpolster);
            }
            if (FALSE === empty($oTrainingstagebuchUebung->trainingstagebuch_bemerkung)) {
                $this->view->assign('actual_uebung_bemerkung', $oTrainingstagebuchUebung->trainingstagebuch_bemerkung);
            } else {
                $this->view->assign('actual_uebung_bemerkung', $oTrainingsplanUebung->trainingsplan_uebung_bemerkung);
            }
            $this->view->assign($oTrainingsplanUebung->toArray());
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
                $oTrainingsplanUebungenStorage = new Application_Model_DbTable_TrainingsplanUebungen();
                $oTrainingsplanRow = $oTrainingsplaeneStorage->getTrainingsplan($iTrainingsplanId);

                $sUebungen = '';

                if (2 == $oTrainingsplanRow->trainingsplan_layout_fk) {
                    $oTrainingsplanChildren =
                        $oTrainingsplaeneStorage->getChildTrainingsplaene($iTrainingsplanId);

                    foreach ($oTrainingsplanChildren as $oTrainingsplanChild) {

                        $sUebungen = '';
                        $oTrainingsplanUebungen = $oTrainingsplanUebungenStorage->getUebungenFuerTrainingsplan($oTrainingsplanChild->trainingsplan_id);
                        foreach ($oTrainingsplanUebungen as $oTrainingsplanUebung) {
                            $this->view->assign($oTrainingsplanUebung->toArray());
                            $this->generateMoeglicheGewichte($oTrainingsplanUebung);
                            $this->generateMoeglicheSitzpositionen($oTrainingsplanUebung);
                            $this->generateMoeglicheRueckenpolster($oTrainingsplanUebung);
                            $this->generateMoeglicheBeinpolster($oTrainingsplanUebung);
                            $sUebungen .= $this->view->render('trainingsplan/get-uebung.phtml');
                        }
                        $this->view->assign('sUebungen', $sUebungen);
                        $this->view->assign('iTrainingsplanId', $oTrainingsplanChild->trainingsplan_id);
                        $sContent .= $this->view->render('trainingsplan/partials/trainingsplan_partial.phtml');
                    }
                } else {
                    $oTrainingsplanUebungen =
                        $oTrainingsplanUebungenStorage->getUebungenFuerParentTrainingsplan($iTrainingsplanId);

                    foreach ($oTrainingsplanUebungen as $oTrainingsplanUebung) {
                        $this->view->assign($oTrainingsplanUebung->toArray());
                        $this->generateMoeglicheGewichte($oTrainingsplanUebung);
                        $this->generateMoeglicheSitzpositionen($oTrainingsplanUebung);
                        $this->generateMoeglicheRueckenpolster($oTrainingsplanUebung);
                        $this->generateMoeglicheBeinpolster($oTrainingsplanUebung);
                        $sUebungen .= $this->view->render('trainingsplan/get-uebung.phtml');
                    }
                    $this->view->assign('sUebungen', $sUebungen);
                    $this->view->assign('iTrainingsplanId', $iTrainingsplanId);
                    $sContent = $this->view->render('trainingsplan/partials/trainingsplan_partial.phtml');
                }
                $this->view->assign('trainingsplan_user_fk', $oTrainingsplanRow->trainingsplan_user_fk);
                $this->view->assign('trainingsplan_id', $iTrainingsplanId);
                $this->view->assign('trainingsplan_active', $oTrainingsplanRow->trainingsplan_active);
                $this->view->assign('trainingsplan_layout_fk', $oTrainingsplanRow->trainingsplan_layout_fk);
                $this->view->assign('sContent', $sContent);
            } else {

            }
        }

        public function speichernAction()
        {
            $aParams = $this->getAllParams();

            if (TRUE === array_key_exists('trainingsplan_uebung_id', $aParams)) {
                $oTrainingsplanUebungen = new Application_Model_DbTable_TrainingsplanUebungen();
                $oUser = Zend_Auth::getInstance()->getIdentity();
                if (Zend_Auth::getInstance()->hasIdentity()) {
                    $iUserId = $oUser->user_id;
                    foreach ($aParams['trainingsplan_uebung_id'] as $iTrainingsplanId => $aTrainingsplanUebungen) {
                        $iOrder = 1;
                        foreach ($aTrainingsplanUebungen as $iTrainingsplanUebungId => $mValue) {
                            $aData = array();
                            $aData['trainingsplan_uebung_wiederholungen']   = $aParams['trainingsplan_uebung_wiederholungen'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_saetze']           = $aParams['trainingsplan_uebung_saetze'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_gewicht']          = $aParams['trainingsplan_uebung_gewicht'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_sitzposition']     = $aParams['trainingsplan_uebung_sitzposition'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_rueckenpolster']   = $aParams['trainingsplan_uebung_rueckenpolster'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_beinpolster']      = $aParams['trainingsplan_uebung_beinpolster'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_bemerkung']        = $aParams['trainingsplan_uebung_bemerkung'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_fk']               = $aParams['uebung_id'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_trainingsplan_fk'] = $iTrainingsplanId;
//                            $aData['trainingsplan_uebung_order']            = $aParams['trainingsplan_uebung_order'][$iTrainingsplanId][$iTrainingsplanUebungId];
                            $aData['trainingsplan_uebung_order']            = $iOrder;

                            if (TRUE == empty($mValue)) {
                                $aData['trainingsplan_uebung_eintrag_user_fk'] = $iUserId;
                                $aData['trainingsplan_uebung_eintrag_datum'] = date('Y-m-d H:i:s');
                                $oTrainingsplanUebungen->insert($aData);
                            } else {
                                $aData['trainingsplan_uebung_aenderung_user_fk'] = $iUserId;
                                $aData['trainingsplan_uebung_aenderung_datum'] = date('Y-m-d H:i:s');
                                $oTrainingsplanUebungen->update($aData, 'trainingsplan_uebung_id = ' . $mValue);
                            }
                            $iOrder++;
                        }
                    }
                } else {
                    echo "Für diese Aktion fehlen Ihnen die benötigten Rechte!";
                }
            }
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

        public function getTrainingsplanFuerSplitAction()
        {
            $aParams = $this->getAllParams();

            $oUser = Zend_Auth::getInstance()->getIdentity();
            $oTrainingsplaene = new Application_Model_DbTable_Trainingsplaene();

            $iUserId = $oUser->user_id;

            if (is_numeric($iUserId)
                && 0 < $iUserId
                && TRUE === array_key_exists('user_id', $aParams)
                && TRUE === is_numeric($aParams['user_id'])
                && 0 < $aParams['user_id']
                && TRUE === array_key_exists('trainingsplan_id', $aParams)
                && TRUE === is_numeric($aParams['trainingsplan_id'])
                && 0 < $aParams['trainingsplan_id']
            ) {
                $iTrainingsplanUserFk = $aParams['user_id'];
                $iTrainingsplanParentFk = $aParams['trainingsplan_id'];
                $aData = array(
                    'trainingsplan_layout_fk' => 1,
                    'trainingsplan_parent_fk' => $iTrainingsplanParentFk,
                    'trainingsplan_user_fk' => $iTrainingsplanUserFk
                );
                $iTrainingsplanId = $this->createTrainingsplan($aData);
                $this->view->assign('iTrainingsplanId', $iTrainingsplanId);
                $sContent = $this->view->render('trainingsplan/partials/trainingsplan_partial.phtml');
                $this->view->assign('sContent', $sContent);
            } else {
                $sContent = 'Konnte Trainingsplan nicht anlegen!';
                $this->view->assign('sContent', $sContent);
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
                $iCount = $aParams['counter'];
                $oUebungen = new Application_Model_DbTable_Uebungen();
                $oUebungRow = $oUebungen->getUebung($iUebungId);

                $this->view->assign('iCount', $iCount);
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

        public function getUebung($iUebungId)
        {
            $oUebungDb = new Application_Model_DbTable_Uebungen();
            return $oUebungDb->getUebung($iUebungId);
        }
    }

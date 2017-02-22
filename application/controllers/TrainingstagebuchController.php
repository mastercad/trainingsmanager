<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 26.04.14
 * Time: 16:58
 */ 


class TrainingstagebuchController extends Zend_Controller_Action
{
    protected $_iMinBeanspruchterMuskel = NULL;
    protected $_iMaxBeanspruchterMuskel = NULL;
    protected $_aBeanspruchteMuskeln = array();

    public function __init()
    {
    }

    public function indexAction()
    {

    }

    /**
     * start dient als weiche um einen eventuell noch nicht vorhandenen tagebuch trainingsplan anzulegen
     * und weiter zu leiten, oder zu entscheiden ob der aktuell angeforderte trainingsplan nicht "trainierbar" ist,
     * oder um einen bereits angelegten zu öffnen und dahin weiter zu leiten
     */
    public function startAction()
    {
        // lade aktuellen trainingsplan, bei einem splitplan den aktuell laufenden oder den nächsten anstehenden
        // wird der split als hauptplan angefordert, wird ein link angeboten zur übersicht des trainingsplanes
        // damit man sich dort für einen split entscheiden kann

        // wird ein trainingsplan aufgerufen und ist noch nicht gestartet, wird ein neuer trainingstagebucheintrag
        // für diesen trainingsplan angelegt
        // vorher wird gecheckt ob noch ein offener trainingsplan existiert, ist das der fall, wird gefragt ob
        // an deren stelle weiter gemacht werden soll,
        // ist das der fall, wir dieser trainingsplan ab dem stand geladen, wo er abgebrochen wurde,
        // wenn nicht, wird der alte abgeschlossen und ein neuer begonnen
        $aParams = $this->getAllParams();
        $iUserId = 11;

        if (array_key_exists('id', $aParams)) {
            $iTrainingsplanId = $aParams['id'];
            $oTrainingsplaeneStorage = new Application_Model_DbTable_TrainingPlans();

            // erst informationen zum plan ziehen
            $oTrainingsplanRow = $oTrainingsplaeneStorage->findTrainingPlan($iTrainingsplanId);

            if (FALSE === $oTrainingsplanRow) {
                echo "Diesen Trainingsplan gibt es nicht!";
            } elseif (1 !== count($oTrainingsplanRow)) {
                echo "Es ist Irgendwas nicht in Ordnung, ich habe mit der ID " . $iTrainingsplanId .
                    " mehr als einen Trainingsplan gefunden!";
            } else {
                $oTrainingsplanRowSet = $oTrainingsplaeneStorage->findChildTrainingPlans($iTrainingsplanId);
                // wenn ein parent trainingsplan, split auswählen lassen
                if (FALSE !== $oTrainingsplanRowSet
                    && 0 < count($oTrainingsplanRowSet)
                ) {
                    echo "Dies ist ein Parent eines Splittrainingsplanes, bitte wählen Sie einen Split dieser " .
                        "Sammlung aus!";
                    $this->redirect('/trainingsplan/show/id/' . $iTrainingsplanId, $aParams);
                } else {
                    $oTrainingstagebuchTrainingsplaeneStorage =
                        new Application_Model_DbTable_TrainingDiaryTrainingPlans();

                    // checken ob ein anderer alter trainingsplan offen ist
                    /** @todo ausformulieren */

                    // checken ob der trainingsplan fortgesetzt werden soll oder neu angelegt
                    $oAktuellesTrainingstagebuch = $oTrainingstagebuchTrainingsplaeneStorage->findLastOpenTrainingPlan($iTrainingsplanId);

                    // keine tagebucheinträge vorhanden
                    if (0 == $oAktuellesTrainingstagebuch->count()) {
                        echo "Habe noch keinen Trainingstagebucheintrag für diesen Trainingsplan, der aber offen ist!";

                        $aData = array(
                            'trainingstagebuch_trainingsplan_trainingsplan_fk' => $iTrainingsplanId,
                            'trainingstagebuch_trainingsplan_eintrag_datum' => date('Y-m-d H:i:s'),
                            'trainingstagebuch_trainingsplan_eintrag_user_fk' => $iUserId
                        );
                        // @FIXME das hier macht keinen sinn! hier kommt man nur rein, wenn es keinen letzten offenen
                        // gibt, es wird dann der letzte offene übergeben an die view?!
                        $iTrainingstagebuchTrainingsplanId = $oTrainingstagebuchTrainingsplaeneStorage->insert($aData);
                        $oAktuellesTrainingstagebuch = $oTrainingstagebuchTrainingsplaeneStorage->findLastOpenTrainingPlan($iTrainingsplanId);
                        $this->redirect('/trainingstagebuch/show-uebung/id/' . $oAktuellesTrainingstagebuch->current()->trainingsplan_uebung_id, $aParams);
//                        $this->redirect('/trainingstagebuch/show/id/' . $iTrainingsplanId, $aParams);

                    // habe einen offenen trainingsplan gefunden, leite an die übersicht der übungen weiter
                    } elseif (1 <= $oAktuellesTrainingstagebuch->count()) {
                        echo "Habe einen Trainingstagebucheintrag für diesen trainingsplan gefunden, der offen ist!";
                        $this->redirect('/trainingstagebuch/show-uebung/id/' . $oAktuellesTrainingstagebuch->current()->trainingsplan_uebung_id, $aParams);
                    } else {
                        echo "Nichts getan in start trainingsplan! o.O";
                    }
                }
            }
        }
    }

    public function showAction()
    {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.touchSwipe.min.js', 'text/javascript');
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $iTrainingstagebuchTrainingsplanId = $aParams['id'];
            $oTrainingsplaeneStorage = new Application_Model_DbTable_TrainingPlans();
            $oTrainingstagebuchTrainingsplanRowSet = $oTrainingsplaeneStorage->findLastOpenTrainingPlan(
                $iTrainingstagebuchTrainingsplanId);

            if (0 < count($oTrainingstagebuchTrainingsplanRowSet)) {
                $oTrainingstagebuchTrainingsplanRow = null;
                $sContent = '';
                $oUebungMuskelnStorage = new Application_Model_DbTable_ExerciseMuscles();
                $aBeanspruchteMuskeln = array();
                $iMinBeanspruchterMuskel = NULL;
                $iMaxBeanspruchterMuskel = NULL;

                foreach ($oTrainingstagebuchTrainingsplanRowSet as $oTrainingstagebuchTrainingsplanRow) {
                    if (FALSE === empty($oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk)) {
                        $oBeanspruchteMuskelnFuerUebung = $oUebungMuskelnStorage->findMusclesForExercise(
                            $oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk);

                        foreach ($oBeanspruchteMuskelnFuerUebung as $oBeanspruchterMuskelFuerUebung) {
                            if (FALSE == array_key_exists($oBeanspruchterMuskelFuerUebung->muskel_name, $aBeanspruchteMuskeln)) {
                                $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] = 0;
                            }
                            $iBeanspruchung = intval($oBeanspruchterMuskelFuerUebung->uebung_muskel_beanspruchung);
                            $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] += $iBeanspruchung;
                            if (NULL == $iMinBeanspruchterMuskel
                                || $iMinBeanspruchterMuskel > $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
                            ) {
                                $iMinBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
                            }
                            if (NULL == $iMaxBeanspruchterMuskel
                                || $iMaxBeanspruchterMuskel < $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
                            ) {
                                $iMaxBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
                            }
                        }
                        $sContent .= $this->generateViewForTrainingsplan($oTrainingstagebuchTrainingsplanRow);
                    }
                }
                $aViewContent = $this->getTrainingsplanInfos($oTrainingstagebuchTrainingsplanRow);
                $this->view->assign('iMinBeanspruchterMuskel', $iMinBeanspruchterMuskel);
                $this->view->assign('iMaxBeanspruchterMuskel', $iMaxBeanspruchterMuskel);
                $this->view->assign('aBeanspruchteMuskeln', $aBeanspruchteMuskeln);
                $this->view->assign('iTrainingsplanId', $iTrainingstagebuchTrainingsplanId);
                $this->view->assign('iPrevTrainingsplanId', $aViewContent['prevTrainingsplanId']);
                $this->view->assign('iNextTrainingsplanId', $aViewContent['nextTrainingsplanId']);
                $this->view->assign('iActualPos', $aViewContent['actualPos']);
                $this->view->assign('iCount', $aViewContent['count']);
                $this->view->assign('sContent', $sContent);
            } else {
                echo "Habe keine Treffer für den Trainingsplan!";
            }
        }
    }

    public function showUebungAction()
    {
//        ini_set('display_errors',1);
//        error_reporting(E_ALL|E_STRICT);
//        error_log(APPLICATION_PATH . '/../data/error.log');

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.touchSwipe.min.js', 'text/javascript');
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $this->_aBeanspruchteMuskeln = array();
            $this->_iMinBeanspruchterMuskel = NULL;
            $this->_iMaxBeanspruchterMuskel = NULL;
            $iTrainingsplanUebungId = $aParams['id'];
            $oUebungenStorage = new Application_Model_DbTable_TrainingPlanExercises();
            $oTrainingstagebuchUebungRow = $oUebungenStorage->findTrainingDiaryByTrainingPlanExerciseId(
                $iTrainingsplanUebungId);

            if (0 < count($oTrainingstagebuchUebungRow)) {
                $sContent = $this->generateViewForUebung($oTrainingstagebuchUebungRow);
                $aViewContent = $this->getUebungInfos($oTrainingstagebuchUebungRow);
                $this->view->assign('iTrainingsplanUebungId', $iTrainingsplanUebungId);
                $this->view->assign('iPrevTrainingsplanUebungId', $aViewContent['prevTrainingsplanUebungId']);
                $this->view->assign('iNextTrainingsplanUebungId', $aViewContent['nextTrainingsplanUebungId']);
                $this->view->assign('iActualPos', $aViewContent['actualPos']);
                $this->view->assign('iCount', $aViewContent['count']);
                $this->view->assign('sContent', $sContent);
            } else {
                echo "Habe keine Treffer für diese Übung!";
            }
        }
    }

    public function editAction()
    {
        // hier kann man die übung bearbeiten, vielleicht ist das unnötig, oder man hat hier die ansicht einer
        // übung des aktuellen trainingsplanes drin
    }

    public function speichernAction()
    {

    }

    public function getTrainingsplanAction()
    {
        $this->view->layout()->disableLayout();
        $aParams = $this->getAllParams();
        if (TRUE === array_key_exists('id', $aParams)) {
            $this->_aBeanspruchteMuskeln = array();
            $iTrainingstagebuchTrainingsplanId = $aParams['id'];
            if (TRUE === is_numeric($iTrainingstagebuchTrainingsplanId)
                && 0 < $iTrainingstagebuchTrainingsplanId
            ) {
                $oTrainingsplaeneStorage = new Application_Model_DbTable_TrainingPlans();
                $oTrainingstagebuchTrainingsplanRowSet = $oTrainingsplaeneStorage->findLastOpenTrainingPlan(
                    $iTrainingstagebuchTrainingsplanId);

                if (0 < count($oTrainingstagebuchTrainingsplanRowSet)) {
                    $sContent = '';
                    $oUebungMuskelnStorage = new Application_Model_DbTable_ExerciseMuscles();
                    $oTrainingstagebuchTrainingsplanRow = null;

                    foreach ($oTrainingstagebuchTrainingsplanRowSet as $oTrainingstagebuchTrainingsplanRow) {

                        if (FALSE === empty($oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk)) {
                            $oBeanspruchteMuskelnFuerUebung = $oUebungMuskelnStorage->findMusclesForExercise(
                                $oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk);

                            if ($oBeanspruchteMuskelnFuerUebung instanceof Traversable) {
                                foreach ($oBeanspruchteMuskelnFuerUebung as $oBeanspruchterMuskelFuerUebung) {
                                    if (FALSE == array_key_exists($oBeanspruchterMuskelFuerUebung->muskel_name, $this->_aBeanspruchteMuskeln)) {
                                        $this->_aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] = 0;
                                    }
                                    $iBeanspruchung = $oBeanspruchterMuskelFuerUebung->uebung_muskel_beanspruchung;
                                    $this->_aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] += $iBeanspruchung;

                                    if (NULL == $this->_iMinBeanspruchterMuskel
                                        || $this->_iMinBeanspruchterMuskel > $this->_aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
                                    ) {
                                        $this->_iMinBeanspruchterMuskel = $this->_aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
                                    }
                                    if (NULL == $this->_iMaxBeanspruchterMuskel
                                        || $this->_iMaxBeanspruchterMuskel < $this->_aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
                                    ) {
                                        $this->_iMaxBeanspruchterMuskel = $this->_aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
                                    }
                                }
                            }
                            $sContent .= $this->generateViewForTrainingsplan($oTrainingstagebuchTrainingsplanRow);
                        }
                    }
                    $aViewContent = $this->getTrainingsplanInfos($oTrainingstagebuchTrainingsplanRow);
                    $this->view->assign('sContent', $sContent);
                    $this->view->assign('iMinBeanspruchterMuskel', $this->_iMinBeanspruchterMuskel);
                    $this->view->assign('iMaxBeanspruchterMuskel', $this->_iMaxBeanspruchterMuskel);
                    $this->view->assign('aBeanspruchteMuskeln', $this->_aBeanspruchteMuskeln);
                    $sLayout = $this->view->render('trainingstagebuch/get-base-trainingsplan-layout.phtml');
                    $aViewContent['content'] = base64_encode($sLayout);
                    $this->view->assign('sJson', json_encode($aViewContent));
                } else {
                    echo "Habe keine Treffer für den Trainingsplan!";
                }
            }
        }
    }

    public function getUebungAction()
    {
        $this->view->layout()->disableLayout();
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $this->_aBeanspruchteMuskeln = array();
            $this->_iMinBeanspruchterMuskel = NULL;
            $this->_iMaxBeanspruchterMuskel = NULL;
            $iTrainingsplanUebungId = $aParams['id'];
            $oUebungenStorage = new Application_Model_DbTable_TrainingPlanExercises();
            $oTrainingstagebuchUebungRow =
                $oUebungenStorage->findTrainingDiaryByTrainingPlanExerciseId($iTrainingsplanUebungId);

            if (0 < count($oTrainingstagebuchUebungRow)) {
                $sContent = $this->generateViewForUebung($oTrainingstagebuchUebungRow);
                $aViewContent = $this->getUebungInfos($oTrainingstagebuchUebungRow);
//                $this->view->assign('iTrainingsplanUebungId', $iTrainingsplanUebungId);
//                $this->view->assign('iPrevTrainingsplanUebungId', $aViewContent['prevTrainingsplanUebungId']);
//                $this->view->assign('iNextTrainingsplanUebungId', $aViewContent['nextTrainingsplanUebungId']);
//                $this->view->assign('iActualPos', $aViewContent['actualPos']);
//                $this->view->assign('iCount', $aViewContent['count']);
                $aViewContent['content'] = base64_encode($sContent);
                $this->view->assign('sJson', json_encode($aViewContent));
            } else {
                echo "Habe keine Treffer für diese Übung!";
            }
        }
    }


    public function getTrainingsplanInfos($oTrainingstagebuchTrainingsplanRow)
    {
        $aViewContent = array();
        $aTrainingsplanIds = array();
        $aViewContent['prevTrainingsplanId'] = NULL;
        $aViewContent['nextTrainingsplanId'] = NULL;

        if (FALSE === empty($oTrainingstagebuchTrainingsplanRow->trainingsplan_parent_fk)) {
            $oTrainingsplaeneStorage = new Application_Model_DbTable_TrainingPlans();
            $oChildTrainingsplaene = $oTrainingsplaeneStorage->findChildTrainingPlans($oTrainingstagebuchTrainingsplanRow->trainingsplan_parent_fk);
            if (FALSE !== $oChildTrainingsplaene) {
                foreach ($oChildTrainingsplaene as $oChildTrainingsplan) {
                    $aTrainingsplanIds[] = $oChildTrainingsplan->trainingsplan_id;
                }
            }
        }
        $aViewContent['count'] = count($aTrainingsplanIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search($oTrainingstagebuchTrainingsplanRow->trainingsplan_id, $aTrainingsplanIds);
            if (0 < $aViewContent['actualPos']) {
                $aViewContent['prevTrainingsplanId'] = $aTrainingsplanIds[$aViewContent['actualPos'] - 1];
            }
            if ($aViewContent['actualPos'] < ($aViewContent['count'] - 1)) {
                $aViewContent['nextTrainingsplanId'] = $aTrainingsplanIds[$aViewContent['actualPos'] + 1];
            }
        }
        $aViewContent['trainingsplanId'] = $oTrainingstagebuchTrainingsplanRow->trainingsplan_id;
        return $aViewContent;
    }

    public function getUebungInfos($oTrainingstagebuchUebungRow)
    {
        $aViewContent = array();
        $aUebungIds = array();
        $aViewContent['prevTrainingsplanUebungId'] = NULL;
        $aViewContent['nextTrainingsplanUebungId'] = NULL;

        if (FALSE === empty($oTrainingstagebuchUebungRow->trainingsplan_id)) {
            $oTrainingsplaeneStorage = new Application_Model_DbTable_TrainingPlanExercises();
            $oUebungen = $oTrainingsplaeneStorage->findExercisesByTrainingPlanId($oTrainingstagebuchUebungRow->trainingsplan_id);
            if (FALSE !== $oUebungen) {
                foreach ($oUebungen as $oUebung) {
                    $aUebungIds[] = $oUebung->trainingsplan_uebung_id;
                }
            }
        }
        $aViewContent['count'] = count($aUebungIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search($oTrainingstagebuchUebungRow->trainingsplan_uebung_id, $aUebungIds);
            if (0 < $aViewContent['actualPos']) {
                $aViewContent['prevTrainingsplanUebungId'] = $aUebungIds[$aViewContent['actualPos'] - 1];
            }
            if ($aViewContent['actualPos'] < ($aViewContent['count'] - 1)) {
                $aViewContent['nextTrainingsplanUebungId'] = $aUebungIds[$aViewContent['actualPos'] + 1];
            }
        }
        $aViewContent['trainingsplanUebungId'] = $oTrainingstagebuchUebungRow->trainingsplan_uebung_id;
        return $aViewContent;
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $oTrainingstagebuchUebungRow
     *
     * @return string
     */
    public function generateViewForUebung($oTrainingstagebuchUebungRow)
    {
        $aBeanspruchteMuskeln = array();
        $iMinBeanspruchterMuskel = NULL;
        $iMaxBeanspruchterMuskel = NULL;
        $oUebungMuskelnStorage = new Application_Model_DbTable_ExerciseMuscles();
        $oBeanspruchteMuskelnFuerUebung = $oUebungMuskelnStorage->findMusclesForExercise(
            $oTrainingstagebuchUebungRow->trainingsplan_uebung_fk);

        foreach ($oBeanspruchteMuskelnFuerUebung as $oBeanspruchterMuskelFuerUebung) {
            if (FALSE == array_key_exists($oBeanspruchterMuskelFuerUebung->muskel_name, $aBeanspruchteMuskeln)) {
                $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] = 0;
            }
            $iBeanspruchung = intval($oBeanspruchterMuskelFuerUebung->uebung_muskel_beanspruchung);
            $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] += $iBeanspruchung;

            if (NULL == $iMinBeanspruchterMuskel
                || $iMinBeanspruchterMuskel > $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
            ) {
                $iMinBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
            }
            if (NULL == $iMaxBeanspruchterMuskel
                || $iMaxBeanspruchterMuskel < $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
            ) {
                $iMaxBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
            }
        }
        $this->_iMinBeanspruchterMuskel = $iMinBeanspruchterMuskel;
        $this->_iMaxBeanspruchterMuskel = $iMaxBeanspruchterMuskel;
        $this->_aBeanspruchteMuskeln = $aBeanspruchteMuskeln;
        $this->view->assign('iMinBeanspruchterMuskel', $iMinBeanspruchterMuskel);
        $this->view->assign('iMaxBeanspruchterMuskel', $iMaxBeanspruchterMuskel);
        $this->view->assign('aBeanspruchteMuskeln', $aBeanspruchteMuskeln);
        $this->view->assign($oTrainingstagebuchUebungRow->toArray());
//        Zend_Debug::dump($oTrainingstagebuchUebungRow);
        return $this->view->render('trainingstagebuch/partials/trainingstagebuch-uebung-partial.phtml');
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $oTrainingstagebuchTrainingsplanRow
     * @return string
     */
    public function generateViewForTrainingsplan($oTrainingstagebuchTrainingsplanRow)
    {
        $this->view->assign($oTrainingstagebuchTrainingsplanRow->toArray());
        return $this->view->render('trainingstagebuch/partials/trainingstagebuch-trainingsplan-partial.phtml');
    }
}

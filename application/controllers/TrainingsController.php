<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 26.04.14
 * Time: 16:58
 */ 


class TrainingsController extends Zend_Controller_Action
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
     * start dient als weiche um einen eventuell noch nicht vorhandenen tagebuch training-plans anzulegen
     * und weiter zu leiten, oder zu entscheiden ob der aktuell angeforderte training-plans nicht "trainierbar" ist,
     * oder um einen bereits angelegten zu öffnen und dahin weiter zu leiten
     */
    public function startAction()
    {
        // lade aktuellen training-plans, bei einem splitplan den aktuell laufenden oder den nächsten anstehenden
        // wird der split als hauptplan angefordert, wird ein link angeboten zur übersicht des trainingsplanes
        // damit man sich dort für einen split entscheiden kann

        // wird ein training-plans aufgerufen und ist noch nicht gestartet, wird ein neuer trainingstagebucheintrag
        // für diesen training-plans angelegt
        // vorher wird gecheckt ob noch ein offener training-plans existiert, ist das der fall, wird gefragt ob
        // an deren stelle weiter gemacht werden soll,
        // ist das der fall, wir dieser training-plans ab dem stand geladen, wo er abgebrochen wurde,
        // wenn nicht, wird der alte abgeschlossen und ein neuer begonnen
        $aParams = $this->getAllParams();
        $iUserId = 22;

        if (array_key_exists('id', $aParams)) {
            $iTrainingsplanId = $aParams['id'];
            $oTrainingsplaeneStorage = new Model_DbTable_TrainingPlans();

            // erst informationen zum plan ziehen
            $oTrainingsplanRow = $oTrainingsplaeneStorage->findFirstExerciseInTrainingPlan($iTrainingsplanId);

            if (FALSE === $oTrainingsplanRow) {
                echo "Diesen Trainingsplan gibt es nicht!";
            } elseif (1 !== count($oTrainingsplanRow)) {
                echo "Es ist Irgendwas nicht in Ordnung, ich habe mit der ID " . $iTrainingsplanId .
                    " mehr als einen Trainingsplan gefunden!";
            } else {
                $oTrainingsplanRowSet = $oTrainingsplaeneStorage->findChildTrainingPlans($iTrainingsplanId);
                // wenn ein parent training-plans, split auswählen lassen
                if (FALSE !== $oTrainingsplanRowSet
                    && 0 < count($oTrainingsplanRowSet)
                ) {
                    echo "Dies ist ein Parent eines Splittrainingsplanes, bitte wählen Sie einen Split dieser " .
                        "Sammlung aus!";
                    $this->redirect('/training-plans/show/id/' . $iTrainingsplanId, $aParams);
                } else {
                    $oTrainingstagebuchTrainingsplaeneStorage = new Model_DbTable_TrainingsXTrainingPlan();

                    // checken ob ein anderer alter training-plans offen ist
                    /** @todo ausformulieren */

                    // checken ob der training-plans fortgesetzt werden soll oder neu angelegt
                    $oAktuellesTrainingstagebuch = $oTrainingstagebuchTrainingsplaeneStorage->findLastOpenTrainingPlan($iTrainingsplanId);

                    // keine tagebucheinträge vorhanden
                    if (0 == $oAktuellesTrainingstagebuch->count()) {
                        echo "Habe noch keinen Trainingstagebucheintrag für diesen Trainingsplan, der aber offen ist!";

                        $trainingsDb = new Model_DbTable_Trainings();
                        $data = [
                            'trainings_training_plan_x_exercise_fk' => $oTrainingsplanRow->offsetGet('training_plan_x_exercise_id'),
                            'trainings_create_date' => date('Y-m-d H:i:s'),
                            'trainings_create_user_fk' => $iUserId
                        ];
                        $trainingsId = $trainingsDb->insert($data);

                        $aData = [
                            'trainings_x_training_plan_trainings_fk' => $trainingsId,
                            'trainings_x_training_plan_training_plan_fk' => $iTrainingsplanId,
                            'trainings_x_training_plan_create_date' => date('Y-m-d H:i:s'),
                            'trainings_x_training_plan_create_user_fk' => $iUserId
                        ];

                        $iTrainingstagebuchTrainingsplanId = $oTrainingstagebuchTrainingsplaeneStorage->insert($aData);

                        $trainingsXTrainingPlanExerciseDb = new Model_DbTable_TrainingsXTrainingPlanExercise();

                        $data = [
                            'trainings_x_training_plan_exercise_training_plan_x_exercise_fk' => $oTrainingsplanRow->offsetGet('training_plan_x_exercise_id'),
                            'trainings_x_training_plan_exercise_create_date' => date('Y-m-d H:i:s'),
                            'trainings_x_training_plan_exercise_create_user_fk' => $iUserId
                        ];
                        $trainingXTrainingPlanExerciseId = $trainingsXTrainingPlanExerciseDb->insert($data);

                        $oAktuellesTrainingstagebuch = $oTrainingstagebuchTrainingsplaeneStorage->findLastOpenTrainingPlan($iTrainingsplanId);
                        $this->redirect('/trainings/show-exercise/id/' . $oAktuellesTrainingstagebuch->current()->training_plan_x_exercise_exercise_fk, $aParams);
//                        $this->redirect('/trainingstagebuch/show/id/' . $iTrainingsplanId, $aParams);

                    // habe einen offenen training-plans gefunden, leite an die übersicht der übungen weiter
                    } elseif (1 <= $oAktuellesTrainingstagebuch->count()) {
                        echo "Habe einen Trainingstagebucheintrag für diesen training-plans gefunden, der offen ist!";
                        $this->redirect('/trainings/show-exercise/id/' . $oAktuellesTrainingstagebuch->current()->training_plan_x_exercise_exercise_fk, $aParams);
                    } else {
                        echo "Nichts getan in start training-plans! o.O";
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
            $trainingPlanId = $aParams['id'];
            $trainingsXTrainingPlanDb = new Model_DbTable_TrainingsXTrainingPlan();
            $trainingsExerciseCollection = $trainingsXTrainingPlanDb->findLastOpenTrainingPlan($trainingPlanId);

            if (0 < count($trainingsExerciseCollection)) {
                $trainingsExercise = null;
                $sContent = '';
                $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
                $aBeanspruchteMuskeln = array();
                $iMinBeanspruchterMuskel = NULL;
                $iMaxBeanspruchterMuskel = NULL;

                foreach ($trainingsExerciseCollection as $trainingsExercise) {
                    if (FALSE === empty($oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk)) {
                        $oBeanspruchteMuskelnFuerUebung = $exerciseXMuscleDb->findMusclesForExercise(
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
                $aViewContent = $this->getTrainingsplanInfos($trainingsExercise);
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

    public function showExerciseAction()
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
            $trainingPlanExerciseId = $aParams['id'];
            $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
            $trainingPlanXExercise = $trainingPlanXExerciseDb->findTrainingDiaryByTrainingPlanExerciseId(
                $trainingPlanExerciseId);

            if (0 < count($trainingPlanXExercise)) {
                $sContent = $this->generateViewForExercise($trainingPlanXExercise);
                $aViewContent = $this->getExerciseInfo($trainingPlanXExercise);
                $this->view->assign('trainingPlanExerciseId', $trainingPlanExerciseId);
                $this->view->assign('prevTrainingPlanExerciseId', $aViewContent['prevTrainingPlanExerciseId']);
                $this->view->assign('nextTrainingPlanExerciseId', $aViewContent['nextTrainingPlanExerciseId']);
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

    public function saveAction()
    {

    }

    public function getTrainingPlanAction()
    {
        $this->view->layout()->disableLayout();
        $aParams = $this->getAllParams();
        if (TRUE === array_key_exists('id', $aParams)) {
            $this->_aBeanspruchteMuskeln = array();
            $iTrainingstagebuchTrainingsplanId = $aParams['id'];
            if (TRUE === is_numeric($iTrainingstagebuchTrainingsplanId)
                && 0 < $iTrainingstagebuchTrainingsplanId
            ) {
                $oTrainingsplaeneStorage = new Model_DbTable_TrainingsXTrainingPlan();
                $oTrainingstagebuchTrainingsplanRowSet = $oTrainingsplaeneStorage->findLastOpenTrainingPlan(
                    $iTrainingstagebuchTrainingsplanId);

                if (0 < count($oTrainingstagebuchTrainingsplanRowSet)) {
                    $sContent = '';
                    $oUebungMuskelnStorage = new Model_DbTable_ExerciseXMuscle();
                    $oTrainingstagebuchTrainingsplanRow = null;

                    foreach ($oTrainingstagebuchTrainingsplanRowSet as $oTrainingstagebuchTrainingsplanRow) {

                        if (FALSE === empty($oTrainingstagebuchTrainingsplanRow->training_plan_x_exercise_exercise_fk)) {
                            $oBeanspruchteMuskelnFuerUebung = $oUebungMuskelnStorage->findMusclesForExercise(
                                $oTrainingstagebuchTrainingsplanRow->training_plan_x_exercise_exercise_fk);

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
                    $aViewContent = $this->getTrainingPlanInfo($oTrainingstagebuchTrainingsplanRow);
                    $this->view->assign('sContent', $sContent);
                    $this->view->assign('iMinBeanspruchterMuskel', $this->_iMinBeanspruchterMuskel);
                    $this->view->assign('iMaxBeanspruchterMuskel', $this->_iMaxBeanspruchterMuskel);
                    $this->view->assign('aBeanspruchteMuskeln', $this->_aBeanspruchteMuskeln);
                    $sLayout = $this->view->render('trainingstagebuch/get-base-training-plans-layout.phtml');
                    $aViewContent['content'] = base64_encode($sLayout);
                    $this->view->assign('sJson', json_encode($aViewContent));
                } else {
                    echo "Habe keine Treffer für den Trainingsplan!";
                }
            }
        }
    }

    public function getExerciseAction()
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
            $oUebungenStorage = new Model_DbTable_TrainingPlanXExercise();
            $oTrainingstagebuchUebungRow =
                $oUebungenStorage->findTrainingDiaryByTrainingPlanExerciseId($iTrainingsplanUebungId);

            if (0 < count($oTrainingstagebuchUebungRow)) {
                $sContent = $this->generateViewForExercise($oTrainingstagebuchUebungRow);
                $aViewContent = $this->getExerciseInfo($oTrainingstagebuchUebungRow);
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


    private function getTrainingPlanInfo($oTrainingstagebuchTrainingsplanRow)
    {
        $aViewContent = array();
        $aTrainingsplanIds = array();
        $aViewContent['prevTrainingsplanId'] = NULL;
        $aViewContent['nextTrainingsplanId'] = NULL;

        if (FALSE === empty($oTrainingstagebuchTrainingsplanRow->training_plan_parent_fk)) {
            $oTrainingsplaeneStorage = new Model_DbTable_TrainingPlans();
            $oChildTrainingsplaene = $oTrainingsplaeneStorage->findChildTrainingPlans($oTrainingstagebuchTrainingsplanRow->training_plan_parent_fk);
            if (FALSE !== $oChildTrainingsplaene) {
                foreach ($oChildTrainingsplaene as $oChildTrainingsplan) {
                    $aTrainingsplanIds[] = $oChildTrainingsplan->training_plan_id;
                }
            }
        }
        $aViewContent['count'] = count($aTrainingsplanIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search($oTrainingstagebuchTrainingsplanRow->training_plan_id, $aTrainingsplanIds);
            if (0 < $aViewContent['actualPos']) {
                $aViewContent['prevTrainingsplanId'] = $aTrainingsplanIds[$aViewContent['actualPos'] - 1];
            }
            if ($aViewContent['actualPos'] < ($aViewContent['count'] - 1)) {
                $aViewContent['nextTrainingsplanId'] = $aTrainingsplanIds[$aViewContent['actualPos'] + 1];
            }
        }
        $aViewContent['trainingsplanId'] = $oTrainingstagebuchTrainingsplanRow->training_plan_id;
        return $aViewContent;
    }

    public function getExerciseInfo($oTrainingstagebuchUebungRow)
    {
        $aViewContent = array();
        $aUebungIds = array();
        $aViewContent['prevTrainingsplanUebungId'] = NULL;
        $aViewContent['nextTrainingsplanUebungId'] = NULL;

        if (FALSE === empty($oTrainingstagebuchUebungRow->training_plan_id)) {
            $oTrainingsplaeneStorage = new Model_DbTable_TrainingPlanXExercise();
            $oUebungen = $oTrainingsplaeneStorage->findExercisesByTrainingPlanId($oTrainingstagebuchUebungRow->training_plan_id);
            if (FALSE !== $oUebungen) {
                foreach ($oUebungen as $oUebung) {
                    $aUebungIds[] = $oUebung->training_plan_x_exercise_id;
                }
            }
        }
        $aViewContent['count'] = count($aUebungIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search($oTrainingstagebuchUebungRow->training_plan_x_exercise_id, $aUebungIds);
            if (0 < $aViewContent['actualPos']) {
                $aViewContent['prevTrainingsplanUebungId'] = $aUebungIds[$aViewContent['actualPos'] - 1];
            }
            if ($aViewContent['actualPos'] < ($aViewContent['count'] - 1)) {
                $aViewContent['nextTrainingsplanUebungId'] = $aUebungIds[$aViewContent['actualPos'] + 1];
            }
        }
        $aViewContent['trainingsplanUebungId'] = $oTrainingstagebuchUebungRow->training_plan_x_exercise_id;
        return $aViewContent;
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $oTrainingstagebuchUebungRow
     *
     * @return string
     */
    public function generateViewForExercise($oTrainingstagebuchUebungRow)
    {
        $aBeanspruchteMuskeln = array();
        $iMinBeanspruchterMuskel = NULL;
        $iMaxBeanspruchterMuskel = NULL;
        $oUebungMuskelnStorage = new Model_DbTable_ExerciseXMuscle();
        $oBeanspruchteMuskelnFuerUebung = $oUebungMuskelnStorage->findMusclesForExercise(
            $oTrainingstagebuchUebungRow->training_plan_x_exercise_exercise_fk);

        foreach ($oBeanspruchteMuskelnFuerUebung as $oBeanspruchterMuskelFuerUebung) {
            if (FALSE == array_key_exists($oBeanspruchterMuskelFuerUebung->muscle_name, $aBeanspruchteMuskeln)) {
                $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muscle_name] = 0;
            }
            $iBeanspruchung = intval($oBeanspruchterMuskelFuerUebung->exercise_x_muscle_muscle_use);
            $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muscle_name] += $iBeanspruchung;

            if (NULL == $iMinBeanspruchterMuskel
                || $iMinBeanspruchterMuskel > $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muscle_name]
            ) {
                $iMinBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muscle_name];
            }
            if (NULL == $iMaxBeanspruchterMuskel
                || $iMaxBeanspruchterMuskel < $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muscle_name]
            ) {
                $iMaxBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muscle_name];
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
        return $this->view->render('loops/trainings-exercise.phtml');
    }

    /**
     * @param \Zend_Db_Table_Row_Abstract $oTrainingstagebuchTrainingsplanRow
     * @return string
     */
    public function generateViewForTrainingsplan($oTrainingstagebuchTrainingsplanRow)
    {
        $this->view->assign($oTrainingstagebuchTrainingsplanRow->toArray());
        return $this->view->render('trainingstagebuch/partials/trainingstagebuch-training-plans-partial.phtml');
    }
}

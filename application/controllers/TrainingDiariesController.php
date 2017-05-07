<?php

/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 26.04.14
 * Time: 16:58
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class TrainingDiariesController extends AbstractController {
    protected $_iMinBeanspruchterMuskel = null;
    protected $_iMaxBeanspruchterMuskel = null;
    protected $_aBeanspruchteMuskeln = array();

    public function init() {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_training_plan_accordion.js',
                'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript');
        }
    }

    public function indexAction() {
    }

    /**
     * start dient als weiche um einen eventuell noch nicht vorhandenen tagebuch training-plans anzulegen
     * und weiter zu leiten, oder zu entscheiden ob der aktuell angeforderte training-plans nicht "trainierbar" ist,
     * oder um einen bereits angelegten zu öffnen und dahin weiter zu leiten
     */
    public function startAction() {
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
        $iUserId = $this->findCurrentUserId();

        if (array_key_exists('id', $aParams)) {
            $trainingPlanId = $aParams['id'];
            $oTrainingsplaeneStorage = new Model_DbTable_TrainingPlans();

            // erst informationen zum plan ziehen
            $oTrainingsplanRow = $oTrainingsplaeneStorage->findFirstExerciseInTrainingPlan($trainingPlanId);

            if (false === $oTrainingsplanRow) {
                echo "Diesen Trainingsplan gibt es nicht!";
            } elseif (1 !== count($oTrainingsplanRow)) {
                echo "Es ist Irgendwas nicht in Ordnung, ich habe mit der ID " . $trainingPlanId .
                    " mehr als einen Trainingsplan gefunden!";
            } else {
                $oTrainingsplanRowSet = $oTrainingsplaeneStorage->findChildTrainingPlans($trainingPlanId);
                // wenn ein parent training-plans, split auswählen lassen
                if (false !== $oTrainingsplanRowSet
                    && 0 < count($oTrainingsplanRowSet)
                ) {
                    echo "Dies ist ein Parent eines Splittrainingsplanes, bitte wählen Sie einen Split dieser " .
                        "Sammlung aus!";
                    $this->redirect('/training-plans/show/id/' . $trainingPlanId, $aParams);
                } else {
                    $trainingDiaryXTrainingPlanStorage = new Model_DbTable_TrainingDiaryXTrainingPlan();

                    // checken ob ein anderer alter training-plans offen ist
                    /** @todo ausformulieren */

                    // checken ob der training-plans fortgesetzt werden soll oder neu angelegt
                    $oAktuellesTrainingstagebuch =
                        $trainingDiaryXTrainingPlanStorage->findLastOpenTrainingPlanByTrainingPlanIdAndUserId($trainingPlanId, $iUserId);

                    // keine tagebucheinträge vorhanden
                    if (! $oAktuellesTrainingstagebuch->count()) {
                        echo "Habe noch keinen Trainingstagebucheintrag für diesen Trainingsplan, der aber offen ist!";

                        $trainingDiariesDb = new Model_DbTable_TrainingDiaries();
                        $data = [
                            'training_diary_create_date' => date('Y-m-d H:i:s'),
                            'training_diary_create_user_fk' => $iUserId
                        ];
                        $trainingDiaryId = $trainingDiariesDb->insert($data);

                        $aData = [
                            'training_diary_x_training_plan_training_diary_fk' => $trainingDiaryId,
                            'training_diary_x_training_plan_training_plan_fk' => $trainingPlanId,
                            'training_diary_x_training_plan_create_date' => date('Y-m-d H:i:s'),
                            'training_diary_x_training_plan_create_user_fk' => $iUserId
                        ];

                        $trainingDiaryXTrainingPlanId = $trainingDiaryXTrainingPlanStorage->insert($aData);

                        $trainingDiaryXTrainingPlanExerciseId = $this->createTrainingDiaryExerciseEntry(
                            $oTrainingsplanRow->offsetGet('training_plan_x_exercise_id'),
                            $trainingDiaryId
                        );

                        $oAktuellesTrainingstagebuch = $trainingDiaryXTrainingPlanStorage->findLastOpenTrainingPlanByTrainingPlanIdAndUserId($trainingPlanId, $iUserId);
                        $this->redirect(
                            '/training-diaries/show-exercise/id/' . $oAktuellesTrainingstagebuch->current()->training_plan_x_exercise_exercise_fk, $aParams);

                        // habe einen offenen training-plans gefunden, leite an die übersicht der übungen weiter
                    } elseif (1 <= $oAktuellesTrainingstagebuch->count()) {
                        $this->redirect('/training-diaries/show-exercise/id/' . $oAktuellesTrainingstagebuch->current()->training_plan_x_exercise_exercise_fk,
                            $aParams);
                    } else {
                        echo "Nichts getan in start training-plans! o.O";
                    }
                }
            }
        }
    }

    private function createTrainingDiaryExerciseEntry($trainingPlanXExerciseId, $trainingDiaryId)
    {
        $iUserId = 22;

        $trainingDiaryXTrainingPlanExerciseDb = new Model_DbTable_TrainingDiaryXTrainingPlanExercise();

        $data = [
            'training_diary_x_training_plan_exercise_t_p_x_e_fk' => $trainingPlanXExerciseId,
            'training_diary_x_training_plan_exercise_training_diary_fk' => $trainingDiaryId,
            'training_diary_x_training_plan_exercise_create_date' => date('Y-m-d H:i:s'),
            'training_diary_x_training_plan_exercise_create_user_fk' => $iUserId
        ];
        return $trainingDiaryXTrainingPlanExerciseDb->insert($data);
    }

    public function showAction() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.touchSwipe.min.js',
            'text/javascript');
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $trainingPlanId = $aParams['id'];
            $trainingDiaryXTrainingPlanDb = new Model_DbTable_TrainingDiaryXTrainingPlan();
            $trainingDiaryExerciseCollection = $trainingDiaryXTrainingPlanDb->findLastOpenTrainingPlan($trainingPlanId);

            if (0 < count($trainingDiaryExerciseCollection)) {
                $trainingsExercise = null;
                $sContent = '';
                $exerciseXMuscleDb = new Model_DbTable_ExerciseXMuscle();
                $aBeanspruchteMuskeln = array();
                $iMinBeanspruchterMuskel = null;
                $iMaxBeanspruchterMuskel = null;

                foreach ($trainingDiaryExerciseCollection as $trainingsExercise) {
                    if (false === empty($oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk)) {
                        $oBeanspruchteMuskelnFuerUebung = $exerciseXMuscleDb->findMusclesForExercise(
                            $oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk);

                        foreach ($oBeanspruchteMuskelnFuerUebung as $oBeanspruchterMuskelFuerUebung) {
                            if (false == array_key_exists($oBeanspruchterMuskelFuerUebung->muskel_name,
                                    $aBeanspruchteMuskeln)
                            ) {
                                $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] = 0;
                            }
                            $iBeanspruchung = intval($oBeanspruchterMuskelFuerUebung->uebung_muskel_beanspruchung);
                            $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name] += $iBeanspruchung;
                            if (null == $iMinBeanspruchterMuskel
                                || $iMinBeanspruchterMuskel > $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
                            ) {
                                $iMinBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
                            }
                            if (null == $iMaxBeanspruchterMuskel
                                || $iMaxBeanspruchterMuskel < $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name]
                            ) {
                                $iMaxBeanspruchterMuskel = $aBeanspruchteMuskeln[$oBeanspruchterMuskelFuerUebung->muskel_name];
                            }
                        }
                        $sContent .= $this->generateViewForTrainingsplan($oTrainingstagebuchTrainingsplanRow);
                    }
                }
                $aViewContent = $this->getTrainingPlanInfo($trainingsExercise);
                $this->view->assign('iMinBeanspruchterMuskel', $iMinBeanspruchterMuskel);
                $this->view->assign('iMaxBeanspruchterMuskel', $iMaxBeanspruchterMuskel);
                $this->view->assign('aBeanspruchteMuskeln', $aBeanspruchteMuskeln);
                $this->view->assign('iTrainingsplanId', $iTrainingstagebuchTrainingsplanId);
                $this->view->assign('iPrevTrainingsplanId', $aViewContent['prevTrainingsplanId']);
                $this->view->assign('iNextTrainingsplanId', $aViewContent['nextTrainingsplanId']);
                $this->view->assign('iActualPos', $aViewContent['actualPos']);
                $this->view->assign('iCount', $aViewContent['count']);
                $this->view->assign('exerciseContent', $sContent);
            } else {
                echo "Habe keine Treffer für den Trainingsplan!";
            }
        }
    }

    public function showExerciseAction() {

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.touchSwipe.min.js',
            'text/javascript');
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $this->_aBeanspruchteMuskeln = array();
            $this->_iMinBeanspruchterMuskel = null;
            $this->_iMaxBeanspruchterMuskel = null;
            $trainingPlanExerciseId = $aParams['id'];

            $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
            $activeTrainingPlanXExercise = $trainingPlanXExerciseDb->findTrainingDiaryByTrainingPlanExerciseId(
                $trainingPlanExerciseId);

            $exercisesContent = '';

            if ($activeTrainingPlanXExercise) {
                $trainingDiaryXTrainingPlanDb = new Model_DbTable_TrainingDiaryXTrainingPlan();
                $trainingPlanXExerciseCollection = $trainingDiaryXTrainingPlanDb->findExercisesByTrainingDiaryId(
                    $activeTrainingPlanXExercise->offsetGet('training_diary_x_training_plan_training_diary_fk'));
                $count = 0;
                $bFirstActiveSet = false;
                foreach ($trainingPlanXExerciseCollection as $trainingPlanXExercise) {
                    $trainingDiaryViewGenerator = new Service_Generator_View_TrainingDiaries($this->view);
                    $trainingDiaryViewGenerator->setExercisesCount(count($trainingPlanXExerciseCollection));
                    $exercisesContent .= $trainingDiaryViewGenerator->generateExerciseContent($trainingPlanXExercise);
                    $aViewContent = $this->getExerciseInfo($trainingPlanXExercise);
                    $this->view->assign('trainingDiaryId', $trainingPlanXExercise->offsetGet('training_diary_id'));
                    $this->view->assign('trainingPlanExerciseId', $trainingPlanExerciseId);
                    $this->view->assign('prevTrainingPlanExerciseId', $aViewContent['prevTrainingPlanExerciseId']);
                    $this->view->assign('nextTrainingPlanExerciseId', $aViewContent['nextTrainingPlanExerciseId']);
                    $this->view->assign('iActualPos', $aViewContent['actualPos']);
                    ++$count;

                }
            } else {
                $exercisesContent = "Habe keine Treffer für diese Übung!";
            }
            $this->view->assign('exerciseContent', $exercisesContent);
        }
    }

    public function editAction() {
        // hier kann man die übung bearbeiten, vielleicht ist das unnötig, oder man hat hier die ansicht einer
        // übung des aktuellen trainingsplanes drin
    }

    public function saveAction() {

        if (0 < $this->getParam('trainingDiaryExerciseInformation')) {
            $userId = 1;

            $trainingPlanDiaryExerciseInformation = $this->getParam('trainingDiaryExerciseInformation');
            $trainingDiaryXTrainingPlanExerciseId = $trainingPlanDiaryExerciseInformation['trainingDiaryXTrainingPlanExerciseId'];

            if (empty($trainingDiaryXTrainingPlanExerciseId)) {
                $trainingPlanXExerciseId = $trainingPlanDiaryExerciseInformation['trainingPlanXExerciseId'];
                $trainingDiaryId = $trainingPlanDiaryExerciseInformation['trainingDiaryId'];
                $trainingDiaryXTrainingPlanExerciseId = $this->createTrainingDiaryExerciseEntry($trainingPlanXExerciseId, $trainingDiaryId);
            }

            $exerciseOptions = $trainingPlanDiaryExerciseInformation['exerciseOptions'];
            $trainingDiaryXExerciseOptionDb = new Model_DbTable_TrainingDiaryXExerciseOption();
            $currentExerciseOptionsInDb = $trainingDiaryXExerciseOptionDb->findExerciseOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryXTrainingPlanExerciseId);
            $currentTrainingDiaryXExerciseOptionsCollection = [];

            foreach ($currentExerciseOptionsInDb as $exerciseOption) {
                $currentTrainingDiaryXExerciseOptionsCollection[$exerciseOption['exercise_option_id']] = $exerciseOption;
            }

            $deviceOptions = $trainingPlanDiaryExerciseInformation['deviceOptions'];
            $trainingDiaryXDeviceOptionDb = new Model_DbTable_TrainingDiaryXDeviceOption();
            $currentDeviceOptionsInDb = $trainingDiaryXDeviceOptionDb->findDeviceOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryXTrainingPlanExerciseId);
            $currentTrainingDiaryXDeviceOptionsCollection = [];

            foreach ($currentDeviceOptionsInDb as $deviceOption) {
                $currentTrainingDiaryXDeviceOptionsCollection[$deviceOption['device_option_id']] = $deviceOption;
            }

            foreach ($exerciseOptions as $exerciseOption) {
                $exerciseOptionId = $exerciseOption['exerciseOptionId'];
                if (array_key_exists($exerciseOptionId, $currentTrainingDiaryXExerciseOptionsCollection)) {
                    if ($exerciseOption['exerciseOptionValue'] != $currentTrainingDiaryXExerciseOptionsCollection[$exerciseOptionId]['training_diary_x_exercise_option_exercise_option_value']) {
                        $data = [
                            'training_diary_x_exercise_option_exercise_option_value' => $exerciseOption['exerciseOptionValue'],
                            'training_diary_x_exercise_option_update_date' => date('Y-m-d H:i:s'),
                            'training_diary_x_exercise_option_update_user_fk' => $userId,
                        ];
                        $trainingDiaryXExerciseOptionDb->update($data,
                            'training_diary_x_exercise_option_id = ' . $currentTrainingDiaryXExerciseOptionsCollection[$exerciseOptionId]['training_diary_x_exercise_option_id']
                        );
                    }
                    unset($currentTrainingDiaryXExerciseOptionsCollection[$exerciseOptionId]);
                } else {
                    $data = [
                        'training_diary_x_exercise_option_exercise_option_value' => $exerciseOption['exerciseOptionValue'],
                        'training_diary_x_exercise_option_exercise_option_fk' => $exerciseOptionId,
                        'training_diary_x_exercise_option_t_d_x_t_p_e_fk' => $trainingDiaryXTrainingPlanExerciseId,
                        'training_diary_x_exercise_option_create_date' => date('Y-m-d H:i:s'),
                        'training_diary_x_exercise_option_create_user_fk' => $userId,
                    ];
                    $trainingDiaryXExerciseOptionDb->insert($data);
                }
            }

            foreach ($deviceOptions as $deviceOption) {
                $deviceOptionId = $deviceOption['deviceOptionId'];
                if (array_key_exists($deviceOptionId, $currentTrainingDiaryXDeviceOptionsCollection)) {
                    if ($deviceOption['exerciseOptionValue'] != $currentTrainingDiaryXDeviceOptionsCollection[$deviceOptionId]['training_diary_x_device_option_device_option_value']) {
                        $data = [
                            'training_diary_x_device_option_device_option_value' => $deviceOption['deviceOptionValue'],
                            'training_diary_x_device_option_update_date' => date('Y-m-d H:i:s'),
                            'training_diary_x_device_option_update_user_fk' => $userId,
                        ];
                        $trainingDiaryXDeviceOptionDb->update($data,
                            'training_diary_x_device_option_id = ' . $currentTrainingDiaryXDeviceOptionsCollection[$deviceOptionId]['training_diary_x_device_option_id']
                        );
                    }
                    unset($currentTrainingDiaryXDeviceOptionsCollection[$deviceOptionId]);
                } else {
                    $data = [
                        'training_diary_x_device_option_device_option_value' => $deviceOption['deviceOptionValue'],
                        'training_diary_x_device_option_device_option_fk' => $deviceOptionId,
                        'training_diary_x_device_option_t_d_x_t_p_e_fk' => $trainingDiaryXTrainingPlanExerciseId,
                        'training_diary_x_device_option_create_date' => date('Y-m-d H:i:s'),
                        'training_diary_x_device_option_create_user_fk' => $userId,
                    ];
                    $trainingDiaryXDeviceOptionDb->insert($data);
                }
            }

            $data = [
                'training_diary_x_training_plan_exercise_flag_finished' => true,
                'training_diary_x_training_plan_exercise_update_date' => date('Y-m-d H:i:s'),
                'training_diary_x_training_plan_exercise_update_user_fk' => $userId
            ];

            $trainingDiaryXTrainingPlanExerciseDb = new Model_DbTable_TrainingDiaryXTrainingPlanExercise();
            $trainingDiaryXTrainingPlanExerciseDb->update($data, 'training_diary_x_training_plan_exercise_id = ' . $trainingDiaryXTrainingPlanExerciseId);

            $this->considerLastExerciseInTrainingDiary($trainingDiaryXTrainingPlanExerciseId);
        }
    }

    /**
     * finish training diary if last exercise arrived
     *
     * @param $trainingDiaryXTrainingPlanExerciseId
     *
     * @return bool
     */
    private function considerLastExerciseInTrainingDiary($trainingDiaryXTrainingPlanExerciseId)
    {
        $trainingPlanXExerciseDb = new Model_DbTable_TrainingDiaryXTrainingPlanExercise();
        $trainingDiary = $trainingPlanXExerciseDb->checkTrainingDiaryFinished($trainingDiaryXTrainingPlanExerciseId)->toArray();
        $userId = 22;

        if ($trainingDiary['trainingPlanIsFinished']) {
            $trainingDiaryXTrainingPlanDb = new Model_DbTable_TrainingDiaryXTrainingPlan();
            $data = [
                'training_diary_x_training_plan_update_date' => date('Y-m-d H:i:s'),
                'training_diary_x_training_plan_update_user_fk' => $userId,
                'training_diary_x_training_plan_flag_finished' => true,
            ];

            $trainingDiaryXTrainingPlanDb->update($data, 'training_diary_x_training_plan_id = ' . $trainingDiary['training_diary_x_training_plan_id']);
            return true;
        }
        return false;
    }

    public function getExerciseAction() {
        $this->view->layout()->disableLayout();
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $this->_aBeanspruchteMuskeln = array();
            $this->_iMinBeanspruchterMuskel = null;
            $this->_iMaxBeanspruchterMuskel = null;
            $trainingPlanExerciseId = $aParams['id'];
            $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
            $trainingDiaryExercise =
                $trainingPlanXExerciseDb->findTrainingDiaryByTrainingPlanExerciseId($trainingPlanExerciseId);

            if ($trainingDiaryExercise) {
                $trainingDiaryViewGenerator = new Service_Generator_View_TrainingDiaries($this->view);
                $sContent = $trainingDiaryViewGenerator->generateExerciseContent($trainingDiaryExercise);
                $aViewContent = $this->getExerciseInfo($trainingDiaryExercise);

                $aViewContent['content'] = base64_encode($sContent);
                $this->view->assign('sJson', json_encode($aViewContent));
            } else {
                echo "Habe keine Treffer für diese Übung!";
            }
        }
    }

    private function getTrainingPlanInfo($oTrainingstagebuchTrainingsplanRow) {
        $aViewContent = array();
        $aTrainingsplanIds = array();
        $aViewContent['prevTrainingsplanId'] = null;
        $aViewContent['nextTrainingsplanId'] = null;

        if (false === empty($oTrainingstagebuchTrainingsplanRow->training_plan_parent_fk)) {
            $oTrainingsplaeneStorage = new Model_DbTable_TrainingPlans();
            $oChildTrainingsplaene = $oTrainingsplaeneStorage->findChildTrainingPlans($oTrainingstagebuchTrainingsplanRow->training_plan_parent_fk);
            if (false !== $oChildTrainingsplaene) {
                foreach ($oChildTrainingsplaene as $oChildTrainingsplan) {
                    $aTrainingsplanIds[] = $oChildTrainingsplan->training_plan_id;
                }
            }
        }
        $aViewContent['count'] = count($aTrainingsplanIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search($oTrainingstagebuchTrainingsplanRow->training_plan_id,
                $aTrainingsplanIds);
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

    public function getExerciseInfo($oTrainingstagebuchUebungRow) {
        $aViewContent = array();
        $aUebungIds = array();
        $aViewContent['prevTrainingsplanUebungId'] = null;
        $aViewContent['nextTrainingsplanUebungId'] = null;

        if (false === empty($oTrainingstagebuchUebungRow->training_plan_id)) {
            $oTrainingsplaeneStorage = new Model_DbTable_TrainingPlanXExercise();
            $oUebungen = $oTrainingsplaeneStorage->findExercisesByTrainingPlanId($oTrainingstagebuchUebungRow->training_plan_id);
            if (false !== $oUebungen) {
                foreach ($oUebungen as $oUebung) {
                    $aUebungIds[] = $oUebung->training_plan_x_exercise_id;
                }
            }
        }
        $aViewContent['count'] = count($aUebungIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search($oTrainingstagebuchUebungRow->training_plan_x_exercise_id,
                $aUebungIds);
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
     * @param \Zend_Db_Table_Row_Abstract $oTrainingstagebuchTrainingsplanRow
     *
     * @return string
     */
    public function generateViewForTrainingsplan($oTrainingstagebuchTrainingsplanRow) {
        $this->view->assign($oTrainingstagebuchTrainingsplanRow->toArray());

        return $this->view->render('training-diaries/partials/training-diary-training-plan-partial.phtml');
    }
}

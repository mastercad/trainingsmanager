<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 26.04.14
 * Time: 16:58
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

require_once APPLICATION_PATH . '/controllers/AbstractController.php';

use Model\DbTable\TrainingPlans;
use Model\DbTable\TrainingDiaryXTrainingPlan;
use Model\DbTable\TrainingDiaryXTrainingPlanExercise;
use Model\DbTable\TrainingDiaries;
use Model\DbTable\ExerciseXMuscle;
use Model\DbTable\TrainingDiaryXExerciseOption;
use Model\DbTable\TrainingDiaryXDeviceOption;
use Service\Generator\View\TrainingDiaries as TrainingDiariesViewGenerator;
use Service\GlobalMessageHandler;
use Model\Entity\Message;
use Model\DbTable\TrainingPlanXExercise;

/**
 * Class TrainingDiariesController
 */
class TrainingDiariesController extends AbstractController
{
    protected $_iMinBeanspruchterMuskel = null;
    protected $_iMaxBeanspruchterMuskel = null;
    protected $_aBeanspruchteMuskeln = array();

    /**
     * initial function for controller
     */
    public function init() 
    {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile(
                $this->view->baseUrl() . '/js/trainingsmanager_training_plan_accordion.js',
                'text/javascript'
            );
            $this->view->headScript()->appendFile(
                $this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript'
            );
        }
    }

    /**
     * index action
     */
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
        $iUserId = $this->findCurrentUserId();

        if (array_key_exists('id', $aParams)) {
            $trainingPlanId = $aParams['id'];
            $oTrainingsplaeneStorage = new TrainingPlans();

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
                    $trainingDiaryXTrainingPlanStorage = new TrainingDiaryXTrainingPlan();

                    // checken ob ein anderer alter training-plans offen ist
                    /**
 * @todo ausformulieren 
*/

                    // checken ob der training-plans fortgesetzt werden soll oder neu angelegt
                    $oAktuellesTrainingstagebuch =
                        $trainingDiaryXTrainingPlanStorage->findLastOpenTrainingPlanByTrainingPlanIdAndUserId($trainingPlanId, $iUserId);

                    // keine tagebucheinträge vorhanden
                    if (! $oAktuellesTrainingstagebuch->count()) {
                        echo "Habe noch keinen Trainingstagebucheintrag für diesen Trainingsplan, der aber offen ist!";

                        $trainingDiariesDb = new TrainingDiaries();
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
                            $trainingDiaryId,
                            $trainingDiaryXTrainingPlanId
                        );

                        $oAktuellesTrainingstagebuch = $trainingDiaryXTrainingPlanStorage->findLastOpenTrainingPlanByTrainingPlanIdAndUserId($trainingPlanId, $iUserId);
                        $this->redirect(
                            '/training-diaries/show-exercise/id/' . $oAktuellesTrainingstagebuch->current()->training_diary_x_training_plan_id, $aParams
                        );

                        // habe einen offenen training-plans gefunden, leite an die übersicht der übungen weiter
                    } elseif (1 <= $oAktuellesTrainingstagebuch->count()) {
                        $this->redirect(
                            '/training-diaries/show-exercise/id/' . $oAktuellesTrainingstagebuch->current()->training_diary_x_training_plan_id,
                            $aParams
                        );
                    } else {
                        echo "Nichts getan in start training-plans! o.O";
                    }
                }
            }
        }
    }

    /**
     * create training diary exercise entry
     *
     * @param int $trainingPlanXExerciseId
     * @param int $trainingDiaryId
     * @param int $trainingDiaryXTrainingPlanId
     *
     * @return mixed
     */
    private function createTrainingDiaryExerciseEntry($trainingPlanXExerciseId, $trainingDiaryId, $trainingDiaryXTrainingPlanId)
    {
        $iUserId = $this->findCurrentUserId();

        $trainingDiaryXTrainingPlanExerciseDb = new TrainingDiaryXTrainingPlanExercise();

        $data = [
            'training_diary_x_training_plan_exercise_t_p_x_e_fk' => $trainingPlanXExerciseId,
            'training_diary_x_training_plan_exercise_t_d_x_t_p_fk' => $trainingDiaryXTrainingPlanId,
            'training_diary_x_training_plan_exercise_training_diary_fk' => $trainingDiaryId,
            'training_diary_x_training_plan_exercise_create_date' => date('Y-m-d H:i:s'),
            'training_diary_x_training_plan_exercise_create_user_fk' => $iUserId
        ];
        return $trainingDiaryXTrainingPlanExerciseDb->insert($data);
    }

    /**
     * show action
     */
    public function showAction() 
    {
        $this->view->headScript()->appendFile(
            $this->view->baseUrl() . '/js/jquery.touchSwipe.min.js',
            'text/javascript'
        );
        $aParams = $this->getAllParams();

        if (array_key_exists('id', $aParams)
            && is_numeric($aParams['id'])
            && 0 < $aParams['id']
        ) {
            $trainingPlanId = $aParams['id'];
            $trainingDiaryXTrainingPlanDb = new TrainingDiaryXTrainingPlan();
            $trainingDiaryExerciseCollection = $trainingDiaryXTrainingPlanDb->findLastOpenTrainingPlanByTrainingPlanIdAndUserId(
                $trainingPlanId, $this->findCurrentUserId()
            );

            if (0 < count($trainingDiaryExerciseCollection)) {
                $trainingsExercise = null;
                $sContent = '';
                $exerciseXMuscleDb = new ExerciseXMuscle();
                $aBeanspruchteMuskeln = array();
                $iMinBeanspruchterMuskel = null;
                $iMaxBeanspruchterMuskel = null;

                foreach ($trainingDiaryExerciseCollection as $trainingsExercise) {
                    if (false === empty($oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk)) {
                        $oBeanspruchteMuskelnFuerUebung = $exerciseXMuscleDb->findMusclesForExercise(
                            $oTrainingstagebuchTrainingsplanRow->trainingsplan_uebung_fk
                        );

                        foreach ($oBeanspruchteMuskelnFuerUebung as $oBeanspruchterMuskelFuerUebung) {
                            if (false == array_key_exists(
                                $oBeanspruchterMuskelFuerUebung->muskel_name,
                                $aBeanspruchteMuskeln
                            )
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
                        $sContent .= $this->generateViewForTrainingPlan($oTrainingstagebuchTrainingsplanRow);
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

    /**
     * show exercise action
     */
    public function showExerciseAction() 
    {

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery.touchSwipe.min.js', 'text/javascript');
        $trainingDiaryXTrainingPlanId = $this->getParam('id');

        if (0 < $trainingDiaryXTrainingPlanId) {
            $this->_aBeanspruchteMuskeln = array();
            $this->_iMinBeanspruchterMuskel = null;
            $this->_iMaxBeanspruchterMuskel = null;
            $trainingPlanExerciseId = $aParams['id'];

            $trainingPlanXExerciseDb = new TrainingDiaryXTrainingPlan();
            $trainingPlanXExerciseCollection = $trainingPlanXExerciseDb->findTrainingDiaryExercisesByTrainingDiaryXTrainingPlanId($trainingDiaryXTrainingPlanId);

            $exercisesContent = '';
            $count = 0;
            $countFinished = 0;

            if (0 < count($trainingPlanXExerciseCollection)) {
                foreach ($trainingPlanXExerciseCollection as $trainingPlanXExercise) {
                    $trainingDiaryViewGenerator = new TrainingDiariesViewGenerator($this->view);
                    $trainingDiaryViewGenerator->setExercisesCount(count($trainingPlanXExerciseCollection));
                    $exercisesContent .= $trainingDiaryViewGenerator->generateExerciseContent($trainingPlanXExercise);
                    $aViewContent = $this->getExerciseInfo($trainingPlanXExercise);
                    $this->view->assign('trainingDiaryId', $trainingPlanXExercise->offsetGet('training_diary_x_training_plan_training_diary_fk'));
                    $this->view->assign('trainingPlanExerciseId', $trainingPlanExerciseId);
                    $this->view->assign('prevTrainingPlanExerciseId', $aViewContent['prevTrainingPlanExerciseId']);
                    $this->view->assign('nextTrainingPlanExerciseId', $aViewContent['nextTrainingPlanExerciseId']);
                    $this->view->assign('iActualPos', $aViewContent['actualPos']);

                    if (1 == $trainingPlanXExercise->offsetGet('training_diary_x_training_plan_exercise_flag_finished')) {
                        ++$countFinished;
                    }
                    ++$count;

                }
            } else {
                $exercisesContent = "Habe keine Treffer für diese Übung!";
            }

            if ($count != $countFinished) {
                $this->view->assign('exerciseContent', $exercisesContent);
            }
        }
    }

    /**
     * edit action
     */
    public function editAction() 
    {
        // hier kann man die übung bearbeiten, vielleicht ist das unnötig, oder man hat hier die ansicht einer
        // übung des aktuellen trainingsplanes drin
    }

    /**
     * save action
     */
    public function saveAction() 
    {

        if (0 < $this->getParam('trainingDiaryExerciseInformation')) {
            $hasErrors = false;
            $userId = $this->findCurrentUserId();

            $trainingPlanDiaryExerciseInformation = $this->getParam('trainingDiaryExerciseInformation');
            $trainingDiaryXTrainingPlanExerciseId = $trainingPlanDiaryExerciseInformation['trainingDiaryXTrainingPlanExerciseId'];
            $trainingDiaryXTrainingPlanId = $trainingPlanDiaryExerciseInformation['trainingDiaryXTrainingPlanId'];

            if (empty($trainingDiaryXTrainingPlanExerciseId)) {
                $trainingPlanXExerciseId = $trainingPlanDiaryExerciseInformation['trainingPlanXExerciseId'];
                $trainingDiaryId = $trainingPlanDiaryExerciseInformation['trainingDiaryId'];
                $trainingDiaryXTrainingPlanExerciseId = $this->createTrainingDiaryExerciseEntry($trainingPlanXExerciseId, $trainingDiaryId, $trainingDiaryXTrainingPlanId);
            }

            $exerciseOptions = $trainingPlanDiaryExerciseInformation['exerciseOptions'];
            $trainingDiaryXExerciseOptionDb = new TrainingDiaryXExerciseOption();
            $currentExerciseOptionsInDb = $trainingDiaryXExerciseOptionDb->findExerciseOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryXTrainingPlanExerciseId);
            $currentTrainingDiaryXExerciseOptionsCollection = [];

            foreach ($currentExerciseOptionsInDb as $exerciseOption) {
                $currentTrainingDiaryXExerciseOptionsCollection[$exerciseOption['exercise_option_id']] = $exerciseOption;
            }

            $deviceOptions = $trainingPlanDiaryExerciseInformation['deviceOptions'];
            $trainingDiaryXDeviceOptionDb = new TrainingDiaryXDeviceOption();
            $currentDeviceOptionsInDb = $trainingDiaryXDeviceOptionDb->findDeviceOptionsByTrainingDiaryTrainingPlanExerciseId($trainingDiaryXTrainingPlanExerciseId);
            $currentTrainingDiaryXDeviceOptionsCollection = [];

            foreach ($currentDeviceOptionsInDb as $deviceOption) {
                $currentTrainingDiaryXDeviceOptionsCollection[$deviceOption['device_option_id']] = $deviceOption;
            }

            if ($exerciseOptions) {
                foreach ($exerciseOptions as $exerciseOption) {
                    $exerciseOptionValue = $exerciseOption['exerciseOptionValue'];
                    $exerciseOptionId = $exerciseOption['exerciseOptionId'];

                    if (empty($exerciseOptionValue)) {
                        GlobalMessageHandler::appendMessage('Option not allowed to be empty!', Message::STATUS_ERROR);
                        $hasErrors = true;
                    } else {
                        if (array_key_exists($exerciseOptionId, $currentTrainingDiaryXExerciseOptionsCollection)) {
                            if ($exerciseOption['exerciseOptionValue'] != $currentTrainingDiaryXExerciseOptionsCollection[$exerciseOptionId]['training_diary_x_exercise_option_exercise_option_value']) {
                                $data = [
                                    'training_diary_x_exercise_option_exercise_option_value' => $exerciseOption['exerciseOptionValue'],
                                    'training_diary_x_exercise_option_update_date' => date('Y-m-d H:i:s'),
                                    'training_diary_x_exercise_option_update_user_fk' => $userId,
                                ];
                                $trainingDiaryXExerciseOptionDb->update(
                                    $data,
                                    'training_diary_x_exercise_option_id = ' . $currentTrainingDiaryXExerciseOptionsCollection[$exerciseOptionId]['training_diary_x_exercise_option_id']
                                );
                            }
                            unset($currentTrainingDiaryXExerciseOptionsCollection[$exerciseOptionId]);
                        } else {
                            $data = [
                                'training_diary_x_exercise_option_exercise_option_value' => $exerciseOptionValue,
                                'training_diary_x_exercise_option_exercise_option_fk' => $exerciseOptionId,
                                'training_diary_x_exercise_option_t_d_x_t_p_e_fk' => $trainingDiaryXTrainingPlanExerciseId,
                                'training_diary_x_exercise_option_create_date' => date('Y-m-d H:i:s'),
                                'training_diary_x_exercise_option_create_user_fk' => $userId,
                            ];
                            $trainingDiaryXExerciseOptionDb->insert($data);
                        }
                    }
                }
            }

            if ($deviceOptions) {
                foreach ($deviceOptions as $deviceOption) {
                    $deviceOptionId = $deviceOption['deviceOptionId'];
                    $deviceOptionValue = $deviceOption['deviceOptionValue'];
                    if (empty($deviceOptionValue)) {
                        GlobalMessageHandler::appendMessage('Option not allowed to be empty!', Message::STATUS_ERROR);
                        $hasErrors = true;
                    } else {
                        if (array_key_exists($deviceOptionId, $currentTrainingDiaryXDeviceOptionsCollection)) {
                            if ($deviceOption['exerciseOptionValue'] != $currentTrainingDiaryXDeviceOptionsCollection[$deviceOptionId]['training_diary_x_device_option_device_option_value']) {
                                $data = [
                                    'training_diary_x_device_option_device_option_value' => $deviceOptionValue,
                                    'training_diary_x_device_option_update_date' => date('Y-m-d H:i:s'),
                                    'training_diary_x_device_option_update_user_fk' => $userId,
                                ];
                                $trainingDiaryXDeviceOptionDb->update(
                                    $data,
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
                }
            }

            $data = [
                'training_diary_x_training_plan_exercise_flag_finished' => true,
                'training_diary_x_training_plan_exercise_update_date' => date('Y-m-d H:i:s'),
                'training_diary_x_training_plan_exercise_update_user_fk' => $userId
            ];

            $trainingDiaryXTrainingPlanExerciseDb = new TrainingDiaryXTrainingPlanExercise();
            $trainingDiaryXTrainingPlanExerciseDb->update($data, 'training_diary_x_training_plan_exercise_id = ' . $trainingDiaryXTrainingPlanExerciseId);

            if (false === $hasErrors) {
                GlobalMessageHandler::appendMessage('Übung erfolgreich beendet!', Message::STATUS_OK);
            }

            if ($this->considerLastExerciseInTrainingDiary($trainingDiaryXTrainingPlanExerciseId)) {
                $this->view->assign('content', $this->view->render('training-diaries/all-exercises-finished.phtml'));
            }
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
        $trainingPlanXExerciseDb = new TrainingDiaryXTrainingPlanExercise();
        $trainingDiary = $trainingPlanXExerciseDb->checkTrainingDiaryFinished($trainingDiaryXTrainingPlanExerciseId)->toArray();
        $userId = $this->findCurrentUserId();

        if ($trainingDiary['trainingPlanIsFinished']) {
            $trainingDiaryXTrainingPlanDb = new TrainingDiaryXTrainingPlan();
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

    /**
     * get exercise action
     */
    public function getExerciseAction() 
    {
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
            $trainingPlanXExerciseDb = new TrainingPlanXExercise();
            $trainingDiaryExercise =
                $trainingPlanXExerciseDb->findTrainingDiaryByTrainingPlanExerciseId($trainingPlanExerciseId);

            if ($trainingDiaryExercise) {
                $trainingDiaryViewGenerator = new TrainingDiariesViewGenerator($this->view);
                $sContent = $trainingDiaryViewGenerator->generateExerciseContent($trainingDiaryExercise);
                $aViewContent = $this->getExerciseInfo($trainingDiaryExercise);

                $aViewContent['content'] = base64_encode($sContent);
                $this->view->assign('sJson', json_encode($aViewContent));
            } else {
                echo "Habe keine Treffer für diese Übung!";
            }
        }
    }

    /**
     * get info from training plan by training plan db table row
     *
     * @param $oTrainingstagebuchTrainingsplanRow
     *
     * @return array
     */
    private function getTrainingPlanInfo($oTrainingstagebuchTrainingsplanRow) 
    {
        $aViewContent = array();
        $aTrainingsplanIds = array();
        $aViewContent['prevTrainingsplanId'] = null;
        $aViewContent['nextTrainingsplanId'] = null;

        if (false === empty($oTrainingstagebuchTrainingsplanRow->training_plan_parent_fk)) {
            $oTrainingsplaeneStorage = new TrainingPlans();
            $oChildTrainingsplaene = $oTrainingsplaeneStorage->findChildTrainingPlans($oTrainingstagebuchTrainingsplanRow->training_plan_parent_fk);
            if (false !== $oChildTrainingsplaene) {
                foreach ($oChildTrainingsplaene as $oChildTrainingsplan) {
                    $aTrainingsplanIds[] = $oChildTrainingsplan->training_plan_id;
                }
            }
        }
        $aViewContent['count'] = count($aTrainingsplanIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search(
                $oTrainingstagebuchTrainingsplanRow->training_plan_id,
                $aTrainingsplanIds
            );
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

    /**
     * get exercise info by training plan exercise row
     *
     * @param Zend_Db_Table_Row_Abstract $trainingPlanXExerciseRow
     *
     * @return array
     */
    public function getExerciseInfo($trainingPlanXExerciseRow) 
    {
        $aViewContent = array();
        $exerciseIds = array();
        $aViewContent['prevTrainingsplanUebungId'] = null;
        $aViewContent['nextTrainingsplanUebungId'] = null;

        if (false === empty($trainingPlanXExerciseRow->training_plan_id)) {
            $trainingPlansDb = new TrainingPlanXExercise();
            $exercises = $trainingPlansDb->findExercisesByTrainingPlanId($trainingPlanXExerciseRow->training_plan_id);
            if (false !== $exercises) {
                foreach ($exercises as $exercise) {
                    $exerciseIds[] = $exercise->training_plan_x_exercise_id;
                }
            }
        }
        $aViewContent['count'] = count($exerciseIds);
        if (0 < $aViewContent['count']) {
            $aViewContent['actualPos'] = array_search(
                $trainingPlanXExerciseRow->training_plan_x_exercise_id,
                $exerciseIds
            );
            if (0 < $aViewContent['actualPos']) {
                $aViewContent['prevTrainingsplanUebungId'] = $exerciseIds[$aViewContent['actualPos'] - 1];
            }
            if ($aViewContent['actualPos'] < ($aViewContent['count'] - 1)) {
                $aViewContent['nextTrainingsplanUebungId'] = $exerciseIds[$aViewContent['actualPos'] + 1];
            }
        }
        $aViewContent['trainingsplanUebungId'] = $trainingPlanXExerciseRow->training_plan_x_exercise_id;

        return $aViewContent;
    }

    /**
     * generate view for training plan
     *
     * @param \Zend_Db_Table_Row_Abstract $trainingDiaryXTrainingPlanRow
     *
     * @return string
     */
    private function generateViewForTrainingPlan($trainingDiaryXTrainingPlanRow) 
    {
        $this->view->assign($trainingDiaryXTrainingPlanRow->toArray());

        return $this->view->render('training-diaries/partials/training-diary-training-plan-partial.phtml');
    }
}

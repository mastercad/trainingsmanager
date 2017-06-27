<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

use Model\DbTable\Dashboards;
use Model\DbTable\DashboardXWidget;
use Model\DbTable\Exercises;
use Model\DbTable\Widgets;
use Model\DbTable\TrainingDiaryXTrainingPlanExercise;
use Service\GlobalMessageHandler;
use Model\Entity\Message;
use Service\TrainingPlan as TrainingPlanService;
use Model\DbTable\TrainingDiaryXTrainingPlan;
use Model\DbTable\TrainingDiaryXExerciseOption;
use Model\DbTable\TrainingDiaryXDeviceOption;
use Model\DbTable\TrainingPlanXExercise;

/**
 * Class IndexController
 */
class IndexController extends AbstractController {

    /**
     * initial function for controller
     */
    public function init() {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_accordion.js',
                'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript');
        }
    }

    /**
     * index action
     */
    public function indexAction() {

        $user = Zend_Auth::getInstance()->getIdentity();
        if ('guest' == $user->user_right_group_name) {
            $this->forward('welcome-content');
        } else {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/c3.min.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/d3.min.js', 'text/javascript');
            $this->view->headLink()->prependStylesheet($this->view->baseUrl() . '/css/c3.min.css', 'screen', true);

            $this->view->assign('widgetsContent', $this->generateWidgetsContent());
        }
    }

    /**
     * generate widgets content
     *
     * @return string
     */
    private function generateWidgetsContent() {

        $dashboardsDb = new Dashboards();
        $dashboardXWidgetDb = new DashboardXWidget();
        $dashboard = $dashboardsDb->findActiveDashboardByUserId($this->findCurrentUserId());
        $widgetContent = '';

        if ($dashboard instanceof Zend_Db_Table_Row_Abstract) {
            $widgetsInDashboard = $dashboardXWidgetDb->findAllWidgetsByDashboardId($dashboard->offsetGet('dashboard_id'));
            foreach ($widgetsInDashboard as $widget) {
                $widgetId = $widget->offsetGet('widget_id');
                $this->view->assign($widget->toArray());
                $this->view->assign('dashboardXWidgetId', $widget->offsetGet('dashboard_x_widget_id'));

                if (1 == $widgetId) {
                    $this->view->assign('activeTrainingDiary', $this->generateActiveTrainingDiaryContent());
                    $widgetContent .= $this->view->render('loops/widgets/current-training-plan.phtml');
                } else if (2 == $widgetId) {
                    $type = $widget->offsetGet('dashboard_x_widget_widget_type');
                    $exerciseId = null;

                    if ('ALL' !== strtoupper($type)) {
                        $exercisesDb = new Exercises();
                        $exercises = $exercisesDb->findExercisesByName($type);
                        if ($exercises instanceof Zend_Db_Table_Rowset_Abstract
                            && 0 < count($exercises)
                        ) {
                            $exerciseId = $exercises->current()->offsetGet('exercise_id');
                        }
                    }
                    $widgetContent .= $this->generateChartsContent($exerciseId);
                }
            }
        }
        return $widgetContent;
    }

    /**
     * welcome content action
     */
    public function welcomeContentAction() {

    }

    /**
     * delete widget action
     */
    public function deleteWidgetAction() {
        $id = intval($this->getParam('id'));

        if (0 < $id) {
            $dashboardXWidgetDb = new DashboardXWidget();
            $dashboardXWidgetDb->deleteDashboard($id);
        }
    }

    /**
     * get widget settings content action
     */
    public function getWidgetSettingsContentAction() {
        $dashboardsDb = new Dashboards();
        $widgetsDb = new Widgets();
        $dashboardXWidgetDb = new DashboardXWidget();
        $widgetsInDashboard = [];
        $widgetsContent = '';

        $dashboard = $dashboardsDb->findActiveDashboardByUserId($this->findCurrentUserId());
        if ($dashboard instanceof Zend_Db_Table_Row_Abstract) {
            $widgetsInDashboard = $this->collectWidgets($dashboardXWidgetDb->findAllWidgetsByDashboardId($dashboard->offsetGet('dashboard_id')));
        }
        $availableWidgets = $this->collectWidgets($widgetsDb->findAllWidgets());

        foreach ($availableWidgets as $widget) {
            $this->view->assign($widget->toArray());
            if (array_key_exists($widget->offsetGet('widget_name'), $widgetsInDashboard)
                && !$widget->offsetGet('widget_editable')
            ) {
                $this->view->assign('widgetInUse', true);
            } else {
                $this->view->assign('widgetInUse', false);
            }
            $widgetsContent .= $this->view->render('loops/widget-setting.phtml');
        }
        $this->view->assign('widgetsContent', $widgetsContent);
    }

    /**
     * load widget edit content action
     */
    public function loadWidgetEditContentAction() {
        $id = intval($this->getParam('id'));
        $content = '';

        if (0 < $id) {
            $widgetsDb = new Widgets();
            $widget = $widgetsDb->findByPrimary($id);

            if ('ÜBUNGSFORTSCHRITT' == strtoupper($widget->offsetGet('widget_name'))) {
                $dashboardsDb = new Dashboards();
                $dashboardXWidgetDb = new DashboardXWidget();
                $dashboard = $dashboardsDb->findActiveDashboardByUserId($this->findCurrentUserId());
                $currentEnabledWidgets = [];
                $allSelected = false;

                if ($dashboard instanceof Zend_Db_Table_Row_Abstract) {
                    $widgetsInDashboard = $dashboardXWidgetDb->findAllWidgetsByDashboardId($dashboard->offsetGet('dashboard_id'));
                    foreach ($widgetsInDashboard as $widget) {
                        $currentEnabledWidgets[$widget->offsetGet('widget_id')][$widget->offsetGet('dashboard_x_widget_widget_type')]
                            = $widget->offsetGet('dashboard_x_widget_id');

                        if ('ALL' == strtoupper($widget->offsetGet('dashboard_x_widget_widget_type'))) {
                            $allSelected = true;
                        }
                    }
                }

                $trainingDiaryXTrainingPlanExerciseDb = new TrainingDiaryXTrainingPlanExercise();
                $trainingDiaryXTrainingPlanExercises = $trainingDiaryXTrainingPlanExerciseDb->findTrainingDiaryTrainingPlanExercises($this->findCurrentUserId());

                if (!$allSelected
                    && count($currentEnabledWidgets[2]) < $trainingDiaryXTrainingPlanExercises->count()
                ) {
                    $this->view->assign('optionValue', base64_encode('ALL'));
                    $this->view->assign('optionText', 'Alle');
                    $exercisesOptionsContent = $this->view->render('loops/option.phtml');

                    foreach ($trainingDiaryXTrainingPlanExercises as $exercise) {
                        $exerciseName = $exercise->offsetGet('exercise_name');
                        if (!array_key_exists($exerciseName, $currentEnabledWidgets[2])) {
                            $this->view->assign('optionValue', base64_encode($exerciseName));
                            $this->view->assign('optionText', $exerciseName);
                            $exercisesOptionsContent .= $this->view->render('loops/option.phtml');
                        }
                    }
                    $this->view->assign($widget->toArray());

                    $this->view->assign('widgetId', $id);
                    $this->view->assign('optionLabelText', $this->translate('label_please_select'));
                    $this->view->assign('optionClassName', 'widget-exercise-select custom-drop-down');
                    $this->view->assign('optionsContent', $exercisesOptionsContent);
                    $exerciseOptionsDropDownContent = $this->view->render('globals/select.phtml');
                    $this->view->assign('exerciseOptionsDropDownContent', $exerciseOptionsDropDownContent);
                    $content = $this->view->render('loops/widget-progress-edit.phtml');
                } else {
                    GlobalMessageHandler::appendMessage('Es sind bereits alle verfügbaren Typen dieses Widgets auf dem Dashboard aktiviert!', Message::STATUS_ERROR);
                }
            }
        }
        $this->view->assign('content', $content);
    }

    /**
     * load widget content action
     */
    public function loadWidgetContentAction() {
        $widgetId = intval($this->getParam('id'));
        $content = '';

        if (1 == $widgetId) {
            $content = $this->generateActiveTrainingDiaryContent();
            $this->registerWidget($widgetId);
        } else if (2 == $widgetId) {
            $type = $this->getParam('type');
            $exerciseId = null;

            if (!empty($type)) {
                $type = base64_decode($type);
                if ('ALL' !== strtoupper($type)) {
                    $exercisesDb = new Exercises();
                    $exerciseId = $exercisesDb->findExercisesByName($type)->current()->offsetGet('exercise_id');
                }
            }
            $widgetsDb = new Widgets();
            $widget = $widgetsDb->findByPrimary($widgetId);
            $this->view->assign($widget->toArray());

            $this->view->assign('dashboardXWidgetId', $this->registerWidget($widgetId, $type));
            $content = $this->generateChartsContent($exerciseId);
        }

        $this->view->assign('content', $content);
    }

    /**
     * register widget
     *
     * @param      $widgetId
     * @param null $type
     *
     * @return mixed
     */
    private function registerWidget($widgetId, $type = null) {
        $dashboardsDb = new Dashboards();
        $dashboard = $dashboardsDb->findActiveDashboardByUserId($this->findCurrentUserId());
        $dashboardId = null;

        if (!($dashboard instanceof Zend_Db_Table_Row_Abstract)) {
            $data = [
                'dashboard_name' => 'New Dashboard',
                'dashboard_user_fk' => $this->findCurrentUserId(),
                'dashboard_flag_active' => 1,
                'dashboard_create_date' => date('Y-m-d H:i:s'),
                'dashboard_create_user_fk' => $this->findCurrentUserId()
            ];
            $dashboardId = $dashboardsDb->insert($data);
        } else {
            $dashboardId = $dashboard->offsetGet('dashboard_id');
        }

        $order = intval($this->getParam('order', 0));
        $dashboardXWidgetDb = new DashboardXWidget();
        $data = [
            'dashboard_x_widget_widget_fk' => $widgetId,
            'dashboard_x_widget_dashboard_fk' => $dashboardId,
            'dashboard_x_widget_create_date' => date('Y-m-d H:i:s'),
            'dashboard_x_widget_create_user_fk' => $this->findCurrentUserId(),
            'dashboard_x_widget_order' => $order
        ];

        if (! is_null($type)) {
            $data['dashboard_x_widget_widget_type'] = $type;
        }
        return $dashboardXWidgetDb->insert($data);
    }

    /**
     * collect widgets, given from db result
     *
     * @param $widgetsInDb
     *
     * @return array
     */
    private function collectWidgets($widgetsInDb) {
        $widgetsCollection = [];

        if ($widgetsInDb instanceof Zend_Db_Table_Rowset_Abstract) {
            foreach ($widgetsInDb as $widget) {
                $widgetsCollection[$widget->offsetGet('widget_name')] = $widget;
            }
        }

        return $widgetsCollection;
    }

    /**
     * generate active training diary content
     *
     * @return string
     */
    private function generateActiveTrainingDiaryContent() {

        $content = '';
        $trainingPlanService = new TrainingPlanService();
        $currentTrainingPlan = $trainingPlanService->searchCurrentTrainingPlan($this->findCurrentUserId());

        if ($currentTrainingPlan instanceof Zend_Db_Table_Row_Abstract) {
            $content = $this->generateCurrentTrainingPlanContent($currentTrainingPlan);
        }

        return $content;
    }

    /**
     * generate current training plan content
     *
     * @param $trainingPlanRow
     *
     * @return string
     */
    private function generateCurrentTrainingPlanContent($trainingPlanRow) {
        $exercisesContent = '';
        $trainingPlanId = $trainingPlanRow->offsetGet('training_plan_id');
        $trainingDiaryXTrainingPlanId = null;
        $exercisesCollection = [];
        // trainingPlan is not finished yet
        if ($trainingPlanRow->offsetExists('training_diary_x_training_plan_id')
            && 0 < $trainingPlanRow->offsetGet('training_diary_x_training_plan_id')
        ) {
            $trainingDiaryXTrainingPlanExercise = new TrainingDiaryXTrainingPlan();
            $exercisesCollection = $trainingDiaryXTrainingPlanExercise->findTrainingDiaryExercisesByTrainingDiaryXTrainingPlanId($trainingPlanRow->offsetGet('training_diary_x_training_plan_id'));
        } else {
            $trainingPlanXExerciseDb = new TrainingPlanXExercise();
            $exercisesCollection = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlanRow->offsetGet('training_plan_id'));
        }

        foreach ($exercisesCollection as $exercise) {
            $this->view->assign($exercise->toArray());
            $exercisesContent .= $this->view->render('loops/current-training-plan-exercise-row.phtml');
        }

        $this->view->assign('exercisesContent', $exercisesContent);
        return $this->view->render('index/current-training-plan.phtml');
    }

    /**
     * generate charts content
     *
     * @param null $exerciseId
     *
     * @return string
     */
    private function generateChartsContent($exerciseId = null) {
        $chartDataCollection = $this->collectDataForExerciseChart($exerciseId);

        $chartContent = '';

        foreach ($chartDataCollection as $exerciseName => $chartData) {
            $chartContent .= $this->generateChartContent($exerciseName, $chartData);
        }
        return $chartContent;
    }

    /**
     * collect data for exercise chart
     *
     * @param $exerciseId
     *
     * @return array
     */
    private function collectDataForExerciseChart($exerciseId) {
        $trainingDiaryExerciseOptionDb = new TrainingDiaryXExerciseOption();
        $trainingDiaryDeviceOptionDb = new TrainingDiaryXDeviceOption();

        $exerciseOptionCollection = $trainingDiaryExerciseOptionDb->findAllExerciseOptions($this->findCurrentUserId(), $exerciseId);
        $deviceOptionCollection = $trainingDiaryDeviceOptionDb->findAllDeviceOptions($this->findCurrentUserId(), $exerciseId);

        $data = [];

        foreach ($exerciseOptionCollection as $exerciseOption) {
            $date = date('Y-m-d', strtotime($exerciseOption->offsetGet('training_diary_x_exercise_option_create_date')));
            $optionName = $exerciseOption->offsetGet('exercise_option_name');
            $exerciseName = $exerciseOption->offsetGet('exercise_name');

            if (!array_key_exists($exerciseName, $data)) {
                $data[$exerciseName] = [];
            }
            if (!array_key_exists($date, $data[$exerciseName])) {
                $data[$exerciseName][$date] = [];
            }
            $data[$exerciseName][$date][$optionName] = [
                'value' => $exerciseOption->offsetGet('training_diary_x_exercise_option_exercise_option_value'),
                'preset' => $exerciseOption->offsetGet('training_plan_x_exercise_option_exercise_option_value'),
            ];
        }

        foreach ($deviceOptionCollection as $deviceOption) {
            $date = date('Y-m-d', strtotime($deviceOption->offsetGet('training_diary_x_device_option_create_date')));
            $optionName = $deviceOption->offsetGet('device_option_name');
            $exerciseName = $deviceOption->offsetGet('exercise_name');

            if (!array_key_exists($exerciseName, $data)) {
                $data[$exerciseName] = [];
            }
            if (!array_key_exists($date, $data[$exerciseName])) {
                $data[$exerciseName][$date] = [];
            }
            $data[$exerciseName][$date][$optionName] = [
                'value' => $deviceOption->offsetGet('training_diary_x_device_option_device_option_value'),
                'preset' => $deviceOption->offsetGet('training_plan_x_device_option_device_option_value'),
            ];
        }

        return $data;
    }

    /**
     * generate chart content
     *
     * @param $exerciseName
     * @param $chartData
     *
     * @return string
     */
    private function generateChartContent($exerciseName, $chartData) {
        $chartCount = uniqid();
        $columns = [];
        // bring values in one row per option
        foreach ($chartData as $date => $options) {
            $columns['x'][] = $date;
            foreach ($options as $optionName => $values) {
                $columns[$optionName][] = $values['value'];
                $columns[$optionName.' Preset'][] = $values['preset'];
            }
        }

        $cleanColumns = [];
        $rowCount = 0;
        // hier müssen noch alle spalten gesammelt werden auf der X achse, wenn beim nächsten step das datum
        // nicht überein stimmt, muss das feld leer gelassen werden, sonst sind die anzahl der spalten zu den
        // inhalten inkonsistent
        foreach ($columns as $columnName => $rowData) {
            $header = true;
            $cleanColumns[$rowCount] = [];
            foreach ($rowData as $value) {
                if ($header) {
                    $cleanColumns[$rowCount][] = $columnName;
                    $header = false;
                }
                $cleanColumns[$rowCount][] = $value;
            }
            ++$rowCount;
        }

        $jsonData = [
            'bindto' => '#chart' . $chartCount,
            'data' => [
                'x' => 'x',
//                'xFormat' => '%Y-%m-%d %H:%M:%S',
                'xFormat' => '%Y-%m-%d',
                'columns' => $cleanColumns,
            ],
            'axis' => [
                'x' => [
                    'type' => 'timeseries',
                    'tick' => [
//                        'format' => '%Y-%m-%d %H:%M:%S',
                        'format' => '%Y-%m-%d',
                    ]
                ]
            ],
            'tooltip' => [
                'enabled' => false
            ],
            'zoom' => [
                'enabled' => true
            ],
            'subchart' => [
                'show' => false
            ]
        ];

        $this->view->assign('jsonString', json_encode($jsonData));
        $this->view->assign('exerciseName', $exerciseName);
        $this->view->assign('chartCount', $chartCount);

        return $this->view->render('loops/chart.phtml');
    }
}

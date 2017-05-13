<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class IndexController extends AbstractController {

    public function init() {
        if (!$this->getParam('ajax')) {
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_accordion.js',
                'text/javascript');
            $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/trainingsmanager_messages.js',
                'text/javascript');
        }
    }

    public function indexAction() {
        $this->view->assign('chartContent', $this->generateChartsContent());
        $this->view->assign('activeTrainingDiary', $this->generateActiveTrainingDiaryContent());
    }

    private function generateActiveTrainingDiaryContent() {

        $content = 'Aktuell ist kein Trainingsplan offen!';
        $trainingPlanService = new Service_TrainingPlan();
        $currentTrainingPlan = $trainingPlanService->searchCurrentTrainingPlan($this->findCurrentUserId());

        if ($currentTrainingPlan instanceof Zend_Db_Table_Row_Abstract) {
            $content = $this->generateCurrentTrainingPlanContent($currentTrainingPlan);
        }

        return $content;
    }

    private function generateCurrentTrainingPlanContent($trainingPlanRow) {
        $exercisesContent = '';
        $trainingPlanId = $trainingPlanRow->offsetGet('training_plan_id');
        $trainingDiaryXTrainingPlanId = null;
        $exercisesCollection = [];
        // trainingPlan is not finished yet
        if ($trainingPlanRow->offsetExists('training_diary_x_training_plan_id')
            && 0 < $trainingPlanRow->offsetGet('training_diary_x_training_plan_id')
        ) {
            $trainingDiaryXTrainingPlanExercise = new Model_DbTable_TrainingDiaryXTrainingPlan();
            $exercisesCollection = $trainingDiaryXTrainingPlanExercise->findTrainingDiaryExercisesByTrainingDiaryXTrainingPlanId($trainingPlanRow->offsetGet('training_diary_x_training_plan_id'));
        } else {
            $trainingPlanXExerciseDb = new Model_DbTable_TrainingPlanXExercise();
            $exercisesCollection = $trainingPlanXExerciseDb->findExercisesByTrainingPlanId($trainingPlanRow->offsetGet('training_plan_id'));
        }

        foreach ($exercisesCollection as $exercise) {
            $this->view->assign($exercise->toArray());
            $exercisesContent .= $this->view->render('loops/current-training-plan-exercise-row.phtml');
        }

        $this->view->assign('exercisesContent', $exercisesContent);
        return $this->view->render('index/current-training-plan.phtml');
    }

    private function generateChartsContent() {
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/c3.min.js', 'text/javascript');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/d3.min.js', 'text/javascript');
        $this->view->headLink()->prependStylesheet($this->view->baseUrl() . '/css/c3.min.css', 'screen', true);

        $chartDataCollection = $this->collectDataForChart();

        $chartContent = '';

        foreach ($chartDataCollection as $exerciseName => $chartData) {
            $chartContent .= $this->generateChartContent($exerciseName, $chartData);
        }
        return $chartContent;
    }

    private function collectDataForChart() {
        $trainingDiaryExerciseOptionDb = new Model_DbTable_TrainingDiaryXExerciseOption();
        $trainingDiaryDeviceOptionDb = new Model_DbTable_TrainingDiaryXDeviceOption();

        $exerciseOptionCollection = $trainingDiaryExerciseOptionDb->findAllExerciseOptions($this->findCurrentUserId());
        $deviceOptionCollection = $trainingDiaryDeviceOptionDb->findAllDeviceOptions($this->findCurrentUserId());

        $data = [];

        foreach ($exerciseOptionCollection as $exerciseOption) {
            $date = date('Y-m-d', strtotime($exerciseOption->offsetGet('training_diary_x_exercise_option_create_date')));
//            $date = $exerciseOption->offsetGet('training_diary_x_exercise_option_create_date');
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
//            $date = $deviceOption->offsetGet('training_diary_x_device_option_create_date');
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

    private function generateChartContent($exerciseName, $chartData) {
        static $chartCount = 1;

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
        ++$chartCount;

        return $this->view->render('loops/chart.phtml');
    }
}

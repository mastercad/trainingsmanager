<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.17
 * Time: 20:20
 */

class Service_Data_TestCasesTrainingPlan {

    private static $testCases = [
        1 => [
            'sqlFiles' => [
                'training_plans.sql',
                'training_diaries.sql',
                'training_diary_x_training_plan.sql',
                'training_diary_x_training_plan_exercise.sql',
            ],
            'expectation' => [
                'training_plan_id' => 50,
                'training_plan_name' => 'Montag',
                'training_plan_training_plan_layout_fk' => 1,
                'training_plan_user_fk' => 22,
                'training_plan_parent_fk' => 39,
                'training_plan_active' => 1,
                'training_plan_order' => 2,
                'training_plan_create_date' => '2017-03-15 20:50:00',
                'training_plan_create_user_fk' => 22,
                'training_plan_update_date' => null,
                'training_plan_update_user_fk' => null,
            ],
            'message' => 'Es wurde kein Trainingsplan begonnen, es wurde als trainingsplan montag erwartet, das ergebnis entspricht aber nicht den erwartungen!'
        ],
        2 => [
            'sqlFiles' => [
                'training_plans.sql',
                'training_diaries.sql',
                'training_diary_x_training_plan.sql',
                'training_diary_x_training_plan_exercise.sql',
            ],
            'expectation' => [
                'training_plan_id' => 54,
                'training_plan_name' => 'Mittwoch',
                'training_plan_training_plan_layout_fk' => 1,
                'training_plan_user_fk' => 22,
                'training_plan_parent_fk' => 39,
                'training_plan_active' => 1,
                'training_plan_order' => 3,
                'training_plan_create_date' => '2017-03-15 21:24:14',
                'training_plan_create_user_fk' => 22,
                'training_plan_update_date' => null,
                'training_plan_update_user_fk' => null,
            ],
            'message' => 'Es wurde Montag beendet, es wird als nächster Trainingsplan Mittwoch erwartet, das ergebnis entspricht aber nicht den erwartungen!'
        ],
        3 => [
            'sqlFiles' => [
                'training_plans.sql',
                'training_diaries.sql',
                'training_diary_x_training_plan.sql',
                'training_diary_x_training_plan_exercise.sql',
            ],
            'expectation' => [
                'training_plan_id' => 50,
                'training_plan_name' => 'Montag',
                'training_plan_training_plan_layout_fk' => 1,
                'training_plan_user_fk' => 22,
                'training_plan_parent_fk' => 39,
                'training_plan_active' => 1,
                'training_plan_order' => 2,
                'training_plan_create_date' => '2017-03-15 20:50:00',
                'training_plan_create_user_fk' => 22,
                'training_plan_update_date' => null,
                'training_plan_update_user_fk' => null,
            ],
            'message' => 'Es wurden alle Trainingspläne einmal beendet, es wird als nächster Trainingsplan Montag erwartet, das ergebnis entspricht aber nicht den erwartungen!'
        ],
        4 => [
            'sqlFiles' => [
                'training_plans.sql',
                'training_diaries.sql',
                'training_diary_x_training_plan.sql',
                'training_diary_x_training_plan_exercise.sql',
            ],
            'expectation' => [
                'training_diary_x_training_plan_id' => 3,
                'training_diary_x_training_plan_training_plan_fk' => 55,
                'training_diary_x_training_plan_flag_finished' => 0,
                'training_diary_x_training_plan_create_date' => '2017-04-22 18:01:01',
                'training_diary_x_training_plan_create_user_fk' => 22,
                'training_diary_x_training_plan_update_date' => '0000-00-00 00:00:00',
                'training_diary_x_training_plan_update_user_fk' => null,
                'training_diary_x_training_plan_training_diary_fk' => 3,
                'training_plan_id' => 55,
                'training_plan_name' => 'Freitag',
                'training_plan_training_plan_layout_fk' => 1,
                'training_plan_user_fk' => 22,
                'training_plan_parent_fk' => 39,
                'training_plan_active' => 1,
                'training_plan_order' => 4,
                'training_plan_create_date' => '2017-03-15 21:24:15',
                'training_plan_create_user_fk' => 22,
                'training_plan_update_date' => null,
                'training_plan_update_user_fk' => null,
            ],
            'message' => 'Es ist noch der Trainingsplan für freitag offen, das ergebnis entspricht aber nicht den erwartungen!'
        ],
        5 => [
            'sqlFiles' => [
                'training_plans.sql',
                'training_diaries.sql',
                'training_diary_x_training_plan.sql',
                'training_diary_x_training_plan_exercise.sql',
            ],
            'expectation' => [
                'training_diary_x_training_plan_id' => 1,
                'training_diary_x_training_plan_training_plan_fk' => 50,
                'training_diary_x_training_plan_flag_finished' => 1,
                'training_diary_x_training_plan_create_date' => '2017-04-19 18:29:42',
                'training_diary_x_training_plan_create_user_fk' => 22,
                'training_diary_x_training_plan_update_date' => '0000-00-00 00:00:00',
                'training_diary_x_training_plan_update_user_fk' => null,
                'training_diary_x_training_plan_training_diary_fk' => 1,
                'training_plan_id' => 50,
                'training_plan_name' => 'Montag',
                'training_plan_training_plan_layout_fk' => 1,
                'training_plan_user_fk' => 22,
                'training_plan_parent_fk' => 0,
                'training_plan_active' => 1,
                'training_plan_order' => 1,
                'training_plan_create_date' => '2017-03-15 20:50:00',
                'training_plan_create_user_fk' => 22,
                'training_plan_update_date' => null,
                'training_plan_update_user_fk' => null,
            ],
            'message' => 'Es nur ein Trainingsplan vorhanden, ich erwarte diesen, das ergebnis entspricht aber nicht den erwartungen!'
        ],
    ];

    /**
     * @param null $testCaseId
     *
     * @return array
     */
    public static function extractTestCase($testCaseId = null) {
        if (false === is_null($testCaseId)) {
            if (array_key_exists($testCaseId, static::$testCases)) {
                return static::$testCases[$testCaseId];
            } else {
                return false;
            }
        }
        return static::$testCases;
    }
}
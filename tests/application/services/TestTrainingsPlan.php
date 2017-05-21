<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.17
 * Time: 18:12
 */

require_once ('data/TestCasesTrainingPlan.php');
require_once (APPLICATION_PATH.'/../tests/AbstractTest.php');

class Test_Service_TrainingsPlan extends Test_AbstractTest
{
    /**
     * mantadory to prevent phpunit output and crash session start
     */
    public static function setupBeforeClass() {
        Zend_Session::$_unitTestEnabled = true;
        static::setDatabasePath(__DIR__ . '/fixtures/');
        parent::setupBeforeClass();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFindNextTrainingPlan($testCaseId, $testCase) {
        $this->prepareDatabase($testCaseId, $testCase);
        $trainingPlanService = new Service_TrainingPlan();
        $trainingPlan = $trainingPlanService->searchCurrentTrainingPlan(22)->toArray();

        $this->assertEquals($testCase['expectation'], $trainingPlan, $testCase['message']);
    }

    /**
     * dataProvider for testFindNextTrainingPlan
     *
     * @return array
     */
    public function dataProvider() {
        $testCasesEnvironment = Service_Data_TestCasesTrainingPlan::extractTestCase();
        $testCases = [];

        foreach ($testCasesEnvironment as $testCaseId => $currentTestCase) {
            $testCases[] = [$testCaseId, $currentTestCase];
        }
        return $testCases;
    }
}
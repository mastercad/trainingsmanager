<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 11.05.17
 * Time: 18:12
 */

require_once ('data/TestCasesTrainingPlan.php');

class Test_Service_TrainingsPlan extends PHPUnit_Framework_TestCase
{
    private static $currentTempDatabaseName = null;

    /** @var Zend_Db_Adapter_Pdo_Mysql  */
    private static $currentDatabaseAdapter = null;

    /** @var Zend_Db_Adapter_Pdo_Mysql  */
    private static $origDatabaseAdapter = null;

    /** @var string */
    private static $origDatabaseName = null;

    public function tearDown() {
        static::removeTempDatabaseAdapter();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFindNextTrainingPlan($testCaseId, $expected, $message) {
        $this->prepareDatabase($testCaseId);
        $trainingPlanService = new Service_TrainingPlan();
        $trainingPlan = $trainingPlanService->searchCurrentTrainingPlan(22)->toArray();

        $this->assertEquals($expected, $trainingPlan, $message);
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
            $testCases[] = [$testCaseId, $currentTestCase['expectation'], $currentTestCase['message']];
        }
        return $testCases;
    }

    /**
     * prepares database with given fixtures
     *
     * @param $testCaseId
     */
    private function prepareDatabase($testCaseId) {
        static::prepareDatabaseAdapter();
        $testCase = Service_Data_TestCasesTrainingPlan::extractTestCase($testCaseId);

        foreach ($testCase['sqlFiles'] as $currentTestCaseDbFile) {
            static::$currentDatabaseAdapter->exec(file_get_contents(__DIR__ . '/fixtures/' . $testCaseId . '/' . $currentTestCaseDbFile));
        }
    }

    /**
     * prepares the Database for current Test
     */
    public static function prepareDatabaseAdapter() {
        /** @var Zend_Db_Adapter_Pdo_Mysql $db */
        $db = Zend_Registry::get('db');
        static::$currentTempDatabaseName = 'trainings_manager_' . time();
        // create new temporary database
        /** @var Zend_Db_Adapter_Pdo_Mysql $currentAdapter */
        static::$origDatabaseAdapter = Zend_Db_Table::getDefaultAdapter();
        static::$origDatabaseAdapter->exec('CREATE DATABASE ' . static::$currentTempDatabaseName);

        $dbConfig = $db->getConfig();
        static::$origDatabaseName = $dbConfig['dbname'];
        $dbConfig['dbname'] = static::$currentTempDatabaseName;
        static::$currentDatabaseAdapter = new Zend_Db_Adapter_Pdo_Mysql($dbConfig);
        Zend_Db_Table::setDefaultAdapter(static::$currentDatabaseAdapter);
    }

    /**
     * reset default database adapter
     */
    public static function removeTempDatabaseAdapter() {
        static::$currentDatabaseAdapter->exec('DROP DATABASE ' . static::$currentTempDatabaseName);
        Zend_Db_Table::setDefaultAdapter(static::$origDatabaseAdapter);
    }
}
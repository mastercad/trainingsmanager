<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.17
 * Time: 20:06
 */

class Test_AbstractTest extends \PHPUnit\Framework\TestCase {

    protected static $currentTempDatabaseName = null;

    /** @var Zend_Db_Adapter_Pdo_Mysql  */
    protected static $currentDatabaseAdapter = null;

    /** @var Zend_Db_Adapter_Pdo_Mysql  */
    protected static $origDatabaseAdapter = null;

    /** @var string */
    protected static $origDatabaseName = null;

    protected static $databasePath = null;

    /**
     * mantadory to prevent phpunit output and crash session start
     */
    public static function setupBeforeClass() {
        Zend_Session::$_unitTestEnabled = true;
    }

    public static function tearDownAfterClass() {
        static::removeTempDatabaseAdapter();
    }

    /**
     * prepares database with given fixtures
     *
     * @param $testCaseId
     * @param array $testCase
     */
    protected function prepareDatabase($testCaseId, array $testCase) {
        static::prepareDatabaseAdapter();

        foreach ($testCase['sqlFiles'] as $currentTestCaseDbFile) {
            static::$currentDatabaseAdapter->exec(file_get_contents(static::getDatabasePath() . $testCaseId . '/' . $currentTestCaseDbFile));
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
        if (static::$currentDatabaseAdapter instanceof Zend_Db_Adapter_Abstract) {
            static::$currentDatabaseAdapter->exec('DROP DATABASE ' . static::$currentTempDatabaseName);
            Zend_Db_Table::setDefaultAdapter(static::$origDatabaseAdapter);
        }
    }

    /**
     * @return null
     */
    protected static function getDatabasePath() {
        return static::$databasePath;
    }

    /**
     * @param null $databasePath
     */
    protected static function setDatabasePath($databasePath) {
        static::$databasePath = $databasePath;
    }
}
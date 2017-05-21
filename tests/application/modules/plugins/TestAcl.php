<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.17
 * Time: 19:53
 */

require_once ('data/TestCasesAcl.php');
require_once (APPLICATION_PATH.'/../tests/AbstractTest.php');

class Test_Module_Plugin_Acl extends Test_AbstractTest
{
    /** @var Auth_Plugin_Acl */
    private static $aclPlugin = null;

    /**
     * @dataProvider dataProviderBaseAclTest
     *
     * @param string $role
     * @param string $resource
     * @param string $privilege
     * @param bool $expectation
     * @param string $message
     */
    public function testAclCreation($role, $resource, $privilege, $expectation, $message) {
        $this->assertEquals($expectation, static::$aclPlugin->isAllowed($role, $resource, $privilege), $message);
    }

    /**
     * dataProvider for testFindNextTrainingPlan
     *
     * @return array
     */
    public function dataProviderBaseAclTest() {
        return Service_Module_Plugin_Data_TestCasesAcl::extractTestCase();
    }

    /**
     * mantadory to prevent phpunit output and crash session start
     */
    public static function setupBeforeClass() {
        parent::setupBeforeClass();
        static::setDatabasePath(__DIR__ . '/fixtures/');
        static::prepareDatabase(1, [
            'sqlFiles' => [
                'user_right_groups.sql',
                'user_right_group_rights.sql',
            ]
        ]);
        static::$aclPlugin = new Auth_Plugin_Acl();
    }

}
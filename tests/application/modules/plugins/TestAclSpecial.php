<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.17
 * Time: 19:53
 */

require_once ('data/TestCasesAclSpecial.php');
require_once (APPLICATION_PATH.'/../tests/AbstractTest.php');

class Test_Module_Plugin_AclSpecial extends Test_AbstractTest
{
    /** @var Auth_Plugin_Acl */
    private static $aclPlugin = null;

    /**
     * mantadory to prevent phpunit output and crash session start
     */
    public static function setupBeforeClass() {
        parent::setupBeforeClass();
        static::setDatabasePath(__DIR__ . '/fixtures/');
        static::prepareDatabase(2, [
            'sqlFiles' => [
                'users.sql',
                'user_groups.sql',
                'user_x_user_group.sql',
                'user_right_groups.sql',
                'user_right_group_rights.sql',
                'exercises.sql',
                'training_plans.sql',
            ]
        ]);
        static::$aclPlugin = new Auth_Plugin_Acl();
    }

    /**
     * @dataProvider dataProviderSpecialRightsTest
     */
    public function testSpecialRights($resource, $privilege, $type, $id, $roleConfig, $expectation, $message) {
        static::$aclPlugin->prepareDynamicPermissionsForCurrentResource($roleConfig['role'], $resource, $privilege);
        $dbClassName = 'Model_DbTable_'.ucfirst($type);
        $role = new Auth_Model_Role_Member($roleConfig);
        /** @var Model_DbTable_Abstract $db */
        $db = new $dbClassName();
        $row = $db->findByPrimary($id);
        $resourceClassName = 'Auth_Model_Resource_'.ucfirst($type);
        $resource = new $resourceClassName($row);
        $this->assertEquals($expectation, static::$aclPlugin->isAllowed($role, $resource, $privilege), $message);
    }

    /**
     * dataProvider for testFindNextTrainingPlan
     *
     * @return array
     */
    public function dataProviderSpecialRightsTest() {
        return Service_Module_Plugin_Data_TestCasesAclSpecial::extractTestCase();
    }

}
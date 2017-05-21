<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.05.17
 * Time: 22:50
 */

class Service_Module_Plugin_Data_TestCasesAclSpecial
{
    private static $testCases = [
        1 => ['default:exercises', 'edit', 'exercises', 1, ['userId' => 2 , 'role' => 'guest', 'groupId' => 1, '' => ''], false, ''],
        2 => ['default:exercises', 'edit', 'exercises', 1, ['userId' => 3, 'role' => 'user', 'groupId' => 1, 'groupName' => 'user_group'], true, ''],
        3 => ['default:exercises', 'edit', 'exercises', 1, ['userId' => 4, 'role' => 'user', 'groupId' => 1, 'groupName' => 'user_group'], false, ''],
        4 => ['default:exercises', 'edit', 'exercises', 1, ['userId' => 4, 'role' => 'member', 'groupId' => 1, 'groupName' => 'user_group'], false, ''],
        5 => ['default:exercises', 'edit', 'exercises', 1, ['userId' => 5, 'role' => 'group_admin', 'groupId' => 1, 'groupName' => 'user_group'], true, ''],
        6 => ['default:exercises', 'edit', 'exercises', 1, ['userId' => 6, 'role' => 'admin', 'groupId' => 1, 'groupName' => 'user_group'], true, ''],
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
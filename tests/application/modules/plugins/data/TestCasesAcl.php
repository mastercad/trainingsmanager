<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.05.17
 * Time: 22:50
 */

class Service_Module_Plugin_Data_TestCasesAcl
{
    private static $testCases = [
        1 => ['guest', 'default:index', 'index', true, ''],
        2 => ['user', 'default:index', 'index', true, ''],
        3 => ['member', 'default:index', 'index', true, ''],
        4 => ['admin', 'default:index', 'index', true, ''],
        4 => ['guest', 'auth:admin', 'index', false, ''],
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
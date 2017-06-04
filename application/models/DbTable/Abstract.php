<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.04.16
 * Time: 09:50
 */

require_once(__DIR__ . '/../../../library/Zend/Db/Table.php');

abstract class Model_DbTable_Abstract extends Zend_Db_Table_Abstract {

    /** @var */
    protected static $aTableMetaData;

    /**
     * @throws Zend_Db_Table_Exception
     */
    public function init() {
        if(true === empty(self::$aTableMetaData)) {
            self::$aTableMetaData = $this->info();
        }

        if (true === $this->checkIfTestUser()) {
            $this->_name = 'test_' . $this->_name;
        }
    }

    abstract function findByPrimary($id);

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return self::$aTableMetaData;
    }

    public function checkIfTestUser() {

        $user = Zend_Auth::getInstance()->getIdentity();

        // current user member of a test group
        if (true === is_object($user)
            && preg_match('/^test\_/', $user->user_right_group_name)
            && false === strpos('test_', $this->_name)
        ) {
            return true;
        }
        return false;
    }

    public function considerTestUserForTableName($tableName) {
        if (true === $this->checkIfTestUser()
            && 0 == preg_match('/^test_/', $tableName)
        ) {
            return 'test_' . trim($tableName);
        }
        return $tableName;
    }

    /**
     * notwendig um zwischen einer tabelle und einer test tabelle mit dem selben namen zu syncen und nicht für
     * jede test tabelle ein neues model anlegen zu müssen...
     *
     * @param $name
     */
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }
}
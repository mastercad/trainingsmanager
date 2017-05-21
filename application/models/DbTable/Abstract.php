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

        $user = Zend_Auth::getInstance()->getIdentity();

        // current user member of a test group
        if (true === is_object($user)
            && preg_match('/^test\_/', $user->user_right_group_name)
            && false === strpos('test_', $this->_name)
        ) {
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
}
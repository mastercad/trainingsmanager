<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.04.16
 * Time: 09:50
 */

require_once(__DIR__ . '/../../../library/Zend/Db/Table.php');

class Application_Model_DbTable_Abstract extends Zend_Db_Table_Abstract {

    /** @var */
    protected static $aTableMetaData;

    /**
     * @throws Zend_Db_Table_Exception
     */
    public function init() {
        if(true === empty(self::$aTableMetaData)) {
            self::$aTableMetaData = $this->info();
        }
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return self::$aTableMetaData;
    }
}
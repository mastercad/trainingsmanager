<?php
/*
 * Created on 17.06.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
 * Class Application_Model_DbTable_UserRightGroupsRight
 */
class Application_Model_DbTable_UserRightGroupsRight extends Application_Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'user_rechte_gruppen_rechte';
    /**
     * @var string
     */
    protected $_primary = 'user_rechte_gruppen_recht_id';

    /**
     * @return bool|Zend_Db_Table_Rowset_Abstract
     */
    public function findAllUserRightGroupRights()
    {
        try {
            return $this->fetchAll();
        } catch (Exception $oException) {
            echo "In " . __FUNCTION__ . " Klasse " . __CLASS__ . " Trat folgender Fehler auf:<br />";
            echo $oException . "<br />";
        }
        return false;
    }
}

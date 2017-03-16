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
class Model_DbTable_UserRightGroupRights extends Model_DbTable_Abstract
{
    /**
     * @var string
     */
    protected $_name 	= 'user_right_group_rights';
    /**
     * @var string
     */
    protected $_primary = 'user_right_group_right_id';

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

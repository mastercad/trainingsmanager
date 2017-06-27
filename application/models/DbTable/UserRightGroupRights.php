<?php
/*
 * Created on 17.06.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

namespace Model\DbTable;

use Zend_Db_Table_Rowset_Abstract;
use Exception;
use Nette\NotImplementedException;

/**
 * Class Application_Model_DbTable_UserRightGroupsRight
 */
class UserRightGroupRights extends AbstractDbTable
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
     * @inheritdoc
     */
    function findByPrimary($id) {
        throw new NotImplementedException('Function findByPrimary not implemented yet!');
    }

    /**
     * find all user right group rights
     *
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

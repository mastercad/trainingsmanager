<?php

namespace Auth\Model\DbTable;

use Zend_Db_Table_Abstract;
use Exception;

/*
 * Created on 17.06.2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class UserRightGroupRights extends Zend_Db_Table_Abstract
{
    protected $_name     = 'user_right_group_rights';
    protected $_primary = 'user_right_group_right_id';

    protected static $_oMeta;

    public function init()
    {
        if(!self::$_oMeta) {
            self::$_oMeta = $this->info();
        }
    }

    public function findUserRightGroupRights($iUserGruppeRechtFk = null)
    {
        try {
            $oSelect = $this->select(true)->setIntegrityCheck(false);
            if (null !== $iUserGruppeRechtFk) {
                $oSelect->where('user_right_group_fk = ' . $iUserGruppeRechtFk);
            }
            $oSelect->order('user_right_group_right');
            return $this->fetchAll($oSelect);
        } catch (Exception $oException) {
            echo "In " . __FUNCTION__ . " Klasse " . __CLASS__ . " Trat folgender Fehler auf:<br />";
            echo $oException . "<br />";
        }
    }
}

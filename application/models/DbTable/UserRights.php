<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Exception;

/**
 * Class Application_Model_DbTable_UserRights
 */
class UserRights extends AbstractDbTable
{
    /**
     * @var string
     */
    protected $_name     = 'user_rights';
    /**
     * @var string
     */
    protected $_primary = 'user_right_id';

    /**
     * find user rights
     *
     * @param array $options
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findUserRights($options) 
    {
        $select = $this->select();
        
        foreach ($options['where_fields'] as $sKey => $sOption) {
            $select->where($sKey . " = ?", $sOption);
        }

        try {
            return $this->fetchRow($select);
        } catch (Exception $oException) {
               echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
               echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }

    /**
     * save user right
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveUserRight($aData) 
    {
        try {
               return $this->insert($aData);
        } catch(Exception $oException) {
               echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
               echo "Meldung : " . $oException->getMessage() . "<br />";
        }
        return false;
    }
}
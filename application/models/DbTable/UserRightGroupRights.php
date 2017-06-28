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

use Zend_Db_Table_Rowset_Abstract;
use Exception;

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

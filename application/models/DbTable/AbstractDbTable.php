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

use Zend_Db_Table_Abstract;
use Zend_Db_Table_Exception;
use Zend_Auth;

/**
 * Class AbstractDbTable
 *
 * @package Model\DbTable
 */
abstract class AbstractDbTable extends Zend_Db_Table_Abstract
{

    /**
     * @var array 
     */
    protected static $aTableMetaData;

    /**
     * @throws Zend_Db_Table_Exception
     */
    public function init() 
    {
        if(true === empty(self::$aTableMetaData)) {
            self::$aTableMetaData = $this->info();
        }

        if (true === $this->checkIfTestUser()) {
            $this->_name = 'test_' . $this->_name;
        }
    }

    /**
     * find row by primary key for current table
     *
     * @param $primary
     *
     * @return mixed
     */
    function findByPrimary($primary) 
    {
        $select = $this->select(self::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        $select->where($this->_primary[1] . ' = ?', intval($primary));

        return $this->fetchRow($select);
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return self::$aTableMetaData;
    }

    /**
     * checks, if current user in one of the test groups
     *
     * @return bool
     */
    public function checkIfTestUser() 
    {

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

    /**
     * if renames current table with prefix test, if not already done
     *
     * @param $tableName
     *
     * @return string
     */
    public function considerTestUserForTableName($tableName) 
    {
        if (true === $this->checkIfTestUser()
            && 0 == preg_match('/^test_/', $tableName)
        ) {
            return 'test_' . trim($tableName);
        }
        return $tableName;
    }

    /**
     * notwendig um zwischen einer tabelle und einer test tabelle mit dem selben namen zu syncen und nicht f??r
     * jede test tabelle ein neues model anlegen zu m??ssen...
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name) 
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return string tableName
     */
    public function getName() 
    {
        return $this->_name;
    }
}
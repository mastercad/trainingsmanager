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
 * Class Application_Model_DbTable_DeviceGroups
 */
class Widgets extends AbstractDbTable
{
    /**
     * @var string 
     */
    protected $_name     = 'widgets';

    /**
     * @var string 
     */
    protected $_primary = 'widget_id';

    /**
     * find all widgets
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAllWidgets() 
    {
        return $this->fetchAll(null, "widget_name");
    }

    /**
     * save widgets data
     *
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveWidgets($aData) 
    {
        try {
            return $this->insert($aData);
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * update widget data
     *
     * @param array $aData
     * @param int   $widgetId
     *
     * @return bool|int
     */
    public function updateWidget($aData, $widgetId) 
    {
        try {
            return $this->update($aData, "widget_id = '" . $widgetId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * delete widget
     *
     * @param int $widgetId
     *
     * @return bool|int
     */
    public function deleteDashboard($widgetId) 
    {
        try {
            return $this->delete("widget_id = '" . $widgetId . "'");
        } catch( Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.06.17
 * Time: 18:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

require_once APPLICATION_PATH . '/controllers/AbstractController.php';

use Model\DbTable\UserRightGroupRights;

/**
 * Class SyncController
 */
class SyncController extends AbstractController
{

    public function databasesAction() 
    {
        $userRightGroupRightsDb = new UserRightGroupRights();
        $testUserRightGroupRightsDb = new UserRightGroupRights();
        $testUserRightGroupRightsDb->setName('test_user_right_group_rights');

        $userRightGroupRights = $userRightGroupRightsDb->findAllUserRightGroupRights();

        // delete complete database
        $testUserRightGroupRightsDb->delete([]);
        foreach ($userRightGroupRights as $userRightGroupRight) {
            $testUserRightGroupRightsDb->insert($userRightGroupRight->toArray());
        }
        return $this;
    }
}
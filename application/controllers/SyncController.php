<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.06.17
 * Time: 18:08
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class SyncController extends AbstractController {

    public function databasesAction() {
        $userRightGroupRightsDb = new Model_DbTable_UserRightGroupRights();
        $testUserRightGroupRightsDb = new Model_DbTable_UserRightGroupRights();
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
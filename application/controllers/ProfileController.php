<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 21.05.17
 * Time: 21:25
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class ProfileController extends AbstractController {

    public function indexAction() {
        $usersDb = new Model_DbTable_Users();
    }

    public function showAction() {
        $usersDb = new Model_DbTable_Users();
        $user = $usersDb->findUser($this->findCurrentUserId());
        $this->view->assign($user->toArray());
    }

    public function editAction() {

    }

    public function newAction() {

    }
}
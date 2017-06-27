<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 21.05.17
 * Time: 21:25
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

use Model\DbTable\Users;

/**
 * Class ProfileController
 */
class ProfileController extends AbstractController {

    /**
     * index action
     */
    public function indexAction() {
        $usersDb = new \Model\DbTable\Users();
    }

    /**
     * show action
     */
    public function showAction() {
        $usersDb = new Users();
        $user = $usersDb->findUser($this->findCurrentUserId());
        $this->view->assign($user->toArray());
    }

    /**
     * edit action
     */
    public function editAction() {

    }

    /**
     * new action
     */
    public function newAction() {

    }
}
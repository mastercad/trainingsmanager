<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.05.17
 * Time: 21:55
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class ResetController extends AbstractController {

    public function indexAction() {
        $resetService = new Service_Reset();
        $resetService->cleanTestActivities();
    }
}
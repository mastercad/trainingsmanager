<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class InterpolateController extends AbstractController {


    public function indexAction() {

    }

    public function trainingDiaryAction() {
        $userId = $this->getParam('userId');

        if (0 < $userId) {
            $interpolateService = new Service_Interpolate();
            $result = $interpolateService->trainingDiary($userId);
        }
    }
}
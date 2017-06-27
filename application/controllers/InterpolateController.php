<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

use Service\Interpolate;

class InterpolateController extends AbstractController {

    /**
     * index action
     */
    public function indexAction() {

    }

    /**
     * interpolate training diary
     */
    public function trainingDiaryAction() {
        $userId = $this->getParam('userId');

        if (0 < $userId) {
            $interpolateService = new Interpolate();
            $result = $interpolateService->trainingDiary($userId);
        }
    }
}
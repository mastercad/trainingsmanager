<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

use \Service\Generator\Thumbnail;

/**
 * Class ButlerController
 */
class ButlerController extends AbstractController
{
    /**
     * create thumb action
     *
     * this action generates image
     */
    public function createThumbAction() {
        $this->view->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $thumbnailService = new Thumbnail();
        $thumbnailService->generate($this->getAllParams());
    }

    /**
     * create image string action
     *
     * this action generates source string
     */
    public function createImageStringAction() {

        $this->view->layout()->disableLayout();

        $thumbnailService = new Thumbnail();
        $imageSourceData = $thumbnailService->generateImageString($this->getAllParams());

        $this->view->assign('sourceData', $imageSourceData);
    }
}


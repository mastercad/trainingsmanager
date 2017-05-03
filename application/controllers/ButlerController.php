<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

/**
 * Class ButlerController
 */
class ButlerController extends AbstractController
{
    /**
     *
     */
    public function createThumbAction() {
        $this->view->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $thumbnailService = new Service_Generator_Thumbnail();
        $thumbnailService->generate($this->getAllParams());
    }

    public function createImageStringAction() {

        $this->view->layout()->disableLayout();

        $thumbnailService = new Service_Generator_Thumbnail();
        $imageSourceData = $thumbnailService->generateImageString($this->getAllParams());

        $this->view->assign('sourceData', $imageSourceData);
    }
}


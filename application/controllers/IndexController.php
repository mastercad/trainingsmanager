<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class IndexController extends AbstractController {

    public function indexAction() {

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/c3.min.js', 'text/javascript');
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/d3.min.js', 'text/javascript');

        $this->view->headLink()->prependStylesheet($this->view->baseUrl() . '/css/c3.min.css', 'screen', true);
    }
}


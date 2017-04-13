<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 24.03.17
 * Time: 19:45
 */

abstract class Service_Generator_View_GeneratorAbstract {

    /** @var Zend_View_Abstract */
    private $view = null;

    /**
     * @return Zend_View_Abstract
     */
    public function getView() {
        return $this->view;
    }

    /**
     * @param Zend_View_Abstract $view
     */
    public function setView($view) {
        $this->view = $view;
    }

    public function __construct(Zend_View_Abstract $view) {
        $this->setView($view);
    }

    /**
     * @param      $tag
     * @param null $locale
     *
     * @return mixed
     * @throws \Zend_Exception
     */
    protected function translate($tag, $locale = null) {
        return Zend_Registry::get('Zend_Translate')->translate($tag, $locale);
    }

}
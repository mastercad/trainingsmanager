<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.03.17
 * Time: 07:39
 */

class AbstractController extends Zend_Controller_Action {

    /**
     * @var
     */
    private $breadcrumb;
    /**
     * @var
     */
    private $keywords;
    /**
     * @var
     */
    private $description;

    /** @var  Zend_Translate */
    private $translation;
    /**
     * @var array
     */
    private $userDefinedLanguages = [];
    /**
     * @var null
     */
    private $favoriteLanguage = null;

    /**
     * @var array
     */
    private $translationSources = [

    ];

    /**
     *
     */
    public function init() {
        $this->initTranslation();
    }

    /**
     *
     */
    public function postDispatch() {

        $params = $this->getRequest()->getParams();

        if(isset($params['ajax']))
        {
            $this->view->layout()->disableLayout();
        }

        $this->view->headMeta()->appendName('keywords', $this->keywords);
        $this->view->headMeta()->appendName('description', $this->description);
        $this->view->assign('breadcrumb', $this->breadcrumb);
        $this->view->assign('translation', $this->translation);
    }

    /**
     *
     */
    private function initTranslation() {

        $this->prepareLanguage();

//        $this->translation = new Zend_Translate([
//                'adapter' => 'array',
//                'content' => APPLICATION_PATH . '/../languages/',
//                'locale'  => 'auto',
//                'scan'    => Zend_Translate::LOCALE_FILENAME
//            ]
//        );

        $baseTranslationPath = APPLICATION_PATH . '/../languages/';
        if (! file_exists($baseTranslationPath . $this->favoriteLanguage)
            && preg_match('/^([a-z]{2})[\_|\-]([A-Z]{2})$/', $this->favoriteLanguage, $matches)
        ) {
            $this->favoriteLanguage = $matches[1];
        }

        $this->translation = new Zend_Translate([
                'adapter' => 'array',
                'content' =>  $baseTranslationPath . $this->favoriteLanguage . '/',
                'locale'  => $this->favoriteLanguage,
            ]
        );

        Zend_Registry::set('Zend_Translate', $this->translation);
    }

    /**
     * prepares the language for translations, first prio is param string in get
     * second prio is HTTP_ACCEPT_LANGUAGE in SERVER vars
     * third set fallback to "en"
     *
     */
    private function prepareLanguage() {

        $this->favoriteLanguage = $this->getParam('lang');

        if (!$this->favoriteLanguage) {
            $langString = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $foundLanguages = explode(',', $langString);

            foreach ($foundLanguages as $pos => $language) {
                if (preg_match('/([a-z\-\_]*);q=([0-9\.]*)/i', $language, $matches)) {
                    $this->addUserDefinedLanguage($matches[1], $matches[2]);
                } else if (preg_match('/([a-z\-\_]*)=([0-9\.]*)/i', $language, $matches)) {
                    $this->addUserDefinedLanguage($matches[1], $matches[2]);
                } else if (preg_match('/([a-z\-\_]*)/i', $language, $matches)) {
                    $this->addUserDefinedLanguage($matches[1], $pos + 1);
                }
            }
            $this->favoriteLanguage = reset($this->userDefinedLanguages);
        }

        if (!$this->favoriteLanguage) {
            $this->favoriteLanguage = 'en';
        }
    }

    /**
     * @param     $lang
     * @param int $pos
     */
    protected function addUserDefinedLanguage($lang, $pos = 1) {
        $this->userDefinedLanguages[$pos . '#' . $lang] = $lang;
        krsort($this->userDefinedLanguages);
    }

    /**
     * add a single translation source file name (without path) to the translations array
     *
     * @param $translationSource
     *
     * @return $this
     */
    protected function addTranslationSource($translationSource) {
        $this->translationSources[] = $translationSource;
        return $this;
    }

    /**
     * @param $translation
     *
     * @return $this
     */
    protected function setTranslation($translation) {
        $this->translation = $translation;
        return $this;
    }

    /**
     * @return \Zend_Translate
     */
    protected function getTranslation() {
        return $this->translation;
    }

    /**
     * @return \Zend_Translate_Adapter
     */
    protected function getTranslator() {
        return $this->getTranslation()->getAdapter();
    }

    /**
     * @param string $tag
     * @param null $locale
     *
     * @return string
     */
    protected function translate($tag, $locale = null) {
        return $this->getTranslator()->translate($tag, $locale);
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function generateDetailOptionsContent($id) {
        $content = '<div class="glyphicon glyphicon-edit edit-button" data-id="' . $id . '"></div>'.
            '<div class="glyphicon glyphicon-trash delete-button" data-id="' . $id . '"></div>';

        return $content;
    }
}
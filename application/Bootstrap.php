<?php

define('ZEND_LOGGER','zend_logger');
define('SESSION_TIMEOUT', 7400);

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());

        Zend_Registry::set('config', $config);
    }

    protected function _initTimezone()
    {
        date_default_timezone_set('Europe/Berlin');
    }

    protected function _initLocale()
    {
        Zend_Locale::setDefault('de_DE');
        $locale = new Zend_Locale('de_DE');

        Zend_Registry::set('Zend_Locale', $locale);
    }

    protected function _initAutoloader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

//        $resourceLoader->addResourceTypes(array(
//            'acl' => array(
//                'path'      => 'acls/',
//                'namespace' => 'Acl',
//            ),
//            'example' => array(
//                'path'      => 'examples/',
//                'namespace' => 'Example',
//            ),
//        ));

        return $autoloader;
    }

    protected function _initResourceloader() {

//        $resourceloader = $this->getResourceLoader()->addResourceType('plugins', APPLICATION_PATH . '/plugins', 'Plugin');
//        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
//            'basePath'  => APPLICATION_PATH . '/plugins',
//            'namespace' => 'Plugin',
//        ));
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'  => APPLICATION_PATH,
            'namespace' => '',
        ));
        $resourceLoader->addResourceType('plugin', 'plugins', 'Plugin');
        $resourceLoader->addResourceType('service', 'services', 'Service');
        $resourceLoader->addResourceType('interface', 'interfaces', 'Interface');
        $resourceLoader->addResourceType('collection', 'collections', 'Collection');
        $resourceLoader->addResourceType('entities', 'entities', 'Entity');
        $resourceLoader->addResourceType('model', 'models', 'Model');

        return $resourceLoader;
    }

    public function _initActionHelper()
    {
        Zend_Controller_Action_HelperBroker::addPath(
                APPLICATION_PATH . '/controllers/helpers', 'Helper');

//        Zend_Controller_Action_HelperBroker::getStaticHelper('sidebar');
    }

    public function _initMVC()
    {
        Zend_Layout::startMvc();
    }

    protected function _initDb()
    {
        $configuration = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        $dbAdapter = Zend_Db::factory($configuration->resources->db);
        Zend_Registry::set('db', $dbAdapter);
        Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
    }

    /**
     *
     */
    protected function _initCache()
    {
        $frontend = array(
            'lifetime' => 7200,
            'automatic_serialization' => true
        );

        $backend = array(
            'cache_dir' => APPLICATION_PATH . '/../public/tmp/',
        );

        $cache = Zend_Cache::factory(
            'core',
            'File',
            $frontend,
            $backend
        );
        Zend_Registry::set('cache', $cache);
    }

    /**
     *
     */
    protected function _initCoreSession()
    {
        $this->bootstrap('session');
    }

    protected function _initView()
    {
        $view = new Zend_View();

        $view->headTitle( 'Trainingsmanager');
        $view->headTitle()->setSeparator(' | ');

        $view->setEncoding('UTF-8');
        $view->env = APPLICATION_ENV;

        $view_renderer = Zend_Controller_Action_HelperBroker::getStaticHelper( 'ViewRenderer');
        $view_renderer->setView( $view);

        Zend_Registry::set('view', $view);

        return $view;
    }

    protected function _initLogger()
    {
        $writer = new Zend_Log_Writer_Firebug();

        $mail = new Zend_Mail();
        $mail->setFrom('fehler@byte-artist.de')
            ->addTo('webservice@byte-artist.de');

        $writer_mail = new Zend_Log_Writer_Mail($mail);

        // Setzt den Subjekt-Text der verwendet wird; Zusammenfassung der Anzahl der
        // Fehler wird der Subjektzeile angefügt bevor die Nachricht gesendet wird.
        $writer_mail->setSubjectPrependText('Fehler auf http://www.byte-artist.de');

        // Nur Einträge vom Level Warnung und höher schicken
        $writer_mail->addFilter(Zend_Log::WARN);

        $writer_file = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../data/application.log');

        $logger = new Zend_Log();
        $logger->addWriter($writer);
        $logger->addWriter($writer_mail);
        $logger->addWriter($writer_file);

        Zend_Registry::set(ZEND_LOGGER, $logger);
    }

    protected function _initDoctype()
    {
        $view = $this->getResource('view');
        $view->doctype('HTML5');
    }

    protected function _initMeta()
    {
        $view = $this->getResource('view');

        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headMeta()->appendHttpEquiv('Content-Language', 'de-DE');
        $view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $view->headMeta()->appendName('robots', 'index, follow');
        $view->headMeta()->appendName('author', 'Andreas Kempe');
    }

    protected function _initHeadScripts()
    {
        /** @var Zend_View $view */
        $view = $this->getResource('view');

        /** @var Zend_View_Helper_HeadScript $view->headScript() */
//        $view->headScript()->prependFile($view->baseUrl() . '/js/default.js', 'text/javascript');
        $view->headScript()->offsetSetFile(1, $view->baseUrl() . '/js/jquery-3.2.0.min.js', 'text/javascript');
        $view->headScript()->offsetSetFile(2, $view->baseUrl() . '/js/base64.js', 'text/javascript');
        $view->headScript()->offsetSetFile(3, $view->baseUrl() . '/js/auth.js', 'text/javascript');
        $view->headScript()->offsetSetFile(5, $view->baseUrl() . '/js/jquery-ui.min.js', 'text/javascript');
        $view->headScript()->offsetSetFile(7, $view->baseUrl() . '/js/bootstrap-tour.min.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(6, $view->baseUrl() . '/js/tether.min.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery.sharrre-1.3.4.min.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery_counts.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/funktionen.min.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(10, $view->baseUrl() . '/js/funktionen.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/funktionen_jquery.min.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(15, $view->baseUrl() . '/js/funktionen_jquery.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(20, $view->baseUrl() . '/js/default.js', 'text/javascript');

//        if(TRUE === is_object($obj_device)
//            && $obj_device->getType() == "desktop"
//        ) {
//            $view->headScript()->appendFile($view->baseUrl() . '/js/blur.js', 'text/javascript');
//        }
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery.snippet.min.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(25, $view->baseUrl() . '/js/cad.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(30, $view->baseUrl() . '/js/cad_wrapper.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(35, $view->baseUrl() . '/js/cad_catch_esc.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(40, $view->baseUrl() . '/js/cad_cms.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(45, $view->baseUrl() . '/js/cad_sperre.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(50, $view->baseUrl() . '/js/cad_message.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(55, $view->baseUrl() . '/js/cad_loader.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery-ui-1.10.1.custom.min.js', 'text/javascript');
//        $view->headScript()->offsetSetFile(60, $view->baseUrl() . '/js/auth.js', 'text/javascript');
        $view->headScript()->offsetSetFile(100, $view->baseUrl() . '/js/html5.js', 'text/javascript', ['conditional' => 'lt IE 9']);
//      $view->headScript()->appendFile('https://apis.google.com/js/plusone.js', 'text/javascript');
    }

    protected function _initLink()
    {
        $view = Zend_Registry::get('view');

        $view->headLink()->appendAlternate($view->baseUrl() . 'http://gmpg.org/xfn/11', 'text/html', true, array('rel' => 'profile', 'title' => 'XFN Profile Version for Meta Markup'));
        $view->headLink()->appendAlternate($view->baseUrl() . '/feed-rss', 'application/rss+xml', 'News-Feed im RSS Format');
        $view->headLink()->appendAlternate($view->baseUrl() . '/feed-atom', 'application/atom+xml', 'News-Feed im Atom Format');

        $view->headLink()->prependStylesheet($view->baseUrl() . '/css/global.css', 'screen', true);
    }

    /*
    protected function _initInlineScripts()
    {
        $view = Zend_Registry::get('view');

        $view->inlineScript()->appendScript("
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-19235950-1']);
            _gaq.push(['_setDomainName', 'byte-artist.de']);
            _gaq.push(['_trackPageview']);

            (function() {
              var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
              ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
              var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();")
          ->appendScript('init();');
    }
    */

    protected function _initFavIcon()
    {
        $view = Zend_Registry::get('view');
        $view->headLink(array('rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon', 'href' => '/images/favicon.ico'));
    }

    protected function _initEmailTransport()
    {
        /*
        $smtp_config = Zend_Registry::get('config')->get('smtp');

        $emailConfig    = array(
            'auth'		=> 'login',
            'username'	=> $smtp_config->params->get('username'),
            'password'	=> $smtp_config->params->get('password'),
            'ssl'		=> $smtp_config->params->get('ssl'),
            'port'		=> $smtp_config->params->get('port')
        );

        $server        = $smtp_config->get('server');

        $transport = new Zend_Mail_Transport_Smtp($server, $emailConfig);
        Zend_Mail::setDefaultTransport($transport);
        */
        $transport = new Zend_Mail_Transport_Sendmail('webservice@byte-artist.de');
        Zend_Mail::setDefaultTransport($transport);
    }

    protected function _initRouter()
    {
        $frontController = Zend_Controller_Front::getInstance();

        $router = $frontController->getRouter();
        $route = new Zend_Controller_Router_Route_Regex(
            'sitemap(.*)\.xml',
            array(
                'controller' => 'xml',
                'action' => 'create-sitemap'
            ),
            array(
                1 => 'sitemap'
            )
        );

        $router->addRoute('sitemap.xml', $route);

        $route = new Zend_Controller_Router_Route_Regex(
            'feed_*(.*)\.rss',
            array(
                'controller' => 'feed-rss'
            ),
            array(
                '1' => 'action'
            )
        );

        $router->addRoute('feed-rss', $route);

        $route = new Zend_Controller_Router_Route_Regex(
            'feed_*(.*)\.atom',
            array(
                'controller' => 'feed-atom',
            ),
            array(
                1 => 'action'
            )
        );
        $router->addRoute('feed-atom', $route);
    }

    public function _initHelpers() {
//        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH . '/controllers/helpers', 'Action_Helper');
        Zend_Controller_Action_HelperBroker::addHelper(new MessageHelper());
        Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-100, new MessageHelper($this));
    }

    public function _initTranslation() {
        $translationService = new Service_Translator();
        Zend_Registry::set('Zend_Translate', $translationService->getTranslation());
    }

    /**
     *
     */
    protected function _initNavigation()
    {
        /** @var Zend_Translate_Adapter $translator */
        $translator = Zend_Registry::get('Zend_Translate')->getAdapter();
        $structure = array(
            array(
                'label' => $translator->translate('label_admin'),
                'title' => $translator->translate('label_admin'),
                'module' => 'auth',
                'controller' => 'admin',
                'action' => 'index',
                'order' => 90,
                'resource' => 'auth:admin',
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => $translator->translate('label_muscles'),
                        'title' => $translator->translate('label_muscles'),
                        'module' => 'default',
                        'controller' => 'muscles',
                        'action' => 'index',
                        'resource' => 'default:muscles',
                        'privilege' => 'index',
                    ),
                    array(
                        'label' => $translator->translate('label_muscle_groups'),
                        'title' => $translator->translate('label_muscle_groups'),
                        'module' => 'default',
                        'controller' => 'muscle-groups',
                        'action' => 'index',
                        'resource' => 'default:muscle-groups',
                        'privilege' => 'index'
                    ),
                    array(
                        'label' => $translator->translate('label_devices'),
                        'title' => $translator->translate('label_devices'),
                        'module' => 'default',
                        'controller' => 'devices',
                        'action' => 'index',
                        'resource' => 'default:devices',
                        'privilege' => 'index'
                    ),
                    array(
                        'label' => $translator->translate('label_device_groups'),
                        'title' => $translator->translate('label_device_groups'),
                        'module' => 'default',
                        'controller' => 'device-groups',
                        'action' => 'index',
                        'resource' => 'default:device-groups',
                        'privilege' => 'index'
                    ),
                    array(
                        'label' => $translator->translate('label_device_options'),
                        'title' => $translator->translate('label_device_options'),
                        'module' => 'default',
                        'controller' => 'device-options',
                        'action' => 'index',
                        'resource' => 'default:device-options',
                        'privilege' => 'index'
                    ),
                    array(
                        'label' => $translator->translate('label_exercise_options'),
                        'title' => $translator->translate('label_exercise_options'),
                        'module' => 'default',
                        'controller' => 'exercise-options',
                        'action' => 'index',
                        'resource' => 'default:exercise-options',
                        'privilege' => 'index'
                    ),
                    array(
                        'label' => $translator->translate('label_permissions'),
                        'title' => $translator->translate('label_permissions'),
                        'module' => 'auth',
                        'controller' => 'admin',
                        'action' => 'index',
                        'resource' => 'auth:admin',
                        'privilege' => 'index'
                    ),
                ),
            ),
            array(
                'label' => $translator->translate('label_exercises'),
                'title' => $translator->translate('label_exercises'),
                'module' => 'default',
                'controller' => 'exercises',
                'action' => 'index',
                'resource' => 'default:exercises',
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => $translator->translate('label_overview'),
                        'title' => $translator->translate('label_overview'),
                        'module' => 'default',
                        'controller' => 'exercises',
                        'action' => 'index',
                        'resource' => 'default:exercises',
                        'privilege' => 'index',
                    ),
                    array(
                        'label' => $translator->translate('label_new'),
                        'title' => $translator->translate('label_new'),
                        'module' => 'default',
                        'controller' => 'exercises',
                        'action' => 'edit',
                        'resource' => 'default:exercises',
                        'privilege' => 'new'
                    ),
                ),
            ),
            array(
                'label' => $translator->translate('label_training_plans'),
                'title' => $translator->translate('label_training_plans'),
                'module' => 'default',
                'controller' => 'training-plans',
                'action' => 'index',
                'resource' => 'default:training-plans',
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => $translator->translate('label_overview'),
                        'title' => $translator->translate('label_overview'),
                        'module' => 'default',
                        'controller' => 'training-plans',
                        'action' => 'index',
                        'resource' => 'default:training-plans',
                        'privilege' => 'index',
                    ),
                    array(
                        'label' => $translator->translate('label_new'),
                        'title' => $translator->translate('label_new'),
                        'module' => 'default',
                        'controller' => 'training-plans',
                        'action' => 'select-layout',
                        'resource' => 'default:training-plans',
                        'privilege' => 'select-layout'
                    ),
                    array(
                        'label' => 'divider',
                        'uri' => '#',
                        'class' => 'divider',
                        'resource' => 'default:training-plans',
                        'privilege' => 'archive'
                    ),
                    array(
                        'label' => $translator->translate('label_archive'),
                        'title' => $translator->translate('label_archive'),
                        'module' => 'default',
                        'controller' => 'training-plans',
                        'action' => 'archive',
                        'resource' => 'default:training-plans',
                        'privilege' => 'archive'
                    ),
                ),
            ),
            /*
            array(
                'label' => $translator->translate('label_training_diaries'),
                'title' => $translator->translate('label_training_diaries'),
                'module' => 'default',
                'controller' => 'training-diaries',
                'action' => 'index',
                'resource' => 'default:training-diaries',
                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => $translator->translate('label_overview'),
                        'title' => $translator->translate('label_overview'),
                        'module' => 'default',
                        'controller' => 'training-diaries',
                        'action' => 'index',
                        'resource' => 'default:training-diaries',
                        'privilege' => 'index',
                    ),
//                    array(
//                        'label' => $translator->translate('label_new'),
//                        'title' => $translator->translate('label_new'),
//                        'module' => 'default',
//                        'controller' => 'training-diaries',
//                        'action' => 'new',
//                        'resource' => 'default:training-diaries',
//                        'privilege' => 'edit'
//                    ),
//                    array(
//                        'label' => 'divider',
//                        'uri' => '#',
//                        'class' => 'divider',
//                        'resource' => 'default:training-plans',
//                        'privilege' => 'archive'
//                    ),
//                    array(
//                        'label' => $translator->translate('label_archive'),
//                        'title' => $translator->translate('label_archive'),
//                        'module' => 'default',
//                        'controller' => 'training-plans',
//                        'action' => 'archive',
//                        'resource' => 'default:training-plans',
//                        'privilege' => 'archive'
//                    ),
                ),
            ),
            */
            /*
            array(
                'label' => 'Profil',
                'title' => 'Profil',
                'module' => 'default',
                'controller' => 'profil',
                'action' => 'index',
//                'resource' => 'default:profil',
//                'privilege' => 'index',
                'pages' => array(
                    array(
                        'label' => 'Ernährungsplan',
                        'title' => 'Ernährungsplan',
                        'module' => 'default',
                        'controller' => 'meal-plan',
                        'action' => 'index',
//                        'resource' => 'default:meal-plan',
//                        'privilege' => 'index',
                        'pages' => array(
                            array(
                                'label' => 'Hinzufügen',
                                'title' => 'Ernährungsplan hinzufügen',
                                'module' => 'default',
                                'controller' => 'meal-plan',
                                'action' => 'edit',
//                                'resource' => 'default:meal-plan',
//                                'privilege' => 'edit',
                            ),
                        )
                    ),
                ),
            ),
            */
        );

        $navigation = new Zend_Navigation($structure);

        Zend_Registry::set('navigation', $navigation);
    }
}


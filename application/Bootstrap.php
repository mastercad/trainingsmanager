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

        return $autoloader;
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

    protected function _initView()
    {
        $view = new Zend_View();

        $view->headTitle( 'Übungsmanager Rundumfit');
        $view->headTitle()->setSeparator(' | ');

        $view->setEncoding('UTF-8');

//      $view->headMeta()->appendHttpEquiv(
//          'Content-Type', 'charset=utf-8'
// 	);
        $view->env = APPLICATION_ENV;

        $view_renderer = Zend_Controller_Action_HelperBroker::getStaticHelper( 'ViewRenderer');
        $view_renderer->setView( $view);

        Zend_Registry::set('view', $view);

        return $view;
    }

    protected function _initAuth()
    {
        $this->bootstrap('frontController');
        $auth = CAD_Auth::getInstance();

        $authAdapter = new CAD_Auth_Adapter_DbTable(Zend_Registry::get('db'), 'users', 'user_login', 'user_passwort', 'MD5(?)');

        $acl = new Application_Plugin_Auth_Acl();
        Zend_Registry::set('acl', $acl);

        $this->getResource('frontController')->registerPlugin(new Application_Plugin_Auth_AccessControl($auth, $acl))->setParam('auth', $auth);

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

        $logger = new Zend_Log();
        $logger->addWriter($writer);
        $logger->addWriter($writer_mail);

        Zend_Registry::set(ZEND_LOGGER, $logger);
    }

    protected function _initDoctype()
    {
        $view = $this->getResource('view');
        $view->doctype('HTML5');
//          $view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
    }

    protected function _initMeta()
    {
        $view = $this->getResource('view');

        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
//          $this->view->headMeta()->appendHttpEquiv('Content-Language', 'de-DE');
        $view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $view->headMeta()->appendName('robots', 'index, follow');
        $view->headMeta()->appendName('author', 'Andreas Kempe');

        /*
        if(preg_match('/google/i', $_SERVER['HTTP_USER_AGENT']))
        {
//          $view->headMeta()->appendName('author-link', 'https://plus.google.com/u/0/100952657106943880213');
//          $view->headMeta()->appendName('og:url', 'http://www.byte-artist.de');
//          $view->headMeta()->appendName('og:title', $view->title);
//          $view->headMeta()->appendName('og:description', $view->description);
//          $view->headMeta()->appendName('og:image', $view->image);
        }

        if(preg_match('/facebook/i', $_SERVER['HTTP_USER_AGENT']))
        {
//          $view->headMeta()->appendName('fb:admins', '100001360008435');
//          $view->headMeta()->appendName('fb:page_id', '198506923607446');
//          $view->headMeta()->appendName('og:url', 'http://www.byte-artist.de');
//          $view->headMeta()->appendName('og:title', $view->title);
//          $view->headMeta()->appendName('og:description', $view->description);
//          $view->headMeta()->appendName('og:image', $view->image);
        }
        */
    }

    protected function _initHeadScripts()
    {
        $view = $this->getResource('view');
        $user_agent = NULL;
        $obj_device = NULL;

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $obj_user_agent = $view->userAgent();
            $obj_device = $obj_user_agent->getDevice();
        }

        $view->headScript()->prependFile($view->baseUrl() . '/js/jquery.min.js', 'text/javascript');
//      $view->headScript()->prependFile($view->baseUrl() . '/js/jquery.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/jquery-ui.min.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery-ui.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery.sharrre-1.3.4.min.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery_counts.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/funktionen.min.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/funktionen.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/funktionen_jquery.min.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/funktionen_jquery.js', 'text/javascript');

        if(TRUE === is_object($obj_device)
            && $obj_device->getType() == "desktop"
        ) {
            $view->headScript()->appendFile($view->baseUrl() . '/js/blur.js', 'text/javascript');
        }
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery.snippet.min.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad_wrapper.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad_catch_esc.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad_cms.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad_sperre.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad_message.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/cad_loader.js', 'text/javascript');
//      $view->headScript()->appendFile($view->baseUrl() . '/js/jquery-ui-1.10.1.custom.min.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/default.js', 'text/javascript');
        $view->headScript()->appendFile($view->baseUrl() . '/js/html5.js', 'text/javascript', array('conditional' => 'lt IE 9',));
//      $view->headScript()->appendFile('https://apis.google.com/js/plusone.js', 'text/javascript');
    }

    protected function _initLink()
    {
        $view = Zend_Registry::get('view');

        $user_agent = NULL;
        $obj_device = NULL;

        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            $obj_user_agent = $view->userAgent();
            $obj_device = $obj_user_agent->getDevice();
        }

        $view->headLink()->appendAlternate($view->baseUrl() . 'http://gmpg.org/xfn/11', 'text/html', true, array('rel' => 'profile', 'title' => 'XFN Profile Version for Meta Markup'));
//      $view->headLink()->appendAlternate($view->baseUrl() . 'https://plus.google.com/100952657106943880213', 'text/html', true, array( 'rel' => 'publisher', 'title' => 'Herausgeber dieser Webseite'));
        $view->headLink()->appendAlternate($view->baseUrl() . '/feed-rss', 'application/rss+xml', 'News-Feed im RSS Format');
        $view->headLink()->appendAlternate($view->baseUrl() . '/feed-atom', 'application/atom+xml', 'News-Feed im Atom Format');

        if(TRUE === is_object($obj_device)
            && $obj_device->getType() == "desktop"
        ) {
            $view->headLink()->prependStylesheet($view->baseUrl() . '/css/global.css', 'screen', true);
            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/normal.css', 'screen', true, array('title' => 'normal'));
        }
        else if(TRUE === is_object($obj_device)
            && $obj_device->getType() == "mobile"
        ) {
            $view->headLink()->prependStylesheet($view->baseUrl() . '/css/mobile.global.css', 'screen', true);
        }
//        $view->headLink()->appendStylesheet($view->baseUrl() . '/css/grau.css', 'screen', true, array('title' => 'grau', 'rel' => 'alternate stylesheet'));

        // wenn eine extension wie firephp installiert ist, rutscht der browser
        // name eins weiter
        if(preg_match('/.*?(firefox)\/([0-9\+\.\,]*).*?/i', $user_agent, $a_treffer))
        {
            $browser = $a_treffer[1];
            $version = $a_treffer[2];

            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/mozilla.css', 'screen', true);
        }
        else if(preg_match('/.*?(rekonq)\/([0-9\+\.\,]*).*?/i', $user_agent, $a_treffer))
        {
            $browser = $a_treffer[1];
            $version = $a_treffer[2];

            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/ubuntu_konqueror.css', 'screen', true);
        }
        else if(preg_match('/.*?(konqueror)\/([0-9\+\.\,]*).*?/i', $user_agent, $a_treffer))
        {
            $browser = $a_treffer[1];
            $version = $a_treffer[2];

            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/konqueror.css', 'screen', true);
        }
        else if(preg_match('/.*?(chrome)\/([0-9\+\.\,]*).*?/i', $user_agent, $a_treffer))
        {
            $browser = $a_treffer[1];
            $version = round($a_treffer[2]);

            if(10 <= $version)
            {
                $view->headLink()->appendStylesheet($view->baseUrl() . '/css/chrome_ab_10.css', 'screen', true);
            }
            else
            {
                $view->headLink()->appendStylesheet($view->baseUrl() . '/css/chrome_ab_2.css', 'screen', true);
            }
        }
        else if(preg_match('/.*?(opera)\/([0-9\+\.\,]*).*?/i', $user_agent, $a_treffer))
        {
            $browser = $a_treffer[1];
            $version = $a_treffer[2];

            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/opera.css', 'screen', true);
        }
        else if(preg_match('/.*?(MSIE)\/([0-9\+\.\,]*).*?/i', $user_agent, $a_treffer))
        {
            $browser = $a_treffer[1];
            $version = $a_treffer[2];

            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/opera.css', 'screen', true);
        }
        elseif (true === is_object($obj_device)
            && $obj_device->getType() !== 'mobile'
        ) {
            $view->headLink()->appendStylesheet($view->baseUrl() . '/css/effekte.css', 'screen', true);
        }
    }

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

        $route = new Zend_Controller_Router_Route_Regex(
            'blog/(\d{4})/(\d{2})/(\d{2})/([a-zA-Z0-9-_]*)/*(.*)',
            array(
                'controller' => 'blog',
                'action' => 'show'
            ),
            array(
                1 => 'jahr',
                2 => 'monat',
                3 => 'tag',
                4 => 'seo_link',
                5 => 'params'
            )
        );

        /*
        $route = new Zend_Controller_Router_Route_Regex(
            'blog/:jahr/:monat/:tag/:title/*',
            array(
                            'controller' => 'blog',
                            'action' => 'show',
                            'jahr' => date("Y"),
                            'monat' => date("m"),
                            'tag' => date("d")
            ),
            array(
                            'jahr' => '\d{4}',
                            'monat' => '\d{2}',
                            'tag' => '\d{2}',
                            'title' => '[a-zA-Z0-9-_]+',
                            'params' => '(.*)'
            )
        );
        */
        $router->addRoute('blog-show', $route);
    }
}


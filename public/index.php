<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH),
    get_include_path(),
)));

function customAutoload($class) {
    $map = [
        'Entity' => 'entities',
        'Collection' => 'collections',
        'Interface' => 'interfaces',
        'Service' => 'services',
        'Model' => 'models',
    ];

    $path = preg_split('/\_/', $class);
    $replacedPath = APPLICATION_PATH;

    foreach ($path as $pathPiece) {
        if (array_key_exists($pathPiece, $map)) {
            $pathPiece = str_replace(array_keys($map), array_values($map), $pathPiece);
            $replacedPath .= '/' . $pathPiece;
        } else {
            $replacedPath .= '/' . $pathPiece;
        }
    }
    $replacedPath .= '.php';

    if (is_readable($replacedPath)) {
        return require_once($replacedPath);
    }
    return false;
}

spl_autoload_register('customAutoload');

/** Zend_Application */
require_once 'Zend/Application.php';
//require_once APPLICATION_PATH . '/../vendor/autoload.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
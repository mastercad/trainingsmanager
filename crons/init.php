<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 15.05.17
 * Time: 19:20
 */

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

    if (false === strpos($class, 'Zend')) {
        $map = [
            'Entity' => 'entities',
            'Collection' => 'collections',
            'Interface' => 'interfaces',
            'Service' => 'services',
            'Model' => 'models',
        ];

        if (false !== (strpos($class, 'Helper'))) {
            $classFilePathName = APPLICATION_PATH.'/controllers/helpers/'.$class.'.php';
            if (is_readable($classFilePathName)) {
                require_once $classFilePathName;
                return true;
            }
        }

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
            require_once $replacedPath;
            return true;
        }

        return false;
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

try {
    $application->bootstrap();
} catch (Exception $exception) {
    echo $exception->getMessage();
}

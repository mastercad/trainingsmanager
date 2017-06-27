<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

require_once realpath(APPLICATION_PATH . '/../vendor/autoload.php');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
//    realpath(APPLICATION_PATH),
    get_include_path(),
)));

function customAutoload($class) {

    if (false === strpos($class, 'Zend')) {
        $modules = [];
        $modulesDirectoryIterator = new DirectoryIterator(APPLICATION_PATH.'/modules');
        foreach ($modulesDirectoryIterator as $moduleDirectory) {
            if (!$moduleDirectory->isDot()
                && $moduleDirectory->isDir()
            ) {
                $modules[strtoupper($moduleDirectory->getBasename())] = $moduleDirectory->getBasename();
            }
        }

        $map = [
            'Entity' => 'entities',
            'Collection' => 'collections',
            'Interfaces' => 'interfaces',
            'Interface' => 'interfaces',
            'Service' => 'services',
            'Model' => 'models',
            'Plugin' => 'plugins',
            'Role' => 'roles',
            'Resource' => 'resources',
            'Assertion' => 'assertions'
        ];

        if (false !== (strpos($class, 'Helper'))) {
            $classFilePathName = APPLICATION_PATH.'/controllers/helpers/'.$class.'.php';
            if (is_readable($classFilePathName)) {
                require_once $classFilePathName;
                return true;
            }
        }

        $path = (false !== strpos($class, '_')) ? explode('_', $class) : explode('\\', $class);
        $replacedPath = APPLICATION_PATH;
        $moduleDirectoryFound = false;
        foreach ($path as $pathPiece) {
            if (array_key_exists($pathPiece, $map)) {
                $pathPiece = str_replace(array_keys($map), array_values($map), $pathPiece);
                $replacedPath .= '/' . $pathPiece;
            } else if (!$moduleDirectoryFound
                && array_key_exists(strtoupper($pathPiece), $modules)
            ) {
                $replacedPath .= '/modules/' . $modules[strtoupper($pathPiece)];
                $moduleDirectoryFound = true;
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

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

try {
    $application->bootstrap()->run();
} catch (Exception $exception) {
    echo $exception->getMessage();
}

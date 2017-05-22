<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 30.04.15
 * Time: 22:32
 */

class CAD_Tool_ModuleControllerActionLister {

    /**
     * einsprungs funktion, sammelt alle informationen über module, controller und actions
     * ausgehend vom Application_Path eines projektes
     *
     * @return mixed
     */
    static public function collect() {
        $aData = array();
        $aData['default'] = self::_collectAllKnownControllersInModule();
        return self::_collectAllModules($aData);
    }

    /**
     * listet alle module eines projektes
     *
     * @param $aData
     *
     * @return mixed
     */
    static private function _collectAllModules(&$aData) {
        if (true === file_exists(APPLICATION_PATH . '/modules')
            && true === is_dir(APPLICATION_PATH . '/modules')
        ) {
            $oDirectoryIterator = new DirectoryIterator(APPLICATION_PATH . '/modules');

            foreach ($oDirectoryIterator as $oFile) {
                if (TRUE === $oFile->isDir()
                    && FALSE === $oFile->isDot()
                ) {
                    $sModuleName = self::_convertCamelCaseAction($oFile->getFilename());
                    $aData[$sModuleName] = self::_collectAllKnownControllersInModule('modules/' . $oFile->getFilename());
                }
            }
        }
        ksort($aData);
        return $aData;
    }

    /**
     * listet alle controller zu einem modul
     *
     * @param string $sModule
     *
     * @return array
     */
    static private function _collectAllKnownControllersInModule($sModule = '') {
        $aData = array();
        $oDirectory = new DirectoryIterator(APPLICATION_PATH . '/' . $sModule . '/controllers');
        foreach ($oDirectory as $oFile) {
            if (preg_match('/(.*?)Controller\.php$/i', $oFile->getFilename(), $aMatches)) {
                $sControllerName = self::_convertCamelCaseAction($aMatches[1]);
                if (false !== ($actions = self::_collectAllActionsFromController($oFile->getPathname()))) {
                    $aData[$sControllerName] = $actions;
                }
            }
        }
        ksort($aData);
        return $aData;
    }

    /**
     * listet alle actions zu einem controller
     *
     * @param $sControllerPath
     *
     * @return array
     */
    static private function _collectAllActionsFromController($sControllerPath) {
        $aData = [];
        $className = static::extractClassName($sControllerPath);
        if (!class_exists($className)) {
            include($sControllerPath);
        }
        $class = new ReflectionClass($className);
        if ($class->isAbstract()) {
            return false;
        }
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (preg_match('/(.*?)Action/i', $method->getName(), $matches)) {
                $sAction = self::_convertCamelCaseAction($matches[1]);
                $aData[] = $sAction;
            }
        }
        sort($aData);
        return $aData;
    }

    static private function _convertCamelCaseAction($sActionName) {
        $oFilter = new Zend_Filter_Word_CamelCaseToDash();
        return strtolower($oFilter->filter($sActionName));
    }

    static private function extractClassName($classPathName) {
        $sControllerContent = file_get_contents($classPathName);
        if (preg_match('/class ([A-Z\_]+) /i', $sControllerContent, $matches)) {
            return $matches[1];
        }
        return false;
    }
}

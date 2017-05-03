<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 27.04.15
 * Time: 18:28
 */

final class CAD_Tool_Extender {

    /**
     * öffentliche einstiegsfunktion, hier kann ein container übergeben werden, muss aber nicht,
     * es kommt in jedem falle einer zurück, hier ist noch denkbar, das man eine klasse als
     * statisches member setzt, zur zeit sind objecte base vom typ stdClass
     *
     * der pfad steuert ob ein array oder ein object angelegt wird, besitzt der aktuelle key beginnend
     * und endend eckige klammern wird ein array an der stelle abgelegt, sonst wird ein object erzeugt
     *
     * @param string $sPath
     * @param mixed $mValue
     * @param array|stdClass|null $mContainer
     *
     * @return array|stdClass|null
     */
    public static function extendOverPath($sPath, $mValue, &$mContainer = null) {
        $aPath = self::_generateArrayFromPath($sPath);
        $sCurrentKey = array_shift($aPath);

        if (false !== ($sNewCurrentKey = self::_extractArrayKey($sCurrentKey))
            || true === is_array($mContainer)
        ) {
            if (false === $sNewCurrentKey) {
                $sNewCurrentKey = $sCurrentKey;
            }
            $mContainer = self::_extendArrayOverPath($sNewCurrentKey, $aPath, $mValue, $mContainer);
        } else {
            $mContainer = self::_extendObjectOverPath($sCurrentKey, $aPath, $mValue, $mContainer);
        }
        return $mContainer;
    }

    /**
     * liefert einen array key aus der übergabe oder false
     *
     * ein array key wird an der beginnenden und endenden eckigen klammer erkannt
     *
     * @param string $sString
     *
     * @return bool|string
     */
    private static function _extractArrayKey($sString) {
        $mReturn = false;
        if (preg_match('/^\[(.*?)\]$/i', $sString, $aMatches)) {
            $mReturn = $aMatches[1];
        }
        return $mReturn;
    }

    /**
     * diese funktion erweitert das übergeben object an der position $sKey um ein array
     *
     * @param string $sKey
     * @param array $aPath
     * @param mixed $mValue
     * @param null|array|stdClass $mContainer
     *
     * @return array
     */
    private static function _extendArrayOverPath($sKey, $aPath, $mValue, &$mContainer = null) {
        $iPathDepth = count($aPath);
        $mContainer = self::_prepareArray($mContainer, $sKey);
        if (0 < $iPathDepth) {
            $mContainer[$sKey] = self::extendOverPath(self::_generatePathFromArray($aPath), $mValue, $mContainer[$sKey]);
        } else {
            $mContainer[$sKey] = $mValue;
        }
        return $mContainer;
    }

    /**
     * diese funktion erweitert das übergebene object an der position $sKey um ein object
     *
     * @param string $sKey
     * @param array $aPath
     * @param mixed $mValue
     * @param null|array|stdClass $mContainer
     *
     * @return stdClass
     */
    private static function _extendObjectOverPath($sKey, $aPath, $mValue, &$mContainer = null) {
        $iPathDepth = count($aPath);
        $mContainer = self::_prepareObject($mContainer, $sKey);
        if (0 < $iPathDepth) {
            $mContainer->{$sKey} = self::extendOverPath(self::_generatePathFromArray($aPath), $mValue, $mContainer->{$sKey});
        } else {
            $mContainer->{$sKey} = $mValue;
        }
        return $mContainer;
    }

    /**
     * diese funktion gibt ein array zurück, welches an position $sKey initialisiert wurde
     *
     * @param array $aContainer
     * @param string $sKey
     *
     * @return array
     */
    private static function _prepareArray(&$aContainer, $sKey) {
        if (false === is_array($aContainer)) {
            $aContainer = array();
        }
        if (false === array_key_exists($sKey, $aContainer)) {
            $aContainer[$sKey] = null;
        }
        return $aContainer;
    }

    /**
     * diese funktion gibt ein object zurücke, welches ein initialisiertes member $sKey hat
     *
     * @param stdClass $oContainer
     * @param string $sKey
     *
     * @return stdClass
     */
    private static function _prepareObject($oContainer, $sKey) {
        if (false === is_object($oContainer)) {
            $oContainer = new stdClass();
        }
        if (false === isset($oContainer->{$sKey})) {
            $oContainer->{$sKey} = null;
        }
        return $oContainer;
    }

    /**
     * diese funktion generiert aus einem Array einen string
     *
     * @param array $aPath
     *
     * @return string
     */
    private static function _generatePathFromArray($aPath) {
        return implode('->', $aPath);
    }

    /**
     * diese funktion generiert aus einem string ein Array
     *
     * @param string $sPath
     *
     * @return array
     */
    private static function _generateArrayFromPath($sPath) {
        return explode('->', $sPath);
    }
}

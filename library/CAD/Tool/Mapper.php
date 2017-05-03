<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 30.05.15
 * Time: 00:03
 */

class CAD_Tool_Mapper {

    const TYPE_ARRAY = 'Array';
    const TYPE_OBJECT = 'Object';
    const TYPE_STD_CLASS = 'ZendView';
    const TYPE_ZEND_VIEW = 'ZendView';

    static private $_sSourceType = null;
    static private $_sDestinationType = null;

    static private $_sExtractorClassName = 'CAD_Tool_Extractor';
    static private $_sExtenderClassName = 'CAD_Tool_Extender';

    static private $_mSource = null;
    static private $_mDestination = null;
    static private $_aMap = null;

    static private $_mValue = null;

    /**
     * Einsprungsfunktion des mappers, es wird die Herkunft, das Ziel sowie die Map der Daten übergeben
     *
     * @param mixed $mSource
     * @param mixed $mDestination
     * @param array $aMap
     *
     * @return mixed
     */
    static public function process($mSource, $mDestination, $aMap) {
        self::_setSource($mSource);
        self::_setDestination($mDestination);
        self::_setMap($aMap);

        self::_init();

        self::_map();

        return self::_getDestination();
    }

    /**
     * löscht alle member der klasse initial und bereitet die typen der übergaben vor
     */
    static private function _init() {
        self::_setDestinationType(null);
        self::_setSourceType(null);
        self::_setValue(null);

        self::_prepareTypes();
    }

    /**
     * hier werden die typen der beiden übergaben gesetzt, falls eines der beiden übergaben nicht identifiziert
     * werden konnte, gibt es eine Exception
     *
     * @throws InvalidArgumentException
     */
    static private function _prepareTypes() {

        self::_setSourceType(self::_identifyType(self::_getSource()));
        self::_setDestinationType(self::_identifyType(self::_getDestination()));

        if (false === self::_getSourceType()) {
            throw new InvalidArgumentException('der Type von Source wird nicht akzeptiert!');
        }
        if (false === self::_getDestinationType()) {
            throw new InvalidArgumentException('der Type von Destination wird nicht akzeptiert!');
        }
    }

    /**
     * zentrale private funktion des services, hier findet das eigentliche mappen statt
     */
    static private function _map() {
        $aMap = self::_getMap();

        if (true === is_array($aMap)) {
            foreach ($aMap as $sSourceKey => $sDestinationKey) {
                $sSourceGetter = self::_generateGetterFunctionName($sSourceKey);
                $sDestinationSetter = self::_generateSetterFunctionName($sDestinationKey);

                if (true === call_user_func('CAD_Tool_Mapper::' . $sSourceGetter, $sSourceKey)) {
                    call_user_func('CAD_Tool_Mapper::' . $sDestinationSetter, $sDestinationKey);
                }
            }
        }
    }

    static private function _generateGetterFunctionName($sKey) {
        if (false === empty(self::$_sExtractorClassName)
            && 0 < strpos($sKey, '->')
        ) {
            return '_getOverExtractor';
        }
        return '_getFrom' . self::_getSourceType();
    }

    static private function _generateSetterFunctionName($sKey) {
        if (false === empty(self::$_sExtenderClassName)
            && 0 < strpos($sKey, '->')
        ) {
            return '_setOverExtender';
        }
        return '_setTo' . self::_getDestinationType();
    }

    static private function _getOverExtractor($sKey) {
        self::_setValue(call_user_func_array(
            array(self::$_sExtractorClassName, 'extractOverPath'), array(self::_getSource(), $sKey)));
        return true;
    }

    static private function _setOverExtender($sKey) {
        $mDestination = &self::_getDestination();
        $mDestination = call_user_func_array(
            array(self::$_sExtenderClassName, 'extendOverPath'), array($sKey, self::_getValue(), &$mDestination));
        self::_setDestination($mDestination);
    }

    /**
     * @param $sKey
     *
     * @return bool
     */
    static private function _getFromObject($sKey) {
        $sGetterFunction = 'get'. $sKey;
        $bReturn = false;
        if (true === method_exists(self::_getSource(), $sGetterFunction)) {
            self::_setValue(self::_getSource()->{$sGetterFunction}());
            $bReturn = true;
        } else {
            $bReturn = self::_getFromMember($sKey);
        }
        return $bReturn;
    }

    /**
     * @param $sKey
     */
    static private function _setToObject($sKey) {
        $sSetterFunction = 'set'. $sKey;
        if (true === method_exists(self::_getDestination(), $sSetterFunction)) {
            self::_getDestination()->{$sSetterFunction}(self::_getValue());
        } else {
            self::_setToMember($sKey);
        }
    }

    /**
     * @param $sKey
     *
     * @return bool
     */
    static private function _getFromArray($sKey) {
        $mData = self::_getSource();
        $bReturn = false;
        if ((true == is_array($mData)
                && true === array_key_exists($sKey, $mData))
            || ($mData instanceof ArrayAccess
                && true === $mData->offsetExists($sKey))
        ) {
            self::_setValue($mData[$sKey]);
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * @param $sKey
     */
    static private function _setToArray($sKey) {
        $aData = self::_getDestination();
        if (true === is_array($aData)) {
            $aData[$sKey] = self::_getValue();
            self::_setDestination($aData);
        }
    }

    /**
     * @param $sKey
     *
     * @return bool
     */
    static private function _getFromZendView($sKey) {
        $bReturn = false;
        if (true === method_exists(self::_getSource(), $sKey)) {
            self::_setValue($mReturn = self::_getSource()->{$sKey}());
            $bReturn = true;
        } else {
            $bReturn = self::_getFromMember($sKey);
        }
        return $bReturn;
    }

    /**
     * @param $sKey
     */
    static private function _setToZendView($sKey) {
        self::_getDestination()->{$sKey} = self::_getValue();
    }

    /**
     * @param $sKey
     *
     * @return bool
     */
    static private function _getFromMember($sKey) {
        $bReturn = false;
        if (true === self::_checkPropertyExists(self::_getSource(), $sKey)) {
            self::_setValue(self::_getSource()->{$sKey});
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * @param $sKey
     */
    static private function _setToMember($sKey) {
        if (true === self::_checkPropertyExists(self::_getDestination(), $sKey)) {
            self::_getDestination()->{$sKey} = self::_getValue();
        }
    }

    /**
     * identifiziert das übergebene Object und gibt den identifizierten Typ fürs mappen zurück
     *
     * @param $mData
     *
     * @return bool|string
     */
    static private function _identifyType($mData) {
        $mReturn = false;

        if (true === is_array($mData)
            || $mData instanceof ArrayAccess
        ) {
            $mReturn = self::TYPE_ARRAY;
        } else if ($mData instanceof Zend_View_Abstract) {
            $mReturn = self::TYPE_ZEND_VIEW;
        } else if ($mData instanceof stdClass) {
            $mReturn = self::TYPE_STD_CLASS;
        } else if (true === is_object($mData)) {
            $mReturn = self::TYPE_OBJECT;
        }
        return $mReturn;
    }

    /**
     * checkt ob die gesuchte property in der klasse existiert und ob sie public ist
     *
     * @param $oClass
     * @param $sProperty
     *
     * @return bool
     */
    static private function _checkPropertyExists($oClass, $sProperty) {
        $bReturn = false;
        if (true === property_exists($oClass, $sProperty)) {
            $oReflection = new ReflectionProperty($oClass, $sProperty);

            if ($oReflection->isPublic()) {
                $bReturn = true;
            }
        }
        return $bReturn;
    }

    /**
     * @return array
     */
    static private function _getMap() {
        return self::$_aMap;
    }

    /**
     * @param array $aMap
     */
    static private function _setMap($aMap) {
        self::$_aMap = $aMap;
    }

    /**
     * @return mixed
     */
    static private function _getDestination() {
        return self::$_mDestination;
    }

    /**
     * @param mixed $mDestination
     */
    static private function _setDestination($mDestination) {
        self::$_mDestination = $mDestination;
    }

    /**
     * @return mixed
     */
    static private function _getSource() {
        return self::$_mSource;
    }

    /**
     * @param mixed $mSource
     */
    static private function _setSource($mSource) {
        self::$_mSource = $mSource;
    }

    /**
     * @return string
     */
    static private function _getDestinationType() {
        return self::$_sDestinationType;
    }

    /**
     * @param string $sDestinationType
     */
    static private function _setDestinationType($sDestinationType) {
        self::$_sDestinationType = $sDestinationType;
    }

    /**
     * @return string
     */
    static private function _getSourceType() {
        return self::$_sSourceType;
    }

    /**
     * @param string $sSourceType
     */
    static private function _setSourceType($sSourceType) {
        self::$_sSourceType = $sSourceType;
    }

    static private function _getValue() {
        return self::$_mValue;
    }

    static private function _setValue($mValue) {
        self::$_mValue = $mValue;
    }
}
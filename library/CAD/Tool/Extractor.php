<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 03.04.15
 * Time: 11:16
 */

class CAD_Tool_Extractor
{
    /**
     * Statische Funktion über die über einen Pfad auf ein beliebiges Object rekursiv
     * ein wert extrahiert werden kann
     *
     * @param null|string|Zend_Db_Table_Rowset_Abstract $mObject
     * @param null $sPath
     * @param null $mDefault
     *
     * @return mixed
     */
    public static function extractOverPath($mObject, $sPath = null, $mDefault = null)
    {
        $mReturn = $mDefault;

        try {
            if (0 < strlen(trim($sPath))) {
                $aPath = explode('->', $sPath);
                $sCurrentKey = array_shift($aPath);
                $sPath = implode('->', $aPath);

                if ((true === is_array($mObject)
                        && true === array_key_exists($sCurrentKey, $mObject))
                    || ($mObject instanceof ArrayObject
                        && true === isset($sCurrentKey, $mObject))
                ) {
                    return self::extractOverPath($mObject[$sCurrentKey], $sPath, $mDefault);
                } else if (true === is_object($mObject)) {
                    if (true === method_exists($mObject, $sCurrentKey)) {
                        return self::extractOverPath($mObject->{$sCurrentKey}(), $sPath, $mDefault);
                    } else if (true === isset($mObject->{$sCurrentKey})) {
                        return self::extractOverPath($mObject->{$sCurrentKey}, $sPath, $mDefault);
                    }
                }
            // wenn es keinen pfad mehr gibt, davon ausgehen das ich die unterste ebene erreicht habe
            // dann geben wir hier den zuletzt gefundenen wert zurück
            } else {
                return $mObject;
            }
        } catch (Exception $oException) {
            // hier braucht nichts passieren, $mReturn wird eine zeile weiter eh übergeben
            // hier soll nur ein eventuell nicht geplanter fehler abgefangen werden
        }

        return $mReturn;
    }
}

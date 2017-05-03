<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.06.15
 * Time: 20:57
 */

class CAD_Tool_TemplateHandler
{
    public function replace($sTemplateContent, $aReplacements, $sPrevNodeName = null) {
        $oXml = simplexml_load_string($sTemplateContent);
        foreach ($aReplacements as $sPlaceholder => $mReplacement) {
            if (true === is_numeric($sPlaceholder)) {
                $sNewContent = $this->replace($sTemplateContent, $mReplacement);
                $oNewXmlNode = simplexml_load_string($sNewContent);
                $oXml->addChild($sPrevNodeName, $oNewXmlNode);
            } else if (true === isset($oXml->{$sPlaceholder})) {
                if (true === is_array($mReplacement)) {
                    $sNewContent = $this->replace($oXml->{$sPlaceholder}->saveXml(), $mReplacement, $sPlaceholder);
                    $oNewXmlNode = simplexml_load_string($sNewContent);
                    $oXml->{$sPlaceholder} = $oNewXmlNode;
                } else {
                    $oXml->{$sPlaceholder} = $mReplacement;
                }
            }
        }
        return $oXml->saveXML();
    }

    public function finalizeTemplateContent($sTemplateContent) {
        $sTemplateContent = preg_replace('/\[%.*?%\]/', '', $sTemplateContent);

        return $sTemplateContent;
    }

//    private function _extendCurrentPath($sPathPiece) {
//        $bReturn = false;
//
//        if (true === is_string($sPathPiece)) {
//            if (0 < strlen($this->_sCurrentPath)) {
//                $this->_sCurrentPath .= "_" . $sPathPiece;
//            } else {
//                $this->_sCurrentPath = $sPathPiece;
//            }
//        }
//        return $bReturn;
//    }

//    private function _removeLastPieceOfCurrentPath() {
//        $bReturn = false;
//
//        if (0 < preg_match('/(.*?)\_.*$/', $this->_sCurrentPath, $aMatches)) {
//            $this->_sCurrentPath = $aMatches[1];
//            $bReturn = true;
//        }
//        return $bReturn;
//    }

//    private function _extractLastPieceOfCurrentPath() {
//        $mReturn = false;
//
//        if (0 < preg_match('/.*\_(.*?)$/', $this->_sCurrentPath, $aMatches)) {
//            $mReturn = $aMatches[1];
//        }
//        return $mReturn;
//    }

//    private function _replaceLoopContent($sLoopContent, $aReplacements) {
//        $sContent = '';
//        $sLastPathPiece = $this->_extractLastPieceOfCurrentPath();
//        // hier liegen untereinander die daten drin
//        foreach ($aReplacements as $sPlaceholder => $aReplacement) {
//            $sTempLoopContent = $sLoopContent;
//            $sContent .= $this->replace($sTempLoopContent, $aReplacement);
//        }
//        return $sContent;
//    }

    /**
     * bekommt einen string übergeben und reinigt den beginn aller keys
     * der aktuellen ebene des arrays davon
     *
     * ist warscheinlich mit der aktuellen umsetzung obsolete
     *
     * @param string $sNameSpace aktueller namespace (pfad im template)
     * @param array $aReplacements inhalt des Loops dessen keys bereinigt werden müssen
     *
     * @return array $aCleanReplacements
     */
//    private function _cleanSubArrayFromCurrentNameSpace($sNameSpace, $aReplacements) {
//        $aCleanReplacements = array();
//
//        foreach ($aReplacements as $sKey => $mValue) {
//            $sNewKey = preg_replace('/^' . $sNameSpace . '/', '', $sKey);
//            $aCleanReplacements[$sNewKey] = $mValue;
//        }
//        return $aCleanReplacements;
//    }

    /**
     * holt aus dem übergebenen Content den TagString des Namespaces
     *
     * @param string $sContent inhalt in dem gesucht wird
     * @param string $sNameSpace name nach dem gesucht wird (namespace des tags)
     *
     * @return string
     */
//    private function _extractString($sContent, $sNameSpace) {
//        $sReturnString = '';
//
//        if (preg_match('/<' . $sNameSpace . '>(.*?)<\/' . $sNameSpace . '>/si', $sContent, $aMatches)) {
//            $sReturnString = trim($aMatches[1]);
//        }
//        return $sReturnString;
//    }
}
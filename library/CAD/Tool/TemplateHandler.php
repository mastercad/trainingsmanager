<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.06.15
 * Time: 20:57
 */

class CAD_Tool_TemplateHandler
{
    private $_sCurrentPath = '';

    public function replace($aReplacements, $sTemplateContent = null) {
        if (true === empty($sTemplateContent)) {
            $sTemplateContent = $this->_generateXmlByStruct($aReplacements);
        } else {
            $sTemplateContent = $this->_replaceByTemplateContent($sTemplateContent, $aReplacements);
        }
        return $sTemplateContent;
    }

    private function _generateXmlByStruct($aStruct, $bIsLoop = false, $iLevel = -1) {
        $sContent = '';
        $iLevel++;
        foreach ($aStruct as $sKey => $mSubStruct) {
            if (true === is_integer($sKey)) {
                $sContent .= $this->_generateLoopContentByStruct($aStruct, $iLevel - 1);
                $this->_sCurrentPath = '';
                break;
            } else {
//                if (false === $bIsLoop) {
                    $this->_sCurrentPath = $sKey;
//                }
                if (true === is_array($mSubStruct)) {
                    $sContent .= str_repeat(' ', (4 * $iLevel)) . '<' . $sKey . $iLevel . '>' . PHP_EOL .
                        $this->_generateXmlByStruct($mSubStruct, $bIsLoop, $iLevel) . PHP_EOL .
                        str_repeat(' ', (4 * $iLevel)) . '</' . $sKey . '>' . PHP_EOL;
                } else {
                    $sContent .= str_repeat(' ', (4 * $iLevel)) . '<' . $sKey . '>' . $mSubStruct .
                        '</' . $sKey . '>' . PHP_EOL;
                }
            }
        }
        --$iLevel;
        return $sContent;
    }

    private function _replaceByTemplateContent($sTemplateContent, $aReplacements, $bIsAlreadyLoop = false) {
        $bLoopFound = false;
        $sLoopContent = '';
        foreach ($aReplacements as $sPlaceholder => $mReplacement) {
            if (true === is_array($mReplacement)) {
                $this->_extendCurrentPath($sPlaceholder);
                if (false === $bIsAlreadyLoop
                    && true === is_int($sPlaceholder)
                ) {
                    $sCurrentContent = $this->_extractLoopContent($sTemplateContent, $sPlaceholder);
                    $sLoopContent = $this->_generateLoopContent($sCurrentContent, $aReplacements);
                    $bLoopFound = true;
                } else {
                    $sTemplateContent = $this->_replaceByTemplateContent($sTemplateContent, $mReplacement);
                }
            } else {
                $sCurrentPlaceholder = $this->_sCurrentPath;
                if (0 < strlen($sCurrentPlaceholder)) {
                    $sCurrentPlaceholder .= '_';
                }
                $sCurrentPlaceholder .= $sPlaceholder;
                $sTemplateContent = preg_replace(
                    '/\[%' . $sCurrentPlaceholder . '%\]/i', $mReplacement, $sTemplateContent
                );
            }

            if (true === $bLoopFound) {
                $sLastPlaceholder = $this->_extractLastPieceOfCurrentPath();
                $sTemplateContent = preg_replace(
                    '/(.*?)<' . $sLastPlaceholder . '>.*?<\/' . $sLastPlaceholder . '>(.*)/si', "$1" . $sLoopContent . "$2", $sTemplateContent
                );
                $this->_removeLastPieceOfCurrentPath();
                break;
            }
        }
        return $sTemplateContent;
    }

    private function _extendCurrentPath($sPathPiece) {
        $bReturn = false;

        if (true === is_string($sPathPiece)) {
            if (0 < strlen($this->_sCurrentPath)) {
                $this->_sCurrentPath .= "_" . $sPathPiece;
            } else {
                $this->_sCurrentPath = $sPathPiece;
            }
        }
        return $bReturn;
    }

    private function _removeLastPieceOfCurrentPath() {
        $bReturn = false;

        if (0 < preg_match('/(.*)\_.*?$/', $this->_sCurrentPath, $aMatches)) {
            $this->_sCurrentPath = $aMatches[1];
            $bReturn = true;
        }
        return $bReturn;
    }

    private function _extractLastPieceOfCurrentPath() {
        $mReturn = false;

        if (0 < preg_match('/.*\_(.*?)$/', $this->_sCurrentPath, $aMatches)) {
            $mReturn = $aMatches[1];
        }
        return $mReturn;
    }

    public function finalizeTemplateContent($sTemplateContent) {
        $sTemplateContent = preg_replace('/\[%.*?%\]/', '', $sTemplateContent);
        return $sTemplateContent;
    }

    private function _generateLoopContent($sLoopContent, $aReplacements)
    {
        $sReturnContent = '';

        foreach ($aReplacements as $mKey => $mReplacement) {
            $sCurrentLoopContent = $sLoopContent;
            if (true === is_array($mReplacement)) {
                if (true === empty($sLoopContent)) {
                    $sReturnContent .= $this->_generateLoopContentByStruct($mReplacement);
                } else {
                    $sReturnContent .= $this->_replaceByTemplateContent($sCurrentLoopContent, $mReplacement, true);
                }
            }
        }
        return $sReturnContent;
    }

    private function _generateLoopContentByStruct($aReplacement, $iLevel = 0) {
        $sReturnContent = '';
        $iCount = 1;
        foreach ($aReplacement as $sKey => $mReplacement) {
            if (true === is_array($mReplacement)) {
                $sReturnContent .= $this->_generateXmlByStruct($mReplacement, true, $iLevel);
            } else {
                $sReturnContent .= str_repeat(' ', 4 * ($iLevel + 1)) . $mReplacement;
            }

            if ($iCount < count($aReplacement)) {
                $sReturnContent .= PHP_EOL . str_repeat(' ', 4 * $iLevel) . '</' . $this->_sCurrentPath . $iLevel . '>' .
                    PHP_EOL . str_repeat(' ', 4 * $iLevel) . '<' . $this->_sCurrentPath . $iLevel . '>' . PHP_EOL;
            }
            ++$iCount;
        }
        return $sReturnContent;
    }

    /**
     * holt aus dem übergebenen Content den TagString des Namespaces
     *
     * @param string $sContent inhalt in dem gesucht wird
     * @param string $sNameSpace name nach dem gesucht wird (namespace des tags)
     *
     * @return string
     */
    private function _extractLoopContent($sContent) {
        $sReturnString = '';

        $aTagThree = explode('_', $this->_sCurrentPath);
        $bFail = false;

        foreach ($aTagThree as $sTag) {
            if (preg_match('/(\s*\<' . $sTag . '\>.*?\<\/' . $sTag . '\>\s*)/si', $sContent, $aMatches)) {
                $sContent = $aMatches[1];
            } else {
                $bFail = true;
                break;
            }
        }

        if (false == $bFail) {
            $sReturnString = $sContent;
        }
        return $sReturnString;
    }
}
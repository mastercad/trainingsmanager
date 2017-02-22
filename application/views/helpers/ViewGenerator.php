<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 10.06.14
 * Time: 12:33
 */

if (false === function_exists('lcfirst')) {
    function lcfirst($sString) {
        return strtolower(substr($sString, 0, 1)) . substr($sString, 1);
    }
}

if (false === function_exists('ucfirst')) {
    function ucfirst($sString) {
        return strtoupper(substr($sString, 0, 1)) . substr($sString, 1);
    }
}

class Zend_View_Helper_ViewGenerator extends Zend_View_Helper_Abstract
{
    private $_aMehrzahl = array(
        'saetze' => 'saetze',
        'sitzposition' => 'sitzpositionen',
        'wiederholungen' => 'wiederholungen',
        'gewicht' => 'gewichte',
        'beinpolster' => 'beinpolster',
        'rueckenpolster' => 'rueckenpolster'
    );

    /**
     * @return $this
     */
    public function viewGenerator()
    {
        return $this;
    }

    /**
     * @param $sType name des zu setzenden basis wertes, die werte werden dann aus geraete gezogen
     * @param $sReferenzColumn name der spalte, gegen die geprüft werden soll aus der datenbank und deren value gesetzt wird
     * @param int $iCount counter der anzahl der select felder pro wert und trainingsplan um das abspeichern mehrerer werte zu gewährleisten
     *
     * @return string
     */
    public function generateOptionsForUebungColumn($sType, $sReferenzColumn = NULL, $iCount = 0)
    {
        $sTrainingsplanUebungColumnName = 'trainingsplan_uebung_' . lcfirst($sType);
        $aMoeglichkeitenArray = $this->generateMoeglichkeitenArrayFuerColumn($sType);
        $iTrainingsplanId = 0;
        $mReferenzValue = NULL;

        if (NULL !== $sReferenzColumn) {
            $mReferenzValue = $this->extractReferenzValue($sType, $sReferenzColumn);
        }

        if (TRUE === isset($this->view->iTrainingsplanId)) {
            $iTrainingsplanId = $this->view->iTrainingsplanId;
        }
        $sContent = '';
        if (TRUE == is_array($aMoeglichkeitenArray)
        ) {
            $sContent .= '<select class="uebung-option" name="' . $sReferenzColumn . '[' . $iTrainingsplanId . '][' . $iCount . ']" id="' . $sReferenzColumn . '_' . $iTrainingsplanId . '_' . $iCount . '" >';
            $sContent .= '<option value="0">Bitte ' . ucfirst($sType) . ' wählen:</option>';
                foreach ($aMoeglichkeitenArray as $iKey => $sValue) {
                    $sContent .= '<option value="' . $sValue . '"';
                        if (NULL !== $mReferenzValue
                            && $sValue == $mReferenzValue)
                        {
                            $sContent .= ' selected="selected" ';
                        }
                        $sContent .= '>' . $sValue . '</option>';
                }
            $sContent .= '</select>';
        } else {
            $sContent .= '<input class="uebung-option" type="text" id="' . $sReferenzColumn . '_' . $iTrainingsplanId . '_' . $iCount . '" name="' . $sReferenzColumn . '[' . $iTrainingsplanId . '][' . $iCount . ']" value="' . $this->formatFloatValue($mReferenzValue) . '" />';
        }
        return $sContent;
    }

    public function extractReferenzValue($sType, $sReferenzColumn)
    {
        $mReferenzValue = NULL;

        // original referenz checken
        if (TRUE === isset($this->view->{$sReferenzColumn})
            && NULL !== $this->view->{$sReferenzColumn}
        ) {
            $mReferenzValue = $this->view->{$sReferenzColumn};
        } elseif (TRUE === isset($this->view->{'trainingsplan_uebung_' . lcfirst($sType)})
            && NULL !== $this->view->{'trainingsplan_uebung_' . lcfirst($sType)}
        ) {
            $mReferenzValue = $this->view->{'trainingsplan_uebung_' . lcfirst($sType)};
        }
        return $mReferenzValue;
    }

    public function formatFloatValue($mValue)
    {
        $iValue = number_format($mValue, 0);
        if ($iValue != $mValue) {
            return $mValue;
        }
        return $iValue;
    }

    public function generateMoeglichkeitenArrayFuerColumn($sColumnName)
    {
        $sMoeglichkeitenArrayName = 'aMoegliche' . ucfirst($this->_aMehrzahl[$sColumnName]);
        $aMoeglichkeitenArray = array();
        if (TRUE == isset($this->view->{$sMoeglichkeitenArrayName})) {
            $aMoeglichkeitenArray = $this->view->{$sMoeglichkeitenArrayName};
        } elseif (TRUE === isset($this->view->{'geraet_moegliche_' . $this->_aMehrzahl[$sColumnName]})) {
            $aMoeglichkeitenArray = explode('|', $this->view->{'geraet_moegliche_' . $this->_aMehrzahl[$sColumnName]});
        }

        // wenn es ein string ist, ein array draus machen
        if (TRUE == is_string($aMoeglichkeitenArray)
            && 0 < strlen(trim($aMoeglichkeitenArray))
        ) {
            $aMoeglichkeitenArray = array($aMoeglichkeitenArray);
        } elseif (TRUE == is_array($aMoeglichkeitenArray)
            && 1 == count($aMoeglichkeitenArray)
        ) {
            $aMoeglichkeitenArray = $aMoeglichkeitenArray[0];
        }
        return $aMoeglichkeitenArray;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 07.05.14
 * Time: 22:15
 */ 

require_once __DIR__ . '/../models/DbTable/Uebungen.php';

class Service_Trainingsplan
{
    /** @var Application_Model_DbTable_Uebungen */
    private $_oUebungenStorage = NULL;

    public function __construct() {
        $this->setUebungenStorage(new Application_Model_DbTable_Uebungen());
    }

    public function getUebung($iUebungId) {
        return $this->_oUebungenStorage->getUebung($iUebungId);
    }
    /**
     * @param null $oUebungenStorage
     */
    public function setUebungenStorage(Application_Model_DbTable_Uebungen $oUebungenStorage) {
        $this->_oUebungenStorage = $oUebungenStorage;
        return $this;
    }

    /**
     * @return null
     */
    public function getUebungenStorage() {
        return $this->_oUebungenStorage;
    }
}

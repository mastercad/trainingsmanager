<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 07.05.14
 * Time: 21:12
 */ 

// http://www.admin-wissen.de/tutorials/php_tutorial/fortgeschrittene/testgetriebene_entwicklung/mocks.html

require_once __DIR__ . '/../application/services/Trainingsplan.php';

class MockTest extends PHPUnit_Framework_TestCase
{
    protected function _createMock()
    {
        // zu mockende klasse,
        // zu überschreibende methoden
        $oMock = $this->getMock('Application_Model_DbTable_Uebungen', array('getUebung'));
        // wie oft wird diese funktion im laufe des tests aufgerufen?
        $oMock->expects($this->exactly(1))
            // verhalten der überschriebenen methode definieren
            ->method('getUebung')
                // soll
                ->will($this->returnValue(array('uebung_id' => 1, 'uebung_name' => 'TestÜbungMock!')));

        return $oMock;
    }

    public function testGetUebungTrainingsplanService()
    {
        $oTrainingsplanService = new Service_Trainingsplan();
        $oTrainingsplanService->setUebungenStorage($this->_createMock());
        $oUebungRow = $oTrainingsplanService->getUebung(1);

        var_dump($oUebungRow);
    }
}

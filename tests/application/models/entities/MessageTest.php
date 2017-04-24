<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 10:19
 */

class Model_Entity_MessageTest extends PHPUnit_Framework_TestCase {

    public function testMapping() {
        $message = [
            'state' => Model_Entity_Message::STATUS_NOTICE,
            'text' => 'TESTTEXT'
        ];

        $messageEntity = new Model_Entity_Message($message);
    }
}
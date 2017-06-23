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

    public function testDriven()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['test'])
            ->getMock();

        $mock->method('test')
            ->with($this->matchesRegularExpression('/param/'), 'param2')
            ->willReturn('NIX');

        $this->assertSame('NIX', $mock->test('param1', 'param2'));
    }

    public function testDriven2()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['test'])
            ->getMock();

        $mock->method('test')
            ->withConsecutive(
                [$this->matchesRegularExpression('/param/'), 'param2'],
                [$this->isType('integer'), $this->isNull()]
            )
            ->willReturn('NIX');

        $this->assertSame('NIX', $mock->test('param1', 'param2'));
        $this->assertSame('NIX', $mock->test(1, null));
    }

    public function testDriven3()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['test'])
            ->getMock();

        $mock->method('test')
            ->withConsecutive(
                [$this->isType('array')],
                [$this->isType('bool')]
            )
            ->willReturn('NIX');

        $this->assertSame('NIX', $mock->test(['test']));
        $this->assertSame('NIX', $mock->test(false));
    }

    public function testProcessSitePaths()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['processSitePaths'])
            ->getMock();

        $expectation = [
            118 => [
                119 => null,
                120 => null,
            ],
            130 => [
                131 => [
                    132 => null
                ],
                133 => null
            ],
            135 => null
        ];

        $paths = [
            '/118/119',
            '/118/120',
            '/130/131/132',
            '/130/133',
            '/135'
        ];

        $mock->method('processSitePaths')
            ->with($this->isType('array'))
            ->willReturn($expectation);

        $this->assertSame($expectation, $mock->processSitePaths($paths));
    }

    /**
     * @dataProvider convertPathProvider
     */
    public function testConvertPathToArray($path, $result)
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['convertPathToArray'])
            ->getMock();

        $mock->method('convertPathToArray')
            ->with($this->matchesRegularExpression('/^\/\d+/'))
            ->willReturn($result);

        $this->assertSame($result, $mock->convertPathToArray($path));
    }

    public function convertPathProvider()
    {
        return [
            [
                '/118/119',
                [118 => [119 => null]]
            ],
            [
                '/118/120',
                [118 => [120 => null]]
            ],
            [
                '/130/131/132',
                [130 => [131 => [132 => null]]]
            ],
            [
                '/135',
                [135 => null]
            ]
        ];
    }
}
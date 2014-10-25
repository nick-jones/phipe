<?php

namespace Phipe\Connection\Stream;

class ProberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prober
     */
    protected $prober;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selector;

    protected function setUp()
    {
        $this->selector = $this->getMock('\Phipe\Connection\Stream\Selector');
        $this->prober = new Prober($this->selector);
    }

    public function testProbe()
    {
        $stream1 = fopen('php://memory', 'r');
        $stream2 = fopen('php://memory', 'r');

        $this->selector->expects($this->once())
            ->method('select')
            ->with(array($stream1, $stream2))
            ->will($this->returnValue(array($stream1)));

        $connection1 = $this->createMockConnection($stream1);

        $connection1->expects($this->once())
            ->method('populateReadBuffer');

        $connection2 = $this->createMockConnection($stream2);

        $connection2->expects($this->never())
            ->method('populateReadBuffer');

        $this->prober->probe(array($connection1, $connection2));
    }

    /**
     * Creates a mock connection which handles the provided stream resource.
     *
     * @param resource $stream The value to be returned by the "getStream" method
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockConnection($stream)
    {
        $connection = $this->getMock('\Phipe\Connection\Stream\Connection', array(), array('127.0.0.1', 80));

        $connection->expects($this->any())
            ->method('getStream')
            ->will($this->returnValue($stream));

        $connection->expects($this->once())
            ->method('clearReadBuffer');

        return $connection;
    }
}
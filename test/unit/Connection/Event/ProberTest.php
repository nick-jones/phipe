<?php

namespace Phipe\Connection\Event;

class ProberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prober
     */
    protected $prober;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventBase;

    protected function setUp()
    {
        $this->eventBase = $this->getMock('\Phipe\Stub\EventBase');
        $this->prober = new Prober($this->eventBase);
    }

    public function testProbe()
    {
        $connection = $this->getMock('\Phipe\Connection', array(), array('127.0.0.1', 80));

        $this->eventBase->expects($this->once())
            ->method('loop')
            ->with($this->equalTo(\EventBase::LOOP_ONCE));

        $this->prober->probe(array($connection));
    }
}
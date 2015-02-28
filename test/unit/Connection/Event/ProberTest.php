<?php

namespace Phipe\Connection\Event;

use Phipe\Stub\EventBase;

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
        $this->eventBase = $this->getMock(EventBase::CLASS);
        $this->prober = new Prober($this->eventBase);
    }

    public function testProbe()
    {
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $this->eventBase->expects($this->once())
            ->method('loop')
            ->with($this->equalTo(\EventBase::LOOP_ONCE));

        $this->prober->probe([$connection]);
    }
}
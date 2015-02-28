<?php

namespace Phipe;

use Phipe\Connection\Prober;

class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pool;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $prober;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $strategies = [];

    protected function setUp()
    {
        $this->pool = $this->getMock(Pool::CLASS);
        $this->prober = $this->getMock(Prober::CLASS);

        $this->strategies = [
            'connect' => $this->getMock(Strategy\Connect::CLASS),
            'reconnect' => $this->getMock(Strategy\Reconnect::CLASS),
            'disconnect' => $this->getMock(Strategy\Disconnect::CLASS),
            'activity_detect' => $this->getMock(Strategy\ActivityDetect::CLASS)
        ];

        $this->session = new Session($this->pool, $this->prober, $this->strategies);
    }

    public function testInitialise()
    {
        $this->strategies['connect']->expects($this->once())
            ->method('performConnect')
            ->with($this->equalTo($this->pool));

        $this->session->initialise();
    }

    public function testWork()
    {
        $this->strategies['reconnect']->expects($this->once())
            ->method('performReconnect')
            ->with($this->equalTo($this->pool));

        $this->strategies['disconnect']->expects($this->once())
            ->method('performDisconnect')
            ->with($this->equalTo($this->pool));

        $this->strategies['activity_detect']->expects($this->once())
            ->method('performDetect')
            ->with($this->equalTo($this->pool), $this->equalTo($this->prober));

        $this->session->work();
    }

    public function testHasWork()
    {
        $this->pool->expects($this->once())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->session->hasWork());
    }

    public function testHasWorkWithNoneAvailable()
    {
        $this->pool->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));

        $this->assertFalse($this->session->hasWork());
    }
}
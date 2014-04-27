<?php

namespace Phipe\Strategy\Reconnect;

class SequentialDelayedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SequentialDelayed
     */
    protected $strategy;

    protected function setUp()
    {
        $this->setUpStrategy();
    }

    /**
     * @param int $waitUntil
     */
    protected function setUpStrategy($waitUntil = 0)
    {
        $this->strategy = new SequentialDelayed(30, $waitUntil);
    }

    public function testReconnect()
    {
        $connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

        $connection->expects($this->once())
            ->method('isDisconnected')
            ->will($this->returnValue(true));

        $connection->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new \Phipe\Connection\ConnectionException('Mock', $connection)));

        $disconnected = $this->getMock('\Phipe\Pool');

        $disconnected->expects($this->once())
            ->method('walk')
            ->with($this->isInstanceOf('Closure'))
            ->will(
                $this->returnCallback(
                    function ($callback) use ($connection) {
                        call_user_func($callback, $connection);
                    }
                )
            );

        $pool = $this->getMock('\Phipe\Pool');

        $pool->expects($this->once())
            ->method('filter')
            ->with($this->isInstanceOf('Closure'))
            ->will(
                $this->returnCallback(
                    function ($callback) use ($connection, $disconnected) {
                        return call_user_func($callback, $connection) ? $disconnected : null;
                    }
                )
            );

        $this->strategy->performReconnect($pool);

        // Re-running should not trigger any further action, due to the delay constraints
        $this->strategy->performReconnect($pool);
    }

    public function testReconnect_FutureWaitUntil()
    {
        $this->setUpStrategy(time() + 3600);

        $pool = $this->getMock('\Phipe\Pool');

        $pool->expects($this->never())
            ->method('filter');

        $this->strategy->performReconnect($pool);
    }
}
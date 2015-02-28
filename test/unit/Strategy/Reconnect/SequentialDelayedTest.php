<?php

namespace Phipe\Strategy\Reconnect;

use Phipe\Connection;
use Phipe\Connection\Exception;
use Phipe\Pool;

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
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $connection->expects($this->once())
            ->method('isDisconnected')
            ->will($this->returnValue(true));

        $connection->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new Exception('Mock', $connection)));

        $disconnected = $this->getMock(Pool::CLASS);

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

        $pool = $this->getMock(Pool::CLASS);

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

    public function testReconnectWithFutureWaitUntil()
    {
        $this->setUpStrategy(time() + 3600);

        $pool = $this->getMock(Pool::CLASS);

        $pool->expects($this->never())
            ->method('filter');

        $this->strategy->performReconnect($pool);
    }
}
<?php

namespace Phipe\Strategy\Disconnect;

use Phipe\Connection;
use Phipe\Pool;

class ExpungingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Expunging
     */
    protected $strategy;

    protected function setUp()
    {
        $this->strategy = new Expunging();
    }

    public function testDisconnect()
    {
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $connection->expects($this->once())
            ->method('isDisconnected')
            ->will($this->returnValue(true));

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

        $pool->expects($this->any())
            ->method('getAllWithState')
            ->will($this->returnValue($this->getMock(Pool::CLASS)));

        $pool->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($connection));

        $this->strategy->performDisconnect($pool);
    }
}
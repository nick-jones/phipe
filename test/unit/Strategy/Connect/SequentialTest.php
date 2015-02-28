<?php

namespace Phipe\Strategy\Connect;

use Phipe\Connection;
use Phipe\Connection\Exception;
use Phipe\Pool;

class SequentialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Sequential
     */
    protected $strategy;

    protected function setUp()
    {
        $this->strategy = new Sequential();
    }

    public function testConnect()
    {
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $connection->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new Exception('Mock', $connection)));

        $pool = $this->getMock(Pool::CLASS);

        $pool->expects($this->once())
            ->method('walk')
            ->with($this->isInstanceOf('Closure'))
            ->will(
                $this->returnCallback(
                    function ($callback) use ($connection) {
                        call_user_func($callback, $connection);
                    }
                )
            );

        $this->strategy->performConnect($pool);
    }
}
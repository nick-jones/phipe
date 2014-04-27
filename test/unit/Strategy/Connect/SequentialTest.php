<?php

namespace Phipe\Strategy\Connect;

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
        $connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

        $connection->expects($this->once())
            ->method('connect')
            ->will($this->throwException(new \Phipe\Connection\ConnectionException('Mock', $connection)));

        $pool = $this->getMock('\Phipe\Pool');

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
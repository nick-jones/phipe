<?php

namespace Phipe\Strategy\Disconnect;

use Phipe\Connection;
use Phipe\Pool;

class SoftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Soft
     */
    protected $strategy;

    protected function setUp()
    {
        $this->strategy = new Soft();
    }

    public function testDisconnect()
    {
        $strategy = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $strategy->expects($this->once())
            ->method('disconnect');

        $eof = $this->getMock(Pool::CLASS);

        $eof->expects($this->once())
            ->method('walk')
            ->with($this->isInstanceOf('Closure'))
            ->will(
                $this->returnCallback(
                    function ($callback) use ($strategy) {
                        call_user_func($callback, $strategy);
                    }
                )
            );

        $pool = $this->getMock(Pool::CLASS);

        $pool->expects($this->once())
            ->method('getAllWithState')
            ->with($this->equalTo(Connection::STATE_EOF))
            ->will($this->returnValue($eof));

        $this->strategy->performDisconnect($pool);
    }
}
<?php

namespace Phipe\Strategy\ActivityDetect;

use Phipe\Connection;
use Phipe\Connection\Prober;
use Phipe\Pool;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Simple
     */
    protected $strategy;

    protected function setUp()
    {
        $this->strategy = new Simple();
    }

    public function testDetect()
    {
        $connections = [
            $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80])
        ];

        $connected = $this->getMock(Pool::CLASS);

        $connected->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($connections));

        $pool = $this->getMock(Pool::CLASS);

        $pool->expects($this->once())
            ->method('getAllWithState')
            ->with($this->equalTo(Connection::STATE_CONNECTED))
            ->will($this->returnValue($connected));

        $prober = $this->getMock(Prober::CLASS);

        $prober->expects($this->once())
            ->method('probe')
            ->with($this->equalTo($connections));

        $this->strategy->performDetect($pool, $prober);
    }
}
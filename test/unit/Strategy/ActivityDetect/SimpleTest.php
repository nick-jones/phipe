<?php

namespace Phipe\Strategy\ActivityDetect;

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
        $connections = array(
            $this->getMock('\Phipe\Connection', array(), array('127.0.0.1', 80))
        );

        $connected = $this->getMock('\Phipe\Pool');

        $connected->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($connections));

        $pool = $this->getMock('\Phipe\Pool');

        $pool->expects($this->once())
            ->method('getAllWithState')
            ->with($this->equalTo(\Phipe\Connection::STATE_CONNECTED))
            ->will($this->returnValue($connected));

        $prober = $this->getMock('\Phipe\Connection\Prober');

        $prober->expects($this->once())
            ->method('probe')
            ->with($this->equalTo($connections));

        $this->strategy->performDetect($pool, $prober);
    }
}
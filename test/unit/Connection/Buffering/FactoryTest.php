<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Factory as ConnectionFactory;
use Phipe\Connection\Prober as ConnectionProber;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $proxied;

    protected function setUp()
    {
        $this->proxied = $this->getMock(ConnectionFactory::CLASS);
        $this->factory = new Factory($this->proxied);
    }

    public function testCreateConnection()
    {
        $host = '127.0.0.1';
        $port = 80;

        $this->proxied
            ->expects($this->once())
            ->method('createConnection')
            ->with($this->equalTo($host), $this->equalTo($port))
            ->will($this->returnValue($this->getMock(Connection::CLASS)));

        $connection = $this->factory->createConnection($host, $port);
        $this->assertEquals(Connection::CLASS, get_class($connection));
    }

    public function testCreateProber()
    {
        $this->proxied
            ->expects($this->once())
            ->method('createProber')
            ->will($this->returnValue($this->getMock(ConnectionProber::CLASS)));

        $prober = $this->factory->createProber();
        $this->assertEquals(Prober::CLASS, get_class($prober));
    }
}
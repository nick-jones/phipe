<?php

namespace Phipe\Connection\Buffering;

class BufferingFactoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var BufferingFactory
     */
    protected $factory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $proxied;

    protected function setUp() {
        $this->proxied = $this->getMock('\Phipe\Connection\Factory');
        $this->factory = new BufferingFactory($this->proxied);
    }

    public function testCreateConnection() {
        $host = '127.0.0.1';
        $port = 80;

        $this->proxied
            ->expects($this->once())
            ->method('createConnection')
            ->with($this->equalTo($host), $this->equalTo($port))
            ->will($this->returnValue($this->getMock('\Phipe\Connection\Connection')));

        $connection = $this->factory->createConnection($host, $port);
        $this->assertEquals('Phipe\Connection\Buffering\BufferingConnection', get_class($connection));
    }

    public function testCreateProber() {
        $this->proxied
            ->expects($this->once())
            ->method('createProber')
            ->will($this->returnValue($this->getMock('\Phipe\Connection\Prober')));

        $prober = $this->factory->createProber();
        $this->assertEquals('Phipe\Connection\Buffering\BufferingProber', get_class($prober));
    }
}
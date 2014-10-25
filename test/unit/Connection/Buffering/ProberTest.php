<?php

namespace Phipe\Connection\Buffering;

class ProberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prober
     */
    protected $prober;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $proxied;

    protected function setUp()
    {
        $this->proxied = $this->getMock('\Phipe\Connection\Prober');
        $this->prober = new Prober($this->proxied);
    }

    public function testProbe()
    {
        $proxiedConnection = $this->getMock('\Phipe\Connection');

        $connection = $this->getMock('\Phipe\Connection\Buffering\Connection');

        $connection->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($proxiedConnection));

        $this->proxied
            ->expects($this->once())
            ->method('probe')
            ->with($this->equalTo(array($proxiedConnection)));

        $this->prober->probe(array($connection));
    }
}
<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Buffering\Connection as BufferingConnection;
use Phipe\Connection\Prober as ConnectionProber;
use Phipe\Connection;

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
        $this->proxied = $this->getMock(ConnectionProber::CLASS);
        $this->prober = new Prober($this->proxied);
    }

    public function testProbe()
    {
        $proxiedConnection = $this->getMock(Connection::CLASS);

        $connection = $this->getMock(BufferingConnection::CLASS);

        $connection->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValue($proxiedConnection));

        $this->proxied
            ->expects($this->once())
            ->method('probe')
            ->with($this->equalTo([$proxiedConnection]));

        $this->prober->probe([$connection]);
    }
}
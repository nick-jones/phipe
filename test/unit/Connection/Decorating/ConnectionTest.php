<?php

namespace Phipe\Connection\Decorating;

use Phipe\Connection as BaseConnection;
use Phipe\Connection\Decorating\Connection as DecoratingConnection;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $proxied;

    protected function setUp()
    {
        $this->proxied = $this->getMock(Connection::CLASS);
        $this->connection = $this->getMockForAbstractClass(DecoratingConnection::CLASS, [$this->proxied]);
    }

    public function testSetConnection()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|BaseConnection $connection */
        $connection = $this->getMock(BaseConnection::CLASS);

        $this->connection->setConnection($connection);

        $this->assertEquals($connection, $this->connection->getConnection());
    }

    public function testGetConnection()
    {
        $this->assertEquals($this->proxied, $this->connection->getConnection());
    }

    public function testConnect()
    {
        $this->proxied
            ->expects($this->once())
            ->method('connect');

        $this->connection->connect();
    }

    public function testDisconnect()
    {
        $this->proxied
            ->expects($this->once())
            ->method('disconnect');

        $this->connection->disconnect();
    }

    public function testWrite()
    {
        $data = 'mock';

        $this->proxied
            ->expects($this->once())
            ->method('write')
            ->with($this->equalTo($data));

        $this->connection->write($data);
    }

    public function testRead()
    {
        $data = 'mock';

        $this->proxied
            ->expects($this->once())
            ->method('read')
            ->will($this->returnValue($data));

        $this->assertEquals($data, $this->connection->read());
    }

    public function testGetState()
    {
        $state = 0;

        $this->proxied
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($state));

        $this->assertEquals($state, $this->connection->getState());
    }

    public function testSetHost()
    {
        $host = 'irc.mock.example';

        $this->proxied
            ->expects($this->once())
            ->method('setHost')
            ->with($this->equalTo($host));

        $this->connection->setHost($host);
    }

    public function testSetPort()
    {
        $port = 80;

        $this->proxied
            ->expects($this->once())
            ->method('setPort')
            ->with($this->equalTo($port));

        $this->connection->setPort($port);
    }

    public function testSetSsl()
    {
        $ssl = true;

        $this->proxied
            ->expects($this->once())
            ->method('setSsl')
            ->with($this->equalTo($ssl));

        $this->connection->setSsl($ssl);
    }
}
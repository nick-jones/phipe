<?php

namespace Phipe;

/**
 * @package Phipe
 */
class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Connection
     */
    protected $connection;

    protected function setUp()
    {
        $this->connection = $this->getMockForAbstractClass(Connection::CLASS, ['127.0.0.1', 80]);
    }

    public function testIsEndOfFile()
    {
        $this->connection
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(Connection::STATE_EOF));

        $result = $this->connection->isEndOfFile();

        $this->assertTrue($result, 'Connection must indicate it has reached EOF');
    }

    public function testIsEndOfFileWithNonTerminated()
    {
        $this->connection
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(0));

        $result = $this->connection->isEndOfFile();

        $this->assertFalse($result, 'Connection must not indicate it has reached EOF');
    }

    public function testIsConnected()
    {
        $this->connection
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(Connection::STATE_CONNECTED));

        $result = $this->connection->isConnected();

        $this->assertTrue($result, 'Connection must indicate it is connected');
    }

    public function testIsConnectedWhenNotConnected()
    {
        $this->connection
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(0));

        $result = $this->connection->isConnected();

        $this->assertFalse($result, 'Connection must not indicate it is connected');
    }

    public function testIsDisconnected()
    {
        $this->connection
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(0));

        $result = $this->connection->isDisconnected();

        $this->assertTrue($result, 'Connection must indicate it is disconnected');
    }

    public function testIsDisconnectedWhenNotDisconnected()
    {
        $this->connection
            ->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(Connection::STATE_CONNECTED));

        $result = $this->connection->isDisconnected();

        $this->assertFalse($result, 'Connection must not indicate it is disconnected');
    }

    public function testAttach()
    {
        $observer = $this->getMock(\SplObserver::CLASS);

        $storage = $this->getMock(\SplObjectStorage::CLASS);

        $storage->expects($this->once())
            ->method('attach')
            ->with($this->equalTo($observer));

        $this->connection->setObservers($storage);
        $this->connection->attach($observer);
    }

    public function testDetach()
    {
        $observer = $this->getMock(\SplObserver::CLASS);

        $storage = $this->getMock(\SplObjectStorage::CLASS);

        $storage->expects($this->once())
            ->method('detach')
            ->with($this->equalTo($observer));

        $this->connection->setObservers($storage);
        $this->connection->detach($observer);
    }

    public function testNotify()
    {
        $event = 'mock';

        $observer = $this->getMock(\SplObserver::CLASS);

        $observer->expects($this->once())
            ->method('update')
            ->with($this->connection, $event);

        $this->connection->setObservers([$observer]);
        $this->connection->notify($event);
    }
}
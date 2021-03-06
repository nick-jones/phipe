<?php

namespace Phipe;

/**
 * @package Phipe
 */
class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $connections;

    protected function setUp()
    {
        $this->connections = $this->getMock(\SplObjectStorage::CLASS);

        $this->pool = new Pool();
        $this->pool->setConnections($this->connections);
    }

    public function testAdd()
    {
        $connection = $this->createMockConnection();

        $this->connections
            ->expects($this->once())
            ->method('attach')
            ->with($connection);

        $this->pool->add($connection);
    }

    public function testRemove()
    {
        $connection = $this->createMockConnection();

        $this->connections
            ->expects($this->once())
            ->method('detach')
            ->with($connection);

        $this->pool->remove($connection);
    }

    public function testSetAll()
    {
        $storage = $this->getMock(\SplObjectStorage::CLASS);

        $this->pool->setConnections($storage);

        $this->assertEquals(0, count($this->pool));
    }

    public function testFilterWithValueInclusion()
    {
        $connections = [
            $this->createMockConnection(),
            $this->createMockConnection()
        ];

        $this->pool->setConnections($connections);

        // Filter always returning TRUE for inclusion, expecting everything to be returned
        $result = $this->pool->filter(
            function (Connection $connection) {
                return true;
            }
        );

        $this->assertEquals(2, count($result));
    }

    public function testFilterWithValueExclusion()
    {
        $this->pool->setConnections([
            $this->createMockConnection()
        ]);

        // Filter always returning FALSE for exclusion, expecting nothing to be return
        $result = $this->pool->filter(
            function (Connection $connection) {
                return false;
            }
        );

        $this->assertEquals(0, count($result));
    }

    public function testWalk()
    {
        $this->pool->setConnections([
            $this->createMockConnection(),
            $this->createMockConnection()
        ]);

        $calls = 0;

        $this->pool->walk(
            function (Connection $connection) use (&$calls) {
                ++$calls;
            }
        );

        $this->assertEquals(2, $calls);
    }

    public function testgetAllWithState()
    {
        $connectionConnected = $this->createMockConnection(Connection::STATE_CONNECTED);
        $connectionDataAvailable = $this->createMockConnection(Connection::STATE_DATA_AVAILABLE);
        $connectionUnknown = $this->createMockConnection(0);

        $connections = [$connectionConnected, $connectionDataAvailable, $connectionUnknown];

        $this->pool->setConnections($connections);

        $this->assertEquals(1, count($this->pool->getAllWithState(Connection::STATE_CONNECTED)));
        $this->assertEquals(1, count($this->pool->getAllWithState(Connection::STATE_DATA_AVAILABLE)));
        $this->assertEquals(0, count($this->pool->getAllWithState(Connection::STATE_EOF)));
    }

    public function testCount()
    {
        $this->assertEquals(0, $this->pool->count());

        $this->pool->setConnections([$this->createMockConnection()]);

        $this->assertEquals(1, $this->pool->count());
    }

    public function testToArray()
    {
        $connection = $this->createMockConnection();

        $storage = new \SplObjectStorage();
        $storage->attach($connection);

        $this->pool->setConnections($storage);
        $result = $this->pool->toArray();

        $this->assertEquals([$connection], $result);
    }

    /**
     * @param int $state
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockConnection($state = 0)
    {
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $connection->expects($this->any())
            ->method('getState')
            ->will($this->returnValue($state));

        return $connection;
    }
}
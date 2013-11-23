<?php

namespace Phipe;

use Phipe\Connection\Connection;

/**
 * @package Phipe
 */
class PoolTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Pool
	 */
	protected $pool;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject
	 */
	protected $connections;

	protected function setUp() {
		$this->connections = $this->getMock('\SplObjectStorage');

		$this->pool = new Pool();
		$this->pool->setAll($this->connections);
	}

	public function testAdd() {
		$connection = $this->createMockConnection();

		$this->connections
			->expects($this->once())
			->method('attach')
			->with($connection);

		$this->pool->add($connection);
	}

	public function testRemove() {
		$connection = $this->createMockConnection();

		$this->connections
			->expects($this->once())
			->method('detach')
			->with($connection);

		$this->pool->remove($connection);
	}

	public function testSetAll() {
		$storage = $this->getMock('\SplObjectStorage');

		$this->pool->setAll($storage);

		$this->assertEquals($storage, $this->pool->getAll());
	}

	public function testGetAll() {
		$this->assertInstanceOf('\SplObjectStorage', $this->pool->getAll());
	}

	public function testFilter_Inclusion() {
		$connections = array(
			$this->createMockConnection(),
			$this->createMockConnection()
		);

		$this->pool->setAll($connections);

		// Filter always returning TRUE for inclusion, expecting everything to be returned
		$result = $this->pool->filter(function(Connection $connection) {
			return TRUE;
		});

		$this->assertEquals(2, count($result));
	}

	public function testFilter_Exclusion() {
		$this->pool->setAll(array(
			$this->createMockConnection()
		));

		// Filter always returning FALSE for exclusion, expecting nothing to be return
		$result = $this->pool->filter(function(Connection $connection) {
			return FALSE;
		});

		$this->assertEquals(0, count($result));
	}

	public function testWalk() {
		$this->pool->setAll(array(
			$this->createMockConnection(),
			$this->createMockConnection()
		));

		$calls = 0;

		$this->pool->walk(function(Connection $connection) use(&$calls) {
			++$calls;
		});

		$this->assertEquals(2, $calls);
	}

	public function testgetAllWithState() {
		$connectionConnected = $this->createMockConnection(Connection::STATE_CONNECTED);
		$connectionDataAvailable = $this->createMockConnection(Connection::STATE_DATA_AVAILABLE);
		$connectionUnknown = $this->createMockConnection(0);

		$connections = array($connectionConnected, $connectionDataAvailable, $connectionUnknown);

		$this->pool->setAll($connections);

		$this->assertEquals(1, count($this->pool->getAllWithState(Connection::STATE_CONNECTED)));
		$this->assertEquals(1, count($this->pool->getAllWithState(Connection::STATE_DATA_AVAILABLE)));
		$this->assertEquals(0, count($this->pool->getAllWithState(Connection::STATE_EOF)));
	}

	public function testCount() {
		$this->assertEquals(0, $this->pool->count());

		$this->pool->setAll(array($this->createMockConnection()));

		$this->assertEquals(1, $this->pool->count());
	}

	/**
	 * @param int $state
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function createMockConnection($state = 0) {
		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$connection->expects($this->any())
			->method('getState')
			->will($this->returnValue($state));

		return $connection;
	}
}
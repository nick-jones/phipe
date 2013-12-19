<?php

namespace Phipe\Connection\Decorating;

class DecoratingConnectionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|DecoratingConnection
	 */
	protected $connection;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection
	 */
	protected $proxied;

	protected function setUp() {
		$this->proxied = $this->getMock('\Phipe\Connection\Connection');

		$this->connection = $this->getMockForAbstractClass(
			'\Phipe\Connection\Decorating\DecoratingConnection',
			array($this->proxied)
		);
	}

	public function testSetConnection() {
		/** @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection $connection */
		$connection = $this->getMock('\Phipe\Connection\Connection');;

		$this->connection->setConnection($connection);

		$this->assertEquals($connection, $this->connection->getConnection());
	}

	public function testGetConnection() {
		$this->assertEquals($this->proxied, $this->connection->getConnection());
	}

	public function testConnect() {
		$this->proxied
			->expects($this->once())
			->method('connect');

		$this->connection->connect();
	}

	public function testDisconnect() {
		$this->proxied
			->expects($this->once())
			->method('disconnect');

		$this->connection->disconnect();
	}

	public function testWrite() {
		$data = 'mock';

		$this->proxied
			->expects($this->once())
			->method('write')
			->with($this->equalTo($data));

		$this->connection->write($data);
	}

	public function testRead() {
		$data = 'mock';

		$this->proxied
			->expects($this->once())
			->method('read')
			->will($this->returnValue($data));

		$this->assertEquals($data, $this->connection->read());
	}

	public function testGetState() {
		$state = 0;

		$this->proxied
			->expects($this->once())
			->method('getState')
			->will($this->returnValue($state));

		$this->assertEquals($state, $this->connection->getState());
	}
}
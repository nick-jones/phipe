<?php

namespace Phipe\Connection\Buffering;

class BufferingConnectionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|BufferingConnection
	 */
	protected $connection;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection
	 */
	protected $proxied;

	protected function setUp() {
		$this->proxied = $this->getMock('\Phipe\Connection\Connection');

		$this->connection = $this->getMockForAbstractClass(
			'\Phipe\Connection\Buffering\BufferingConnection',
			array($this->proxied)
		);
	}

	public function testWrite() {
		$this->proxied
			->expects($this->at(0))
			->method('write')
			->with($this->equalTo("foo\n"));

		$this->proxied
			->expects($this->at(1))
			->method('write')
			->with($this->equalTo("bar\n"));

		$this->connection->write("foo\nba");
		$this->connection->write("r\n");
	}


	public function testRead() {
		$this->proxied
			->expects($this->at(0))
			->method('read')
			->will($this->returnValue("foo\nba"));

		$this->proxied
			->expects($this->at(1))
			->method('read')
			->will($this->returnValue("r\n"));

		$this->assertEquals("foo\n", $this->connection->read());
		$this->assertEquals("bar\n", $this->connection->read());
	}
}
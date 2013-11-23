<?php

namespace Phipe\Handler\Disconnect;

class SoftTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Soft
	 */
	protected $handler;

	protected function setUp() {
		$this->handler = new Soft();
	}

	public function testDisconnect() {
		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$connection->expects($this->once())
			->method('disconnect');

		$eof = $this->getMock('\Phipe\Pool');

		$eof->expects($this->once())
			->method('walk')
			->with($this->isInstanceOf('Closure'))
			->will($this->returnCallback(function($callback) use($connection) {
				call_user_func($callback, $connection);
			}));

		$pool = $this->getMock('\Phipe\Pool');

		$pool->expects($this->once())
			->method('getAllWithState')
			->with($this->equalTo(\Phipe\Connection\Connection::STATE_EOF))
			->will($this->returnValue($eof));

		$this->handler->performDisconnect($pool);
	}
}
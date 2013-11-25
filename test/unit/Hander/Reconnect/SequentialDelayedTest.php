<?php

namespace Phipe\Handler\Reconnect;

class SequentialDelayedTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var SequentialDelayed
	 */
	protected $handler;

	protected function setUp() {
		$this->handler = new SequentialDelayed();
	}

	public function testReconnect() {
		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$connection->expects($this->once())
			->method('isDisconnected')
			->will($this->returnValue(TRUE));

		$connection->expects($this->once())
			->method('connect');

		$disconnected = $this->getMock('\Phipe\Pool');

		$disconnected->expects($this->once())
			->method('walk')
			->with($this->isInstanceOf('Closure'))
			->will($this->returnCallback(function($callback) use($connection) {
				call_user_func($callback, $connection);
			}));

		$pool = $this->getMock('\Phipe\Pool');

		$pool->expects($this->once())
			->method('filter')
			->with($this->isInstanceOf('Closure'))
			->will($this->returnCallback(function($callback) use($connection, $disconnected) {
				return call_user_func($callback, $connection) ? $disconnected : NULL;
			}));

		$this->handler->performReconnect($pool);

		// Re-running should not trigger any further action, due to the delay constraints
		$this->handler->performReconnect($pool);
	}
}
<?php

namespace Phipe\Handler\Disconnect;

class ExpungingTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Expunging
	 */
	protected $handler;

	protected function setUp() {
		$this->handler = new Expunging();
	}

	public function testDisconnect() {
		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$connection->expects($this->once())
			->method('isDisconnected')
			->will($this->returnValue(TRUE));

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

		$pool->expects($this->any())
			->method('getAllWithState')
			->will($this->returnValue($this->getMock('\Phipe\Pool')));

		$pool->expects($this->once())
			->method('remove')
			->with($this->equalTo($connection));

		$this->handler->performDisconnect($pool);
	}
}
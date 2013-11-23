<?php

namespace Phipe\Handler\Connect;

class SimpleTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Simple
	 */
	protected $handler;

	protected function setUp() {
		$this->handler = new Simple();
	}

	public function testConnect() {
		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$connection->expects($this->once())
			->method('connect');

		$pool = $this->getMock('\Phipe\Pool');

		$pool->expects($this->once())
			->method('walk')
			->with($this->isInstanceOf('Closure'))
			->will($this->returnCallback(function($callback) use($connection) {
				call_user_func($callback, $connection);
			}));

		$this->handler->performConnect($pool);
	}
}
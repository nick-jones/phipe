<?php

namespace Phipe;

class SessionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Session
	 */
	protected $session;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Pool
	 */
	protected $pool;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Prober
	 */
	protected $prober;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject[]
	 */
	protected $handlers = array();

	/**
	 *
	 */
	protected function setUp() {
		$this->pool = $this->getMock('\Phipe\Pool');
		$this->prober = $this->getMock('\Phipe\Connection\Prober');

		$this->handlers = array(
			'connect' => $this->getMock('\Phipe\Handler\Connect'),
			'reconnect' => $this->getMock('\Phipe\Handler\Reconnect'),
			'disconnect' => $this->getMock('\Phipe\Handler\Disconnect'),
			'activity' => $this->getMock('\Phipe\Handler\Activity')
		);

		$this->session = new Session($this->pool, $this->prober, $this->handlers);
	}

	public function testInitialise() {
		$this->handlers['connect']->expects($this->once())
			->method('performConnect')
			->with($this->equalTo($this->pool));

		$this->session->initialise();
	}

	public function testWork() {
		$this->handlers['reconnect']->expects($this->once())
			->method('performReconnect')
			->with($this->equalTo($this->pool));

		$this->handlers['disconnect']->expects($this->once())
			->method('performDisconnect')
			->with($this->equalTo($this->pool));

		$this->handlers['activity']->expects($this->once())
			->method('performDetect')
			->with($this->equalTo($this->pool), $this->equalTo($this->prober));

		$this->session->work();
	}

	public function testHasWork() {
		$this->pool->expects($this->once())
			->method('count')
			->will($this->returnValue(1));

		$this->assertTrue($this->session->hasWork());
	}

	public function testHasWork_None() {
		$this->pool->expects($this->once())
			->method('count')
			->will($this->returnValue(0));

		$this->assertFalse($this->session->hasWork());
	}
}
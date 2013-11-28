<?php

namespace Phipe\Connection\Event;

require_once __DIR__ . '/../../../helper/Stub/EventBase.php';

class EventProberTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var EventProber
	 */
	protected $prober;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Stub\EventBase
	 */
	protected $eventBase;

	protected function setUp() {
		$this->eventBase = $this->getMock('\Phipe\Stub\EventBase');
		$this->prober = new EventProber($this->eventBase);
	}

	public function testProbe() {
		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$this->eventBase->expects($this->once())
			->method('loop')
			->with($this->equalTo(\EventBase::LOOP_ONCE));

		$this->prober->probe(array($connection));
	}
}
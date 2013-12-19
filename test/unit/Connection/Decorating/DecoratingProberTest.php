<?php

namespace Phipe\Connection\Decorating;

class DecoratingProberTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var DecoratingProber
	 */
	protected $prober;

	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Prober
	 */
	protected $proxied;

	protected function setUp() {
		$this->proxied = $this->getMock('\Phipe\Connection\Prober');
		$this->prober = new DecoratingProber($this->proxied);
	}

	public function testProbe() {
		$proxiedConnection = $this->getMock('\Phipe\Connection\Connection');

		$connection = $this->getMock('\Phipe\Connection\Decorating\DecoratingConnection');

		$connection->expects($this->once())
			->method('getConnection')
			->will($this->returnValue($proxiedConnection));

		$this->proxied
			->expects($this->once())
			->method('probe')
			->with($this->equalTo(array($proxiedConnection)));

		$this->prober->probe(array($connection));
	}
}
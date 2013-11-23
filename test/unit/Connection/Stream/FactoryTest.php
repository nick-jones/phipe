<?php

namespace Phipe\Connection\Stream;

class FactoryTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var StreamFactory
	 */
	protected $factory;

	protected function setUp() {
		$this->factory = new StreamFactory();
	}

	public function testCreateConnection() {
		$connection = $this->factory->createConnection('127.0.0.1', 80);
		$this->assertEquals('Phipe\Connection\Stream\StreamConnection', get_class($connection));
	}

	public function testCreateProber() {
		$prober = $this->factory->createProber();
		$this->assertEquals('Phipe\Connection\Stream\StreamProber', get_class($prober));
	}
}
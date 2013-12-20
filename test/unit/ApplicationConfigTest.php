<?php

namespace Phipe;

/**
 * @package Phipe
 */
class ApplicationConfigTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var ApplicationConfig
	 */
	protected $container;

	protected function setUp() {
		$this->container = new ApplicationConfig();
	}

	public function testCreateDefaultValues() {
		$this->assertEquals(array(), $this->container['connections']);
		$this->assertEquals(array(), $this->container['observers']);
		$this->assertTrue($this->container['reconnect']);
	}

	public function testCreateDefaultFactories() {
		$this->assertEquals('Phipe\Connection\Stream\StreamFactory', get_class($this->container['factory']));
		$this->assertEquals('Phipe\Pool', get_class($this->container['pool']));
		$this->assertEquals('Phipe\Loop\Runner', get_class($this->container['loop_runner']));
		$this->assertInstanceOf('SimpleConfig\Container', $this->container['handlers']);
	}

	public function testCreateDefaultHandlers() {
		$this->assertEquals('Phipe\Handler\Connect\Sequential', get_class($this->container['handlers']['connect']));
		$this->assertEquals('Phipe\Handler\Reconnect\SequentialDelayed', get_class($this->container['handlers']['reconnect']));
		$this->assertEquals('Phipe\Handler\Disconnect\Soft', get_class($this->container['handlers']['disconnect']));
		$this->assertEquals('Phipe\Handler\Activity\Simple', get_class($this->container['handlers']['activity']));
	}

	public function testCreateDefaultHandlers_NoReconnect() {
		$this->container['reconnect'] = FALSE;
		$this->assertEquals('Phipe\Handler\Disconnect\Expunging', get_class($this->container['handlers']['disconnect']));
	}
}
<?php

namespace Phipe\Config;

class ContainerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Container
	 */
	protected $container;

	protected function setUp() {
		$this->container = new Container();
	}

	public function testOffsetExists() {
		$this->assertFalse($this->container->offsetExists('foo'));
		$this->container['foo'] = TRUE;
		$this->assertTrue($this->container->offsetExists('foo'));
	}

	public function testOffsetGet() {
		$this->assertNull($this->container->offsetGet('foo'));
		$this->container['foo'] = TRUE;
		$this->assertTrue($this->container->offsetGet('foo'));
	}

	public function testOffsetSet() {
		$this->container->offsetSet('foo', TRUE);
		$this->assertTrue($this->container['foo']);
	}

	public function testOffsetUnset() {
		$this->container['foo'] = TRUE;
		$this->container->offsetUnset('foo');
		$this->assertNull($this->container['foo']);
	}

	public function testFactory() {
		$this->container->factory('foo', function() {
			return TRUE;
		});

		$this->assertTrue($this->container['foo']);
	}

	public function testFactoryExists() {
		$this->assertFalse($this->container->factoryExists('foo'));
		$this->container->factory('foo', function() { });
		$this->assertTrue($this->container->factoryExists('foo'));
	}
}
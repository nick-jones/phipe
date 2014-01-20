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
        $this->assertInstanceOf('SimpleConfig\Container', $this->container['strategies']);
    }

    public function testCreateDefaultStrategies() {
        $this->assertEquals('Phipe\Strategy\Connect\Sequential', get_class($this->container['strategies']['connect']));
        $this->assertEquals('Phipe\Strategy\Reconnect\SequentialDelayed', get_class($this->container['strategies']['reconnect']));
        $this->assertEquals('Phipe\Strategy\Disconnect\Soft', get_class($this->container['strategies']['disconnect']));
        $this->assertEquals('Phipe\Strategy\ActivityDetect\Simple', get_class($this->container['strategies']['activity_detect']));
    }

    public function testCreateDefaultStrategies_NoReconnect() {
        $this->container['reconnect'] = FALSE;
        $this->assertEquals('Phipe\Strategy\Disconnect\Expunging', get_class($this->container['strategies']['disconnect']));
    }
}
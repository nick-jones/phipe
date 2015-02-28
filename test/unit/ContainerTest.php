<?php

namespace Phipe;

use Phipe\Connection\Stream\Factory;
use Phipe\Loop\Runner;

use Phipe\Strategy\ActivityDetect\Simple as ActivityDetect;
use Phipe\Strategy\Connect\Sequential;
use Phipe\Strategy\Disconnect\Expunging;
use Phipe\Strategy\Disconnect\Soft;
use Phipe\Strategy\Reconnect\SequentialDelayed;

use SimpleConfig\Container as SimpleConfigContainer;

/**
 * @package Phipe
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testCreateDefaultValues()
    {
        $this->assertEquals([], $this->container['connections']);
        $this->assertEquals([], $this->container['observers']);
        $this->assertTrue($this->container['reconnect']);
    }

    public function testCreateDefaultFactories()
    {
        $this->assertEquals(Factory::CLASS, get_class($this->container['factory']));
        $this->assertEquals(Pool::CLASS, get_class($this->container['pool']));
        $this->assertEquals(Runner::CLASS, get_class($this->container['loop_runner']));
        $this->assertInstanceOf(SimpleConfigContainer::CLASS, $this->container['strategies']);
    }

    public function testCreateDefaultStrategies()
    {
        $this->assertEquals(Sequential::CLASS, get_class($this->container['strategies']['connect']));
        $this->assertEquals(SequentialDelayed::CLASS, get_class($this->container['strategies']['reconnect']));
        $this->assertEquals(Soft::CLASS, get_class($this->container['strategies']['disconnect']));
        $this->assertEquals(ActivityDetect::CLASS,get_class($this->container['strategies']['activity_detect']));
    }

    public function testCreateDefaultStrategiesWithNoReconnect()
    {
        $this->container['reconnect'] = false;
        $this->assertEquals(Expunging::CLASS, get_class($this->container['strategies']['disconnect']));
    }
}
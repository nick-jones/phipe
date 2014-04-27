<?php

namespace Phipe\Connection\Event;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new EventFactory();
    }

    public function testCreateConnection()
    {
        $connection = $this->factory->createConnection('127.0.0.1', 80);
        $this->assertEquals('Phipe\Connection\Event\EventConnection', get_class($connection));
    }

    public function testCreateProber()
    {
        $prober = $this->factory->createProber();
        $this->assertEquals('Phipe\Connection\Event\EventProber', get_class($prober));
    }
}
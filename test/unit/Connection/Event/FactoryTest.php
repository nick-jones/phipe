<?php

namespace Phipe\Connection\Event;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory = new Factory();
    }

    public function testCreateConnection()
    {
        $connection = $this->factory->createConnection('127.0.0.1', 80);
        $this->assertEquals(Connection::CLASS, get_class($connection));
    }

    public function testCreateProber()
    {
        $prober = $this->factory->createProber();
        $this->assertEquals(Prober::CLASS, get_class($prober));
    }
}
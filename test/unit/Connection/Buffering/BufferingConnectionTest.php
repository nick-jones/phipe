<?php

namespace Phipe\Connection\Buffering;

class BufferingConnectionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BufferingConnection
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection
     */
    protected $proxied;

    protected function setUp() {
        $this->proxied = $this->getMock('\Phipe\Connection\Connection');

        $this->connection = $this->getMockForAbstractClass(
            '\Phipe\Connection\Buffering\BufferingConnection',
            array($this->proxied)
        );
    }

    public function testWrite() {
        $this->proxied
            ->expects($this->at(0))
            ->method('write')
            ->with($this->equalTo("foo\n"));

        $this->proxied
            ->expects($this->at(1))
            ->method('write')
            ->with($this->equalTo("bar\n"));

        $this->connection->write("foo\nba");
        $this->connection->write("r\n");
    }

    public function testPopulateReadBuffer() {
        $this->proxied
            ->expects($this->at(0))
            ->method('read')
            ->will($this->returnValue("foo\nba"));

        $this->proxied
            ->expects($this->at(1))
            ->method('read')
            ->will($this->returnValue("r\n"));

        $this->connection->populateReadBuffer();
        $this->assertEquals("foo\n", $this->connection->read());

        $this->connection->clearReadBuffer();

        $this->connection->populateReadBuffer();
        $this->assertEquals("bar\n", $this->connection->read());
    }

    public function testSetReadBuffer() {
        $this->connection->setReadBuffer('mock');
        $this->assertEquals('mock', $this->connection->read());
    }

    public function testClearReadBuffer() {
        $this->connection->setReadBuffer('mock');
        $this->connection->clearReadBuffer();

        $this->assertEquals('', $this->connection->read());
    }

    public function testRead() {
        $this->connection->setReadBuffer('mock');
        $this->assertEquals('mock', $this->connection->read());
        $this->assertEquals('mock', $this->connection->read()); // idempotent

        $this->connection->setReadBuffer('foo');
        $this->assertEquals('foo', $this->connection->read());
    }
}
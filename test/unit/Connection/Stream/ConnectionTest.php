<?php

namespace Phipe\Connection\Stream;

use Phipe\ResourceTestHelper;

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Connection
     */
    protected $stream;

    /**
     * @var ResourceTestHelper
     */
    protected $resourceTestHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observer;

    const TEST_HOST = '127.0.0.1';

    const TEST_PORT = 83751;

    protected function setUp()
    {
        $handle = fopen('php://memory', 'rw');
        $this->resourceTestHelper = new ResourceTestHelper($handle);
        $this->observer = $this->getMock('\SplObserver');

        $this->stream = new Connection(self::TEST_HOST, self::TEST_PORT);
        $this->stream->setStream($handle);
        $this->stream->attach($this->observer);
    }

    protected function tearDown()
    {
        $this->resourceTestHelper->close();
    }

    public function testRead()
    {
        $payload = 'hello';

        $this->stream->setReadBuffer($payload);

        $result = $this->stream->read();
        $this->assertEquals($payload, $result);
    }

    public function testWrite()
    {
        $payload = 'hello';
        $this->stream->write($payload);

        $result = $this->resourceTestHelper->fetchPayload();
        $this->assertEquals($payload, $result);
    }

    public function testWriteWithNullHandle()
    {
        $this->setExpectedException('\Phipe\Connection\Exception', 'Stream socket is not writable');

        $this->stream->setStream(null);
        $this->stream->write('mock');
    }

    public function testConnect()
    {
        $this->resourceTestHelper = new ResourceTestHelper(
            stream_socket_server(sprintf('tcp://%s:%d', self::TEST_HOST, self::TEST_PORT))
        );

        $this->stream->setStream(null);
        $this->stream->connect();

        $this->assertInternalType('resource', $this->stream->getStream());
    }

    public function testConnectWhenAlreadyConnected()
    {
        $this->setExpectedException('\Phipe\Connection\Exception', 'Already connected');

        $this->stream->connect();
    }

    public function testConnectFailure()
    {
        $this->setExpectedException('\Phipe\Connection\Exception', 'Stream connection failed');

        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->stream), $this->equalTo(\Phipe\Connection::EVENT_CONNECT_FAIL));

        $this->stream->setStream(null);
        $this->stream->connect();
    }

    public function testDisconnect()
    {
        $this->stream->disconnect();

        $this->assertNull($this->stream->getStream());
    }

    public function testDisconnectWhenAlreadyDisconnected()
    {
        $this->setExpectedException('\Phipe\Connection\Exception', 'Already disconnected');

        $this->stream->disconnect();
        $this->stream->disconnect();
    }

    public function testGetStateWhenConnected()
    {
        $state = $this->stream->getState();
        $this->assertGreaterThan(0, Connection::STATE_CONNECTED & $state);
        $this->assertEquals(0, Connection::STATE_DATA_AVAILABLE & $state);
        $this->assertEquals(0, Connection::STATE_EOF & $state);
    }

    public function testGetStateWhenDataAvailable()
    {
        $this->stream->setReadBuffer('mock');

        $state = $this->stream->getState();
        $this->assertGreaterThan(0, Connection::STATE_CONNECTED & $state);
        $this->assertGreaterThan(0, Connection::STATE_DATA_AVAILABLE & $state);
        $this->assertEquals(0, Connection::STATE_EOF & $state);
    }

    public function testGetStateWhenEndOfFile()
    {
        $this->resourceTestHelper->fetchPayload();

        $state = $this->stream->getState();
        $this->assertGreaterThan(0, Connection::STATE_CONNECTED & $state);
        $this->assertEquals(0, Connection::STATE_DATA_AVAILABLE & $state);
        $this->assertGreaterThan(0, Connection::STATE_EOF & $state);
    }

    public function testGetStateWhenDisconnected()
    {
        $this->resourceTestHelper->close();

        $state = $this->stream->getState();
        $this->assertEquals(0, $state);
    }

    public function testPopulateReadBuffer()
    {
        $this->resourceTestHelper->addPayload('mock');

        $this->stream->populateReadBuffer();

        $this->assertEquals('mock', $this->stream->read());
    }

    public function testPopulateReadBufferWithNullHandle()
    {
        $this->setExpectedException('\Phipe\Connection\Exception', 'Stream socket is not readable');

        $this->stream->setStream(null);
        $this->stream->populateReadBuffer();
    }

    public function testClearReadBuffer()
    {
        $this->stream->setReadBuffer('mock');
        $this->assertEquals('mock', $this->stream->read());

        $this->stream->clearReadBuffer();
        $this->assertEquals('', $this->stream->read());
    }
}
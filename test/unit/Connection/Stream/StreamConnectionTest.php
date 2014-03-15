<?php

namespace Phipe\Connection\Stream;

use Phipe\ResourceTestHelper;

require_once __DIR__ . '/../../../helper/ResourceTestHelper.php';

class StreamConnectionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var StreamConnection
     */
    protected $stream;

    /**
     * @var ResourceTestHelper
     */
    protected $resourceTestHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\SplObserver
     */
    protected $observer;

    const TEST_HOST = '127.0.0.1';

    const TEST_PORT = 83751;

    protected function setUp() {
        $handle = fopen('php://memory', 'rw');
        $this->resourceTestHelper = new ResourceTestHelper($handle);
        $this->observer = $this->getMock('\SplObserver');

        $this->stream = new StreamConnection(self::TEST_HOST, self::TEST_PORT);
        $this->stream->setStream($handle);
        $this->stream->attach($this->observer);
    }

    protected function tearDown() {
        $this->resourceTestHelper->close();
    }

    public function testRead() {
        $payload = 'hello';

        $this->stream->setReadBuffer($payload);

        $result = $this->stream->read();
        $this->assertEquals($payload, $result);
    }

    public function testWrite() {
        $payload = 'hello';
        $this->stream->write($payload);

        $result = $this->resourceTestHelper->fetchPayload();
        $this->assertEquals($payload, $result);
    }

    public function testWrite_NullHandle() {
        $this->setExpectedException('\Phipe\Connection\ConnectionException', 'Stream socket is not writable');

        $this->stream->setStream(null);
        $this->stream->write('mock');
    }

    public function testConnect() {
        $this->resourceTestHelper = new ResourceTestHelper(
            stream_socket_server(sprintf('tcp://%s:%d', self::TEST_HOST, self::TEST_PORT))
        );

        $this->stream->setStream(NULL);
        $this->stream->connect();

        $this->assertInternalType('resource', $this->stream->getStream());
    }

    public function testConnect_AlreadyConnected() {
        $this->setExpectedException('\Phipe\Connection\ConnectionException', 'Already connected');

        $this->stream->connect();
    }

    public function testConnect_Failure() {
        $this->setExpectedException('\Phipe\Connection\ConnectionException', 'Stream connection failed');

        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->stream), $this->equalTo(\Phipe\Connection\Connection::EVENT_CONNECT_FAIL));

        $this->stream->setStream(NULL);
        $this->stream->connect();
    }

    public function testDisconnect() {
        $this->stream->disconnect();

        $this->assertNull($this->stream->getStream());
    }

    public function testDisconnect_AlreadyDisconnected() {
        $this->setExpectedException('\Phipe\Connection\ConnectionException', 'Already disconnected');

        $this->stream->disconnect();
        $this->stream->disconnect();
    }

    public function testGetState_Connected() {
        $state = $this->stream->getState();
        $this->assertGreaterThan(0, StreamConnection::STATE_CONNECTED & $state);
        $this->assertEquals(0, StreamConnection::STATE_DATA_AVAILABLE & $state);
        $this->assertEquals(0, StreamConnection::STATE_EOF & $state);
    }

    public function testGetState_DataAvailable() {
        $this->stream->setReadBuffer('mock');

        $state = $this->stream->getState();
        $this->assertGreaterThan(0, StreamConnection::STATE_CONNECTED & $state);
        $this->assertGreaterThan(0, StreamConnection::STATE_DATA_AVAILABLE & $state);
        $this->assertEquals(0, StreamConnection::STATE_EOF & $state);
    }

    public function testGetState_EndOfFile() {
        $this->resourceTestHelper->fetchPayload();

        $state = $this->stream->getState();
        $this->assertGreaterThan(0, StreamConnection::STATE_CONNECTED & $state);
        $this->assertEquals(0, StreamConnection::STATE_DATA_AVAILABLE & $state);
        $this->assertGreaterThan(0, StreamConnection::STATE_EOF & $state);
    }

    public function testGetState_Disconnected() {
        $this->resourceTestHelper->close();

        $state = $this->stream->getState();
        $this->assertEquals(0, $state);
    }

    public function testPopulateReadBuffer() {
        $this->resourceTestHelper->addPayload('mock');

        $this->stream->populateReadBuffer();

        $this->assertEquals('mock', $this->stream->read());
    }

    public function testPopulateReadBuffer_NullHandle() {
        $this->setExpectedException('\Phipe\Connection\ConnectionException', 'Stream socket is not readable');

        $this->stream->setStream(null);
        $this->stream->populateReadBuffer();
    }

    public function testClearReadBuffer() {
        $this->stream->setReadBuffer('mock');
        $this->assertEquals('mock', $this->stream->read());

        $this->stream->clearReadBuffer();
        $this->assertEquals('', $this->stream->read());
    }
}
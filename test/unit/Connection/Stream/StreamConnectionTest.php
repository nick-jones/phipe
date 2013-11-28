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

	protected function setUp() {
		$handle = fopen('php://memory', 'rw');
		$this->resourceTestHelper = new ResourceTestHelper($handle);

		$this->stream = new StreamConnection('127.0.0.1', 80);
		$this->stream->setStream($handle);
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

	public function testConnect() {
		$host = '127.0.0.1';
		$port = 83751;

		$helper = new ResourceTestHelper(
			stream_socket_server(sprintf('tcp://%s:%d', $host, $port))
		);

		$connection = new StreamConnection($host, $port);
		$connection->connect();

		$this->assertInternalType('resource', $connection->getStream());

		$helper->close();
	}

	public function testConnect_AlreadyConnected() {
		$this->setExpectedException('\Phipe\Connection\ConnectionException', 'Already connected');

		$connection = new StreamConnection('example.com', 80);
		$connection->connect();
		$connection->connect();
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

	public function testClearReadBuffer() {
		$this->stream->setReadBuffer('mock');
		$this->assertEquals('mock', $this->stream->read());

		$this->stream->clearReadBuffer();
		$this->assertEquals('', $this->stream->read());
	}
}
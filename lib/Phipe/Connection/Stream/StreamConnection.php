<?php

namespace Phipe\Connection\Stream;

use Phipe\Connection\ConnectionException;

/**
 * Stream based Connection implementation. This utilises resource handles created via "stream_socket_client" for
 * connectivity purposes.
 *
 * @package Phipe\Connection\Stream
 */
class StreamConnection extends \Phipe\Connection\Connection {
    /**
     * @var resource
     */
    protected $stream;

    /**
     * @var string
     */
    protected $readBuffer;

    /**
     * Connects to the host and port supplied during construction.
     *
     * @throws ConnectionException
     */
    public function connect() {
        if ($this->isConnected()) {
            throw new ConnectionException('Already connected', $this);
        }

        $stream = @stream_socket_client($this->getAddress(), $errorNumber, $errorMessage, 10);

        if ($stream === FALSE) {
            $this->notify(self::EVENT_CONNECT_FAIL);

            $message = sprintf('Stream connection failed (%d), message: %s', $errorNumber, $errorMessage);
            throw new ConnectionException($message, $this);
        }

        stream_set_write_buffer($stream, 8192);
        stream_set_timeout($stream, 10);

        $this->stream = $stream;

        $this->notify(self::EVENT_CONNECT);
    }

    /**
     * Disconnects the connection by shutting down the resource handle.
     */
    public function disconnect() {
        if ($this->isDisconnected()) {
            throw new ConnectionException('Already disconnected', $this);
        }

        stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);

        $this->stream = NULL;

        $this->notify(self::EVENT_DISCONNECT);
    }

    /**
     * The read buffer is populated by the "populateReadBuffer" method, so this simply returns whatever data has been
     * added to it.
     *
     * @return string
     */
    public function read() {
        return $this->readBuffer;
    }

    /**
     * Writes data out to the resource handle.
     *
     * @param string $data
     * @throws ConnectionException
     */
    public function write($data) {
        $result = fwrite($this->stream, $data);

        if ($result === FALSE) {
            throw new ConnectionException('Stream socket write failed', $this);
        }

        $this->notify(self::EVENT_WRITE, $data);
    }

    /**
     * Indicates the connection state by interrogating the resource handle and read buffer.
     *
     * @return int
     */
    public function getState() {
        if (!is_resource($this->stream)) {
            return 0;
        }

        $state = self::STATE_CONNECTED;

        if ($this->readBuffer !== NULL) {
            $state |= self::STATE_DATA_AVAILABLE;
        }

        if (feof($this->stream)) {
            $state |= self::STATE_EOF;
        }

        return $state;
    }

    /**
     * Populates the internal read buffer by reading from the resource handle.
     *
     * @throws ConnectionException
     */
    public function populateReadBuffer() {
        $data = fread($this->stream, 8192);

        if ($data === FALSE) {
            throw new ConnectionException('Stream connection read failed', $this);
        }

        $this->setReadBuffer($data);
    }

    /**
     * @param $readBuffer
     */
    public function setReadBuffer($readBuffer) {
        $this->readBuffer = $readBuffer;

        $this->notify(self::EVENT_READ);
    }

    /**
     *
     */
    public function clearReadBuffer() {
        $this->readBuffer = NULL;
    }

    /**
     * @param resource $stream
     */
    public function setStream($stream) {
        $this->stream = $stream;
    }

    /**
     * @return resource
     */
    public function getStream() {
        return $this->stream;
    }

    /**
     * Helper method for constructing an appropriate connection address based on the various parameters available.
     *
     * @return string
     */
    protected function getAddress() {
        $protocol = $this->ssl ? 'ssl' : 'tcp';
        $address = sprintf('%s://%s', $protocol, $this->host);

        if ($this->port) {
            $address .= sprintf(':%d', $this->port);
        }

        return $address;
    }
}
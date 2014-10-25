<?php

namespace Phipe\Connection\Stream;

use Phipe\Connection as BaseConnection;
use Phipe\Connection\Exception;

/**
 * Stream based Connection implementation. This utilises resource handles created via "stream_socket_client" for
 * connectivity purposes.
 *
 * @package Phipe\Connection\Stream
 */
class Connection extends BaseConnection
{
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
     * @throws Exception
     */
    public function connect()
    {
        if ($this->isConnected()) {
            throw new Exception('Already connected', $this);
        }

        $stream = @stream_socket_client($this->getAddress(), $errorNumber, $errorMessage, 10);

        if ($stream === false) {
            $this->handleConnectFailure($errorNumber, $errorMessage);
        }

        stream_set_write_buffer($stream, 8192);
        stream_set_timeout($stream, 10);

        $this->stream = $stream;

        $this->notify(self::EVENT_CONNECT);
    }

    /**
     * Disconnects the connection by shutting down the resource handle.
     */
    public function disconnect()
    {
        if ($this->isDisconnected()) {
            throw new Exception('Already disconnected', $this);
        }

        stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);

        $this->stream = null;

        $this->notify(self::EVENT_DISCONNECT);
    }

    /**
     * The read buffer is populated by the "populateReadBuffer" method, so this simply returns whatever data has been
     * added to it.
     *
     * @return string
     */
    public function read()
    {
        return $this->readBuffer;
    }

    /**
     * Writes data out to the resource handle.
     *
     * @param string $data
     * @throws Exception
     */
    public function write($data)
    {
        if (!is_resource($this->stream)) {
            throw new Exception('Stream socket is not writable', $this);
        }

        $result = fwrite($this->stream, $data);

        if ($result === false) {
            throw new Exception('Stream socket write failed', $this);
        }

        $this->notify(self::EVENT_WRITE, $data);
    }

    /**
     * Indicates the connection state by interrogating the resource handle and read buffer.
     *
     * @return int
     */
    public function getState()
    {
        if (!is_resource($this->stream)) {
            return 0;
        }

        $state = self::STATE_CONNECTED;

        if ($this->readBuffer !== null) {
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
     * @throws Exception
     */
    public function populateReadBuffer()
    {
        if (!is_resource($this->stream)) {
            throw new Exception('Stream socket is not readable', $this);
        }

        $data = fread($this->stream, 8192);

        if ($data === false) {
            throw new Exception('Stream socket read failed', $this);
        }

        $this->setReadBuffer($data);
    }

    /**
     * @param $readBuffer
     */
    public function setReadBuffer($readBuffer)
    {
        $this->readBuffer = $readBuffer;

        $this->notify(self::EVENT_READ);
    }

    /**
     *
     */
    public function clearReadBuffer()
    {
        $this->readBuffer = null;
    }

    /**
     * @param resource|NULL $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param int $errorNumber
     * @param string $errorMessage
     * @throws Exception
     */
    protected function handleConnectFailure($errorNumber, $errorMessage)
    {
        $this->notify(self::EVENT_CONNECT_FAIL);

        $message = sprintf('Stream connection failed (%d), message: %s', $errorNumber, $errorMessage);
        throw new Exception($message, $this);
    }

    /**
     * Helper method for constructing an appropriate connection address based on the various parameters available.
     *
     * @return string
     */
    protected function getAddress()
    {
        $protocol = $this->ssl ? 'ssl' : 'tcp';
        $address = sprintf('%s://%s', $protocol, $this->host);

        if ($this->port) {
            $address .= sprintf(':%d', $this->port);
        }

        return $address;
    }
}
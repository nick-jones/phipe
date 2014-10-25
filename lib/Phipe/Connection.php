<?php

namespace Phipe;

/**
 * Abstract class to represent a connection. Observer methods are implemented concretely, as it seems unlikely
 * subclasses would wish to vary the behaviour of these.
 *
 * @package Phipe
 */
abstract class Connection implements \SplSubject
{
    /**
     * Indicates that the connection is active
     */
    const STATE_CONNECTED = 1;

    /**
     * Indicates that there is data available to be read
     */
    const STATE_DATA_AVAILABLE = 2;

    /**
     * Indicates that this connection has reached the end
     */
    const STATE_EOF = 4;

    /**
     * This connection has connected
     */
    const EVENT_CONNECT = 'connect';

    /**
     * This connection has failed in its attempts to connect
     */
    const EVENT_CONNECT_FAIL = 'connect_fail';

    /**
     * This connection has disconnected
     */
    const EVENT_DISCONNECT = 'disconnect';

    /**
     * This connection has just read some data
     */
    const EVENT_READ = 'read';

    /**
     * This connection has just written some data
     */
    const EVENT_WRITE = 'write';

    /**
     * This connection has reached EOF
     */
    const EVENT_EOF = 'eof';

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var bool
     */
    protected $ssl;

    /**
     * @var \SplObjectStorage|\SplObserver[]
     */
    protected $observers;

    /**
     * @param string|null $host
     * @param int|null $port
     * @param bool $ssl
     */
    public function __construct($host = null, $port = null, $ssl = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->ssl = $ssl;
        $this->observers = new \SplObjectStorage();
    }

    /**
     * Connect to the host and port registered with this class.
     */
    abstract public function connect();

    /**
     * Disconnect the active connection, if applicable.
     */
    abstract public function disconnect();

    /**
     * Write the provided data out.
     *
     * @param string $data
     */
    abstract public function write($data);

    /**
     * Read any available data and return it.
     *
     * @return string
     */
    abstract public function read();

    /**
     * Indicates the state of the connection, by providing a bitmask constructed from the STATE_* constants.
     *
     * @return int
     */
    abstract public function getState();

    /**
     * Convenience method for checking for EOF state.
     *
     * @return bool
     */
    public function isEndOfFile()
    {
        return (self::STATE_EOF & $this->getState()) > 0;
    }

    /**
     * Convenience method for checking for connected state.
     *
     * @return bool
     */
    public function isConnected()
    {
        return (self::STATE_CONNECTED & $this->getState()) > 0;
    }

    /**
     * Convenient method for checking for disconnected state.
     *
     * @return bool
     */
    public function isDisconnected()
    {
        return !$this->isConnected();
    }

    /**
     * @param \SplObjectStorage $observers
     */
    public function setObservers($observers)
    {
        $this->observers = $observers;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @param bool $ssl
     */
    public function setSsl($ssl)
    {
        $this->ssl = $ssl;
    }

    /**
     * @param \SplObserver $observer
     */
    public function attach(\SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    /**
     * @param \SplObserver $observer
     */
    public function detach(\SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    /**
     * Notifies our observers, which event context optionally supplied.
     */
    public function notify($event = null, $data = null)
    {
        foreach ($this->observers as $observer) {
            $observer->update($this, $event, $data);
        }
    }
}
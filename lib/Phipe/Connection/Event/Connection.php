<?php

namespace Phipe\Connection\Event;

use Phipe\Connection as BaseConnection;
use Phipe\Connection\Exception;

/**
 * Connection implementation built on top of the PHP "event" extension, a port of libevent to the PHP infrastructure.
 * This extension can be installed via the PECL repositories, or compiled from scratch, if preferred.
 *
 * This implementation makes use of EventBufferEvent instances, tied to a EventBase instance for connection handling,
 * etc. Callbacks registered with EventBufferEvent allow us to maintain state information.
 *
 * @link http://pecl.php.net/package/event
 * @link http://www.php.net/manual/en/book.event.php
 * @package Phipe\Connector\Event
 */
class Connection extends BaseConnection
{
    /**
     * Base instance for use when creating the EventBufferEvent instance
     *
     * @var \EventBase
     */
    protected $eventBase;

    /**
     * The instance which will provide our connectivity related activity.
     *
     * @var \EventBufferEvent
     */
    protected $bufferEvent;

    /**
     * Since all state information is conveyed via callbacks, we must keep track of it.
     *
     * @var int
     */
    protected $state = 0;

    /**
     * Default options to be used when creating EventBufferEvent instances.
     *
     * @var int
     */
    protected $bufferEventOptions = \EventBufferEvent::OPT_CLOSE_ON_FREE;

    /**
     * Default SSL method to be used when creating EventSslContext instances.
     *
     * @link http://php.net/manual/en/class.eventsslcontext.php
     * @var int
     */
    protected $sslMethod = \EventSslContext::SSLv23_CLIENT_METHOD;

    /**
     * Default options to be passed to EventSslContext.
     *
     * @var array
     */
    protected $sslOptions = array(
        \EventSslContext::OPT_VERIFY_PEER => false,
        \EventSslContext::OPT_ALLOW_SELF_SIGNED => true
    );

    /**
     * Connect to host & port, as provided in the constructor. To be done: SSL & DNS support.
     */
    public function connect()
    {
        $this->applyBufferEventOptions();

        $address = sprintf('%s:%d', $this->host, $this->port);

        $this->getBufferEvent()
            ->connect($address);
    }

    /**
     * Some examples imply forcing the EventBufferEvent::free() call on destruction is useful. Whether or not this is
     * true is not clear right now, but acting on the cautious side, this has been added.
     *
     * @link http://www.php.net/manual/en/event.examples.php
     */
    public function __destruct()
    {
        $this->destroyBufferEvent();
    }

    /**
     * Disconnects the connection. The EventBufferEvent class does not have an explicit disconnect method, but it
     * appears calling free and killing the instance should suffice.
     */
    public function disconnect()
    {
        $this->destroyBufferEvent();
        $this->state = 0;

        $this->notify(self::EVENT_DISCONNECT);
    }

    /**
     * Reads data from the input buffer.
     *
     * @return string
     */
    public function read()
    {
        return $this->getBufferEvent()
            ->getInput()
            ->read(8192);
    }

    /**
     * Writes data to the output buffer.
     *
     * @param string $data
     */
    public function write($data)
    {
        $this->getBufferEvent()
            ->getOutput()
            ->add($data);

        $this->notify(self::EVENT_WRITE, $data);
    }

    /**
     * Returns information about our state. Unlike the Stream implementation, this isn't achieved by interrogating our
     * connection agent, but by just returning our cached state.
     *
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Method called internally by EventBufferEvent when data is ready to be read.
     *
     * @param \EventBufferEvent $bufferEvent
     */
    public function eventRead($bufferEvent)
    {
        $this->state |= self::STATE_DATA_AVAILABLE;

        $this->notify(self::EVENT_READ);
    }

    /**
     * Method called internally by EventBufferEvent when status has changed.
     *
     * @param \EventBufferEvent $bufferEvent
     * @param int $events
     * @throws Exception
     */
    public function eventStatus($bufferEvent, $events)
    {
        if ($events & \EventBufferEvent::CONNECTED) {
            $this->state |= self::STATE_CONNECTED;

            $this->notify(self::EVENT_CONNECT);
        }

        if ($events & \EventBufferEvent::EOF) {
            $this->state |= self::STATE_EOF;

            $this->notify(self::EVENT_EOF);
        }

        if ($events & \EventBufferEvent::ERROR) {
            $this->handleEventError();
        }
    }

    /**
     * @throws Exception
     */
    protected function handleEventError()
    {
        if (!$this->isConnected()) {
            $this->notify(self::EVENT_CONNECT_FAIL);
        }

        $errorNumber = \EventUtil::getLastSocketErrno();
        $errorMessage = \EventUtil::getLastSocketError();
        $message = sprintf('EventBufferEvent received error status (%d), message: %s', $errorNumber, $errorMessage);

        throw new Exception($message, $this);
    }

    /**
     * Destroys our EventBufferEvent instance
     */
    protected function destroyBufferEvent()
    {
        if ($this->bufferEvent) {
            $this->bufferEvent->free();
            $this->bufferEvent = null;
        }
    }

    /**
     * Factory method for creating a EventBufferEvent instance to be used for connections. Callbacks are passed into
     * the constructor, so the instance is ready to be used within this class.
     *
     * @return \EventBufferEvent
     */
    public function createBufferEvent()
    {
        $base = $this->getEventBase();

        return new \EventBufferEvent($base, null, $this->bufferEventOptions);
    }

    /**
     * Factory method for creating EventBufferEvent instances that will talk over SSL.
     *
     * @return \EventBufferEvent
     */
    public function createSslBufferEvent()
    {
        $base = $this->getEventBase();
        $context = new \EventSslContext($this->sslMethod, $this->sslOptions);
        $state = \EventBufferEvent::SSL_CONNECTING;

        return \EventBufferEvent::sslSocket($base, null, $context, $state, $this->sslMethod);
    }

    /**
     *
     */
    protected function applyBufferEventOptions()
    {
        $bufferEvent = $this->getBufferEvent();

        $readCallback = array($this, 'eventRead');
        $statusCallback = array($this, 'eventStatus');

        $bufferEvent->setCallbacks($readCallback, null, $statusCallback);
        $bufferEvent->enable(\Event::READ | \Event::WRITE);
    }

    /**
     * @param \EventBufferEvent $bufferEvent
     */
    public function setBufferEvent($bufferEvent)
    {
        $this->bufferEvent = $bufferEvent;
    }

    /**
     * Retrieve our EventBufferEvent instance. If one does not exist, it will be created.
     *
     * @return \EventBufferEvent
     */
    protected function getBufferEvent()
    {
        if (!$this->bufferEvent) {
            $this->bufferEvent = $this->ssl
                ? $this->createSslBufferEvent()
                : $this->createBufferEvent();
        }

        return $this->bufferEvent;
    }

    /**
     * @param \EventBase $eventBase
     */
    public function setEventBase($eventBase)
    {
        $this->eventBase = $eventBase;
    }

    /**
     * Retrieve our EventBase instance. If one does not exist, it will be created.
     *
     * @return \EventBase
     */
    public function getEventBase()
    {
        return $this->eventBase;
    }
}
<?php

namespace Phipe\Connection\Event;

use Phipe\Connection\Factory as BaseFactory;

/**
 * Factory implementation for the Event implementations of Connection & Prober. Since the Event implementations rely
 * on a shared instance of EventBase, only connections created from a single factory instance should be used with a
 * factory instance from that same factory. Using a different prober instance will result in problems.
 *
 * @package Phipe\Connection\Event
 */
class Factory implements BaseFactory
{
    /**
     * This instance is shared amongst Connections and Probers created via this factory.
     *
     * @var \EventBase
     */
    protected $eventBase;

    /**
     * @param \EventBase|null $eventBase
     */
    public function __construct(\EventBase $eventBase = null)
    {
        if (!$eventBase) {
            $eventBase = new \EventBase();
        }

        $this->eventBase = $eventBase;
    }

    /**
     * @param string $host
     * @param int $port
     * @param bool $ssl
     * @return Connection
     */
    public function createConnection($host, $port, $ssl = false)
    {
        $connection = new Connection($host, $port, $ssl);
        $connection->setEventBase($this->eventBase);
        return $connection;
    }

    /**
     * @return Prober
     */
    public function createProber()
    {
        return new Prober($this->eventBase);
    }
}
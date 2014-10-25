<?php

namespace Phipe;

use Phipe\Connection;

/**
 * Convenience class for watching connections. This implements the SplObserver interface, and provides a simple
 * interface for listening for the various events for which the Connection instances can trigger. The "on()" method
 * provides a fluent way to achieve this.
 *
 * @package Phipe
 */
class Watcher implements \SplObserver
{
    /**
     * @var array
     */
    protected $eventCallbackMappings = array(
        Connection::EVENT_CONNECT => array(),
        Connection::EVENT_CONNECT_FAIL => array(),
        Connection::EVENT_READ => array(),
        Connection::EVENT_WRITE => array(),
        Connection::EVENT_EOF => array(),
        Connection::EVENT_DISCONNECT => array()
    );

    /**
     * Register a callback to be triggered when an event happens.
     *
     * @param string $event
     * @param callable $callback
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function on($event, callable $callback)
    {
        if (!array_key_exists($event, $this->eventCallbackMappings)) {
            throw new \InvalidArgumentException(sprintf('Event "%s" is unrecognised', $event));
        }

        $this->eventCallbackMappings[$event][] = $callback;

        return $this;
    }

    /**
     * @param \SplSubject $subject
     * @param string|null $event
     * @param mixed $data
     */
    public function update(\SplSubject $subject, $event = null, $data = null)
    {
        if ($subject instanceof Connection) {
            $this->connectionUpdate($subject, $event, $data);
        }
    }

    /**
     * @param Connection $connection
     * @param string|null $event
     * @param string $data
     */
    protected function connectionUpdate(Connection $connection, $event, $data)
    {
        if (isset($this->eventCallbackMappings[$event])) {
            $this->executeCallbacks($this->eventCallbackMappings[$event], $connection, $data);
        }
    }

    /**
     * @param callable[] $callbacks
     * @param Connection $connection
     * @param mixed $data
     */
    protected function executeCallbacks($callbacks, Connection $connection, $data)
    {
        foreach ($callbacks as $callback) {
            call_user_func($callback, $connection, $data);
        }
    }
}
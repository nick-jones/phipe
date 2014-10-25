<?php

namespace Phipe;

use Phipe\Connection;

/**
 * This class is a container for connection instances. The interface provides various ways to interact all the
 * Connection instances in a convenient fashion.
 *
 * @package Phipe
 */
class Pool implements \Countable
{
    /**
     * @var \SplObjectStorage
     */
    protected $connections;

    public function __construct()
    {
        $this->connections = new \SplObjectStorage();
    }

    /**
     * Add a Connection instance to the Pool
     *
     * @param Connection $connection
     */
    public function add(Connection $connection)
    {
        $this->connections->attach($connection);
    }

    /**
     * Remove a Connection instance from the Pool
     *
     * @param Connection $connection
     */
    public function remove(Connection $connection)
    {
        $this->connections->detach($connection);
    }

    /**
     * Sets the connections container instance.
     *
     * @param \SplObjectStorage|Connection[] $connections
     */
    public function setConnections($connections)
    {
        $this->connections = $connections;
    }

    /**
     * Applies a user-supplied callback to each Connection instance within the Pool. Each Connection instance will
     * be supplied to the callback.
     *
     * @param callable $callback
     */
    public function walk(callable $callback)
    {
        foreach ($this->connections as $connection) {
            call_user_func($callback, $connection);
        }
    }

    /**
     * Filter the Pool by virtue of a callback. If the callback returns TRUE then the Connection will be included
     * in the returned Pool. As you'd expect, returning FALSE results in exclusion.
     *
     * @param callable $callback The callback to apply to each Connection.
     * @return Pool A new instance of Pool which contains the filtered Connection instances
     */
    public function filter(callable $callback)
    {
        $connections = new self();

        foreach ($this->connections as $connection) {
            if (call_user_func($callback, $connection)) {
                $connections->add($connection);
            }
        }

        return $connections;
    }

    /**
     * Returns a copy of this Pool instance with only those in the supplied state. For example, providing
     * $state = Connection::STATE_CONNECTED will result in a Pool instance containing only connected Connection
     * instances from this Pool.
     *
     * @param int $state The state to search for. Must be a Connection::STATE_* value.
     * @return Pool A new instance of Pool containing only Connection instances holding the supplied state.
     */
    public function getAllWithState($state)
    {
        return $this->filter(
            function (Connection $connection) use ($state) {
                return $state & $connection->getState();
            }
        );
    }

    /**
     * @return int The number of connections contained within this instance.
     */
    public function count()
    {
        return count($this->connections);
    }

    /**
     * Returns the all the Connection instances contained within an array.
     *
     * @return array
     */
    public function toArray()
    {
        $connections = array();

        foreach ($this->connections as $connection) {
            array_push($connections, $connection);
        }

        return $connections;
    }
}
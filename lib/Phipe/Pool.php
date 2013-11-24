<?php

namespace Phipe;

use Phipe\Connection\Connection;

/**
 * This class is a container for connection instances. The interface provides various ways to interact all the
 * Connection instances in a convenient fashion.
 *
 * @package Phipe
 */
class Pool implements \Countable {
	/**
	 * @var \SplObjectStorage
	 */
	protected $connections;

	public function __construct() {
		$this->connections = new \SplObjectStorage();
	}

	/**
	 * Add a Connection instance to the Pool
	 *
	 * @param Connection $connection
	 */
	public function add(Connection $connection) {
		$this->connections->attach($connection);
	}

	/**
	 * Remove a Connection instance from the Pool
	 *
	 * @param Connection $connection
	 */
	public function remove(Connection $connection) {
		$this->connections->detach($connection);
	}

	/**
	 * @param \SplObjectStorage|Connection[] $connections
	 */
	public function setConnections($connections) {
		$this->connections = $connections;
	}

	/**
	 * @param callable $callback
	 */
	public function walk(callable $callback) {
		foreach ($this->connections as $connection) {
			call_user_func($callback, $connection);
		}
	}

	/**
	 * @param callable $callback
	 * @return Pool
	 */
	public function filter(callable $callback) {
		$connections = new self();

		foreach ($this->connections as $connection) {
			if (call_user_func($callback, $connection)) {
				$connections->add($connection);
			}
		}

		return $connections;
	}

	/**
	 * @param $state
	 * @return Pool
	 */
	public function getAllWithState($state) {
		return $this->filter(function(Connection $connection) use($state) {
			return $state & $connection->getState();
		});
	}

	/**
	 * @return int
	 */
	public function count() {
		return count($this->connections);
	}

	/**
	 * Returns the all the Connection instances contained within an array.
	 *
	 * @return array
	 */
	public function toArray() {
		$connections = array();

		foreach ($this->connections as $connection) {
			array_push($connections, $connection);
		}

		return $connections;
	}
}
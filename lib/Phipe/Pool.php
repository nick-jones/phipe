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

	/**
	 *
	 */
	public function __construct() {
		$this->connections = new \SplObjectStorage();
	}

	/**
	 * @param Connection $connection
	 */
	public function add(Connection $connection) {
		// Add to the connection set
		$this->connections->attach($connection);
	}

	/**
	 * @param Connection $connection
	 */
	public function remove(Connection $connection) {
		// Remove from the connection set
		$this->connections->detach($connection);
	}

	/**
	 * @return \SplObjectStorage
	 */
	public function getAll() {
		return $this->connections;
	}

	/**
	 * @param \SplObjectStorage|Connection[] $connections
	 */
	public function setAll($connections) {
		$this->connections = $connections;
	}

	/**
	 * @param callable $callback
	 */
	public function walk(callable $callback) {
		foreach ($this->getAll() as $connection) {
			call_user_func($callback, $connection);
		}
	}

	/**
	 * @param callable $callback
	 * @return Pool
	 */
	public function filter(callable $callback) {
		$connections = new self();

		foreach ($this->getAll() as $connection) {
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
}
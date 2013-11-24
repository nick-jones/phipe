<?php

namespace Phipe\Handler\Reconnect;

use Phipe\Pool;
use Phipe\Connection\Connection;

/**
 * A simple implementation of the Reconnection handler interface. This class will attempt to reconnect *all*
 * disconnected connections at a regular interval (60 seconds is default, but a different delay can be provided).
 *
 * @package Phipe\Handler\Reconnect
 */
class SimpleDelayed implements \Phipe\Handler\Reconnect {
	/**
	 * The delay, in seconds, to wait between each reconnection attempt.
	 *
	 * @var int
	 */
	protected $delay;

	/**
	 * Unix timestamp. This indicates at what time we should next attempt to reconnect. Initial value is 0, so we will
	 * attempt on first request, and then every $delay seconds from then onwards.
	 *
	 * @var int
	 */
	protected $waitUntil = 0;

	/**
	 * @param int $delay Delay to wait before attempting to reconnect any dropped connections.
	 */
	public function __construct($delay = 60) {
		$this->delay = $delay;
	}

	/**
	 * @param Pool $pool
	 */
	public function performReconnect(Pool $pool) {
		if (time() >= $this->waitUntil) {
			$this->reconnectDisconnectedInPool($pool);

			$this->waitUntil = time() + $this->delay;
		}
	}

	/**
	 * Attempts to reconnect any disconnected instances from the Pool.
	 *
	 * @param Pool $pool
	 */
	protected function reconnectDisconnectedInPool(Pool $pool) {
		$connections = $pool->filter(function(Connection $connection) {
			return $connection->isDisconnected();
		});

		$connections->walk(function(Connection $connection) {
			$connection->connect();
		});
	}
}
<?php

namespace Phipe\Handler\Reconnect;

use Phipe\Pool;
use Phipe\Connection\Connection;

/**
 *
 * @package Phipe\Handler\Reconnect
 */
class SimpleDelayed implements \Phipe\Handler\Reconnect {
	/**
	 * @var int
	 */
	protected $delay;

	/**
	 * @var int
	 */
	protected $waitUntil = 0;

	/**
	 * @param int $delay
	 */
	public function __construct($delay = 60) {
		$this->delay = $delay;
	}

	/**
	 *
	 */
	public function performReconnect(Pool $pool) {
		if (time() >= $this->waitUntil) {
			$this->reconnectDisconnectedConnectionsInPool($pool);

			$this->waitUntil = time() + $this->delay;
		}
	}

	/**
	 * @param Pool $pool
	 */
	protected function reconnectDisconnectedConnectionsInPool(Pool $pool) {
		$connections = $pool->filter(function(Connection $connection) {
			return $connection->isDisconnected();
		});

		$connections->walk(function(Connection $connection) {
			$connection->connect();
		});
	}
}
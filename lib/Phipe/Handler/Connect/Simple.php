<?php

namespace Phipe\Handler\Connect;

use Phipe\Pool;
use Phipe\Connection\Connection;

/**
 *
 * @package Phipe\Handler\Connect
 */
class Simple implements \Phipe\Handler\Connect {
	/**
	 * @param Pool $pool
	 */
	public function performConnect(Pool $pool) {
		$this->connectAllConnectionsInPool($pool);
	}

	/**
	 * @param Pool $pool
	 */
	protected function connectAllConnectionsInPool(Pool $pool) {
		$pool->walk(function(Connection $connection) {
			$connection->connect();
		});
	}
}
<?php

namespace Phipe\Handler\Connect;

use Phipe\Pool;
use Phipe\Connection\Connection;

/**
 * A Simple implementation of a Connect handler. This implementation connects all instances sequentially; this may
 * block depending on the Connection implementation, so a more intelligent implementation may be required where large
 * numbers of Connections are in use.
 *
 * @package Phipe\Handler\Connect
 */
class Simple implements \Phipe\Handler\Connect {
	/**
	 * @param Pool $pool
	 */
	public function performConnect(Pool $pool) {
		$this->connectAllInPool($pool);
	}

	/**
	 * @param Pool $pool
	 */
	protected function connectAllInPool(Pool $pool) {
		$pool->walk(function(Connection $connection) {
			$connection->connect();
		});
	}
}
<?php

namespace Phipe\Handler\Disconnect;

use Phipe\Pool;
use Phipe\Connection\Connection;

/**
 *
 * @package Phipe\Handler\Disconnect
 */
class Expunging extends Soft {
	/**
	 * @param Pool $pool
	 */
	public function performDisconnect(Pool $pool) {
		// Perform a soft disconnect first. This will ensure all EOF connections are disconnected.
		parent::performDisconnect($pool);

		$this->removeDisconnectedConnectionsFromPool($pool);
	}

	/**
	 * @param Pool $pool
	 */
	protected function removeDisconnectedConnectionsFromPool(Pool $pool) {
		$connections = $pool->filter(function(Connection $connection) {
			return $connection->isDisconnected();
		});

		$connections->walk(function(Connection $connection) use($pool) {
			$pool->remove($connection);
		});
	}
}
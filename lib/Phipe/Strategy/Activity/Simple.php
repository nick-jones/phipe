<?php

namespace Phipe\Strategy\Activity;

use Phipe\Pool;
use Phipe\Connection\Connection;
use Phipe\Connection\Prober;

/**
 * Simple activity strategy implementation. All connections are pushed to a Prober instance for detecting changed
 * connections.
 *
 * @package Phipe\Strategy\Activity
 */
class Simple implements \Phipe\Strategy\Activity {
	/**
	 * @param Pool $pool
	 * @param Prober $prober
	 */
	public function performDetect(Pool $pool, Prober $prober){
		$this->probeConnectedInPool($pool, $prober);
	}

	/**
	 * Probes all active connected Connection instances from the Pool.
	 *
	 * @param Pool $pool
	 * @param Prober $prober
	 */
	protected function probeConnectedInPool(Pool $pool, Prober $prober) {
		$connections = $pool->getAllWithState(Connection::STATE_CONNECTED)
			->toArray(); // Prober interface expects Connections to be passed in an array

		$prober->probe($connections);
	}
}
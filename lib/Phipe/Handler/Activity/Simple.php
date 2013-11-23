<?php

namespace Phipe\Handler\Activity;

use Phipe\Pool;
use Phipe\Connection\Connection;
use Phipe\Connection\Prober;

/**
 *
 * @package Phipe\Handler\Activity
 */
class Simple implements \Phipe\Handler\Activity {
	/**
	 * @param Pool $pool
	 * @param Prober $prober
	 */
	public function performDetect(Pool $pool, Prober $prober){
		$this->probeAllConnectionsInPool($pool, $prober);
	}

	/**
	 * @param Pool $pool
	 * @param Prober $prober
	 */
	protected function probeAllConnectionsInPool(Pool $pool, Prober $prober) {
		$connections = array();

		$container = $pool->getAllWithState(Connection::STATE_CONNECTED)
			->getAll();

		foreach ($container as $connection) {
			array_push($connections, $connection);
		}

		$prober->probe($connections);
	}
}
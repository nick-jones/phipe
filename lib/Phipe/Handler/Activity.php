<?php

namespace Phipe\Handler;

use Phipe\Pool;
use Phipe\Connection\Prober;

/**
 * Activity handlers should detect changes within the Pools connections. A prober instance is supplied to aid with
 * this process.
 *
 * @package Phipe\Handler
 */
interface Activity {
	/**
	 * @param Pool $pool
	 * @param \Phipe\Connection\Prober $prober
	 */
	public function performDetect(Pool $pool, Prober $prober);
}
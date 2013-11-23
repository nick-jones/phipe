<?php

namespace Phipe\Handler;

use Phipe\Pool;
use Phipe\Connection\Prober;

interface Activity {
	/**
	 * @param Pool $pool
	 * @param \Phipe\Connection\Prober $prober
	 */
	public function performDetect(Pool $pool, Prober $prober);
}
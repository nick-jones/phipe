<?php

namespace Phipe\Handler;

use Phipe\Pool;

interface Reconnect {
	/**
	 * @param Pool $pool
	 */
	public function performReconnect(Pool $pool);
}
<?php

namespace Phipe\Handler;

use Phipe\Pool;

/**
 * Reconnect handlers should reconnect any dropped connections. This and the Connect handlers are going to be
 * reasonably similar in terms of behaviour, so we can consider merging them.
 *
 * @package Phipe\Handler
 */
interface Reconnect {
	/**
	 * @param Pool $pool
	 */
	public function performReconnect(Pool $pool);
}
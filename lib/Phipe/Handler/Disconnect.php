<?php

namespace Phipe\Handler;

use Phipe\Pool;

/**
 * Disconnect handlers should ensure that dropped connections are dealt with, and cleaned up if necessary.
 *
 * @package Phipe\Handler
 */
interface Disconnect {
	/**
	 * @param Pool $pool
	 */
	public function performDisconnect(Pool $pool);
}
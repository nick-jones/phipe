<?php

namespace Phipe\Handler;

use Phipe\Pool;

interface Disconnect {
	/**
	 * @param Pool $pool
	 */
	public function performDisconnect(Pool $pool);
}
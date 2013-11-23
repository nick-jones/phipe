<?php

namespace Phipe\Handler;

use Phipe\Pool;

interface Connect {
	/**
	 * @param Pool $pool
	 */
	public function performConnect(Pool $pool);
}
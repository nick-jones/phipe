<?php

namespace Phipe\Strategy;

use Phipe\Pool;

/**
 * Disconnect strategies should ensure that dropped connections are dealt with, and cleaned up if necessary.
 *
 * @package Phipe\Strategy
 */
interface Disconnect {
    /**
     * @param Pool $pool
     */
    public function performDisconnect(Pool $pool);
}
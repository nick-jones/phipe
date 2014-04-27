<?php

namespace Phipe\Strategy;

use Phipe\Pool;

/**
 * Reconnect strategies should reconnect any dropped connections. This and the Connect strategies are going to be
 * reasonably similar in terms of behaviour, so we can consider merging them.
 *
 * @package Phipe\Strategy
 */
interface Reconnect
{
    /**
     * @param Pool $pool
     */
    public function performReconnect(Pool $pool);
}
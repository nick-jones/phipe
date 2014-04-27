<?php

namespace Phipe\Strategy;

use Phipe\Pool;

/**
 * Connect implementations should ensure that all of the Pools connections are requested to connect. This is likely
 * to be called once only per session.
 *
 * @package Phipe\Strategy
 */
interface Connect
{
    /**
     * @param Pool $pool
     */
    public function performConnect(Pool $pool);
}
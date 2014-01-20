<?php

namespace Phipe\Connection;

/**
 * The Probers role is to look for changed connections. It does not have to return any information, it should simply
 * ensure that those connections update their state, so observers can continue to watch their state, and, if applicable,
 * manage them.
 *
 * @package Phipe
 */
interface Prober {
    /**
     * Check whether any of the supplied connections have changed state, and ensure they update their internal state.
     *
     * @param Connection[] $connections
     */
    public function probe(array $connections);
}
<?php

namespace Phipe\Connection;

use Phipe\Connection;
use Phipe\Connection\Prober;

/**
 * Generic factory for creating Connection related instances.
 *
 * @package Phipe
 */
interface Factory
{
    /**
     * @param $host
     * @param $port
     * @param bool $ssl
     * @internal param array $config
     * @return Connection
     */
    public function createConnection($host, $port, $ssl = false);

    /**
     * @return Prober
     */
    public function createProber();
}
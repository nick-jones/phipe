<?php

namespace Phipe\Connection\Stream;

use Phipe\Connection\Factory as BaseFactory;

/**
 * Simple Factory implementation for Stream implementations of Connection and Prober.
 *
 * @package Phipe\Connection\Stream
 */
class Factory implements BaseFactory
{
    /**
     * @param string $host
     * @param int $port
     * @param bool $ssl
     * @return Connection
     */
    public function createConnection($host, $port, $ssl = false)
    {
        return new Connection($host, $port, $ssl);
    }

    /**
     * @return Prober
     */
    public function createProber()
    {
        return new Prober(new Selector());
    }
}
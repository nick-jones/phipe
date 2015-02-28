<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Prober as BaseProber;

/**
 * Prober for BufferingConnections. This utilises the prober of the original connection.
 *
 * @package Phipe\Connection
 */
class Prober implements BaseProber
{
    /**
     * @var BaseProber
     */
    protected $prober;

    /**
     * @param BaseProber $prober
     */
    public function __construct(BaseProber $prober)
    {
        $this->prober = $prober;
    }

    /**
     * @param Connection[] $connections
     */
    public function probe(array $connections)
    {
        $this->clearConnectionReadBuffers($connections);
        $this->probeProxiedConnections($connections);
        $this->populateConnectionReadBuffers($connections);
    }

    /**
     * The original proxied connections are extracted from the Connection and
     * passed into the appropriate prober (as provided in the constructor)
     *
     * @param Connection[] $connections
     */
    protected function probeProxiedConnections(array $connections)
    {
        $proxied = [];

        foreach ($connections as $connection) {
            array_push($proxied, $connection->getConnection());
        }

        $this->prober->probe($proxied);
    }

    /**
     * Clears the read buffers of the provided connections.
     *
     * @param Connection[] $connections
     */
    protected function clearConnectionReadBuffers(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->clearReadBuffer();
        }
    }

    /**
     * Requests that the provided Connection instances populate their own read buffer.
     *
     * @param Connection[] $connections
     */
    protected function populateConnectionReadBuffers(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->populateReadBuffer();
        }
    }
}
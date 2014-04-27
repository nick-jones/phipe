<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Prober;

/**
 * Prober for BufferingConnections. This utilises the prober of the original connection.
 *
 * @package Phipe\Connection
 */
class BufferingProber implements Prober
{
    /**
     * @var Prober
     */
    protected $prober;

    /**
     * @param Prober $prober
     */
    public function __construct(Prober $prober)
    {
        $this->prober = $prober;
    }

    /**
     * @param BufferingConnection[] $connections
     */
    public function probe(array $connections)
    {
        $this->clearConnectionReadBuffers($connections);
        $this->probeProxiedConnections($connections);
        $this->populateConnectionReadBuffers($connections);
    }

    /**
     * The original proxied connections are extracted from the DecoratingConnection and
     * passed into the appropriate prober (as provided in the constructor)
     *
     * @param BufferingConnection[] $connections
     */
    protected function probeProxiedConnections(array $connections)
    {
        $proxied = array();

        foreach ($connections as $connection) {
            array_push($proxied, $connection->getConnection());
        }

        $this->prober->probe($proxied);
    }

    /**
     * Clears the read buffers of the provided connections.
     *
     * @param BufferingConnection[] $connections
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
     * @param BufferingConnection[] $connections
     */
    protected function populateConnectionReadBuffers(array $connections)
    {
        foreach ($connections as $connection) {
            $connection->populateReadBuffer();
        }
    }
}
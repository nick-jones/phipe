<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Factory;

/**
 * Factory class for creating connections and probers of the Buffering flavour. Since this
 * this family of classes just provide decoration, we must receive a factory instance for
 * true connectivity and probing. This must be provided in the constructor.
 *
 * @package Phipe\Connection\Buffering
 */
class BufferingFactory implements Factory
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $host
     * @param int $port
     * @param bool $ssl
     * @return BufferingConnection
     */
    public function createConnection($host, $port, $ssl = false)
    {
        return new BufferingConnection(
            $this->factory->createConnection($host, $port, $ssl)
        );
    }

    /**
     * @return BufferingProber
     */
    public function createProber()
    {
        return new BufferingProber(
            $this->factory->createProber()
        );
    }
}
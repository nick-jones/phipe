<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Factory as BaseFactory;

/**
 * Factory class for creating connections and probers of the Buffering flavour. Since this
 * this family of classes just provide decoration, we must receive a factory instance for
 * true connectivity and probing. This must be provided in the constructor.
 *
 * @package Phipe\Connection\Buffering
 */
class Factory implements BaseFactory
{
    /**
     * @var BaseFactory
     */
    protected $factory;

    /**
     * @param BaseFactory $factory
     */
    public function __construct(BaseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param string $host
     * @param int $port
     * @param bool $ssl
     * @return Connection
     */
    public function createConnection($host, $port, $ssl = false)
    {
        return new Connection(
            $this->factory->createConnection($host, $port, $ssl)
        );
    }

    /**
     * @return Prober
     */
    public function createProber()
    {
        return new Prober(
            $this->factory->createProber()
        );
    }
}
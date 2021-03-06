<?php

namespace Phipe;

use Phipe\Loop\Worker;
use Phipe\Connection\Prober;
use Phipe\Strategy\Connect;
use Phipe\Strategy\Disconnect;
use Phipe\Strategy\Reconnect;
use Phipe\Strategy\ActivityDetect;

/**
 * A helper class for maintaining Pool collections. This class implements the Worker interface, which allows it to
 * manage Pool instances in an ongoing basis. It will make use a the supplied Prober instance to look for changes,
 * and will handle reconnections, if desired.
 *
 * @package Phipe
 */
class Session implements Worker
{
    /**
     * The pool instance to be managed.
     *
     * @var Pool
     */
    protected $pool;

    /**
     * The prober to look for changed connections with.
     *
     * @var Prober
     */
    protected $prober;

    /**
     * @var array|Container
     */
    protected $strategies;

    /**
     * @param Pool $pool
     * @param Prober $prober
     * @param array|Container $strategies
     */
    public function __construct(Pool $pool, Prober $prober, $strategies)
    {
        $this->pool = $pool;
        $this->prober = $prober;
        $this->strategies = $strategies;
    }

    /**
     * Sets up the Pool ready to be worked.
     */
    public function initialise()
    {
        $this->getConnectStrategy()
            ->performConnect($this->pool);
    }

    /**
     * Works the Pool. Resolves any changed connections, and attempts to reconnect any dropped connections, if
     * applicable.
     */
    public function work()
    {
        $this->getActivityDetectStrategy()
            ->performDetect($this->pool, $this->prober);

        $this->getDisconnectStrategy()
            ->performDisconnect($this->pool);

        $this->getReconnectStrategy()
            ->performReconnect($this->pool);
    }

    /**
     * This session still has work to do whilst the Pool has connections registered.
     *
     * @return bool
     */
    public function hasWork()
    {
        return count($this->pool) > 0;
    }

    /**
     * @return Connect
     */
    protected function getConnectStrategy()
    {
        return $this->strategies['connect'];
    }

    /**
     * @return Reconnect
     */
    protected function getReconnectStrategy()
    {
        return $this->strategies['reconnect'];
    }

    /**
     * @return Disconnect
     */
    protected function getDisconnectStrategy()
    {
        return $this->strategies['disconnect'];
    }

    /**
     * @return ActivityDetect
     */
    protected function getActivityDetectStrategy()
    {
        return $this->strategies['activity_detect'];
    }
}
<?php

namespace Phipe;

use SimpleConfig\Container as BaseContainer;

/**
 *
 * @package Phipe
 */
class Container extends BaseContainer
{
    /**
     * @param array $values
     * @param array $factories
     */
    public function __construct(array $values = [], array $factories = [])
    {
        $values += $this->createDefaultValues();
        $factories += $this->createDefaultFactories();

        parent::__construct($values, $factories);
    }

    /**
     * @return array
     */
    protected function createDefaultValues()
    {
        $values = [
            'connections' => [],
            'observers' => [],
            'reconnect' => true
        ];

        return $values;
    }

    /**
     * @return array
     */
    protected function createDefaultFactories()
    {
        $factories = [
            'factory' => function () {
                return new Connection\Stream\Factory();
            },
            'pool' => function () {
                return new Pool();
            },
            'loop_runner' => function () {
                return new Loop\Runner();
            },
            'strategies' => function () {
                return new BaseContainer([], $this->createDefaultStrategies());
            }
        ];

        return $factories;
    }

    /**
     * @return array
     */
    protected function createDefaultStrategies()
    {
        $strategies = [
            'connect' => function () {
                return new Strategy\Connect\Sequential();
            },
            'reconnect' => function () {
                return new Strategy\Reconnect\SequentialDelayed();
            },
            'disconnect' => function () {
                return $this['reconnect'] ?
                    new Strategy\Disconnect\Soft() :
                    new Strategy\Disconnect\Expunging();
            },
            'activity_detect' => function () {
                return new Strategy\ActivityDetect\Simple();
            }
        ];

        return $strategies;
    }
}
<?php

namespace Phipe\Stub;

/**
 * EventBase is a final class, so cannot be mocked. This provides a copy of the interface, for use within
 * unit tests.
 *
 * @package Phipe\Stub
 */
interface EventBase {
    /**
     * @param \EventConfig|null $cfg
     */
    public function __construct (\EventConfig $cfg = NULL);

    /**
     * @return bool
     */
    public function dispatch();

    /**
     * @return int
     */
    public function getFeatures();

    /**
     * @param \EventConfig|null $cfg
     * @return string
     */
    public function getMethod(\EventConfig $cfg = NULL);

    /**
     * @return double
     */
    public function getTimeOfDayCached();

    /**
     * @return bool
     */
    public function gotExit();

    /**
     * @return bool
     */
    public function gotStop();

    /**
     * @param int $flags
     * @return bool
     */
    public function loop($flags = 0);

    /**
     * @param string $n_priorities
     * @return bool
     */
    public function priorityInit($n_priorities);

    /**
     * @param string $base
     */
    public function reInit($base);

    /**
     * @return bool
     */
    public function stop();
}
<?php

namespace Phipe\Loop;

/**
 * Class to run long running jobs.
 *
 * @package Phipe\Loop
 */
class Runner {
    /**
     * @var bool
     */
    protected $running = FALSE;

    /**
     * Loops, asking the worker to perform it's work. If the worker indicates there is no further work to do, it will
     * exit.
     *
     * @param Worker $worker
     */
    public function loop(Worker $worker) {
        $this->running = TRUE;

        $worker->initialise();

        while ($this->running && $worker->hasWork()) {
            $worker->work();
        }

        $this->stop();
    }

    /**
     *
     */
    public function stop() {
        $this->running = FALSE;
    }

    /**
     * @return bool
     */
    public function isRunning() {
        return $this->running;
    }
}
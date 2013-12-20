<?php

namespace Phipe;

use Phipe\Pool;
use Phipe\Connection\Prober;
use Phipe\Handler\Connect;
use Phipe\Handler\Disconnect;
use Phipe\Handler\Reconnect;
use Phipe\Handler\Activity;

/**
 * A helper class for maintaining Pool collections. This class implements the Worker interface, which allows it to
 * manage Pool instances in an ongoing basis. It will make use a the supplied Prober instance to look for changes,
 * and will handle reconnections, if desired.
 *
 * @package Phipe
 */
class Session implements \Phipe\Loop\Worker {
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
	 * @var array|ApplicationConfig
	 */
	protected $handlers;

	/**
	 * @param Pool $pool
	 * @param Prober $prober
	 * @param array|ApplicationConfig $handlers
	 */
	public function __construct(Pool $pool, Prober $prober, $handlers) {
		$this->pool = $pool;
		$this->prober = $prober;
		$this->handlers = $handlers;
	}

	/**
	 * Sets up the Pool ready to be worked.
	 */
	public function initialise() {
		$this->getConnectHandler()
			->performConnect($this->pool);
	}

	/**
	 * Works the Pool. Resolves any changed connections, and attempts to reconnect any dropped connections, if
	 * applicable.
	 */
	public function work() {
		$this->getActivityHandler()
			->performDetect($this->pool, $this->prober);

		$this->getDisconnectHandler()
			->performDisconnect($this->pool);

		$this->getReconnectHandler()
			->performReconnect($this->pool);
	}

	/**
	 * This session still has work to do whilst the Pool has connections registered.
	 *
	 * @return bool
	 */
	public function hasWork() {
		return $this->pool->count() > 0;
	}

	/**
	 * @return Connect
	 */
	protected function getConnectHandler() {
		return $this->handlers['connect'];
	}

	/**
	 * @return Reconnect
	 */
	protected function getReconnectHandler() {
		return $this->handlers['reconnect'];
	}

	/**
	 * @return Disconnect
	 */
	protected function getDisconnectHandler() {
		return $this->handlers['disconnect'];
	}

	/**
	 * @return Activity
	 */
	protected function getActivityHandler() {
		return $this->handlers['activity'];
	}
}
<?php

namespace Phipe;

use SimpleConfig\Container;

/**
 *
 * @package Phipe
 */
class ApplicationConfig extends Container {
	/**
	 * @param array $values
	 * @param array $factories
	 */
	public function __construct(array $values = array(), array $factories = array()) {
		$values += $this->createDefaultValues();
		$factories += $this->createDefaultFactories();

		parent::__construct($values, $factories);
	}

	/**
	 * @return array
	 */
	protected function createDefaultValues() {
		$values = array(
			'connections' => array(),
			'observers' => array(),
			'reconnect' => TRUE
		);

		return $values;
	}

	/**
	 * @return array
	 */
	protected function createDefaultFactories() {
		$factories = array(
			'factory' => function() {
				return new Connection\Stream\StreamFactory();
			},
			'pool' => function() {
				return new Pool();
			},
			'loop_runner' => function() {
				return new Loop\Runner();
			},
			'strategies' => function() {
				return new Container(array(), $this->createDefaultStrategies());
			}
		);

		return $factories;
	}

	/**
	 * @return array
	 */
	protected function createDefaultStrategies() {
		$strategies = array(
			'connect' => function() {
				return new Strategy\Connect\Sequential();
			},
			'reconnect' => function() {
				return new Strategy\Reconnect\SequentialDelayed();
			},
			'disconnect' => function() {
				return $this['reconnect'] ?
					new Strategy\Disconnect\Soft() :
					new Strategy\Disconnect\Expunging();
			},
			'activity' => function() {
				return new Strategy\Activity\Simple();
			}
		);

		return $strategies;
	}
}
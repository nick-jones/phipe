<?php

namespace Phipe;

/**
 *
 * @package Phipe
 */
class ApplicationContainer extends \Phipe\Config\Container {
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
			'handlers' => function() {
				return new Config\Container(array(), $this->createDefaultHandlers());
			}
		);

		return $factories;
	}

	/**
	 * @return array
	 */
	protected function createDefaultHandlers() {
		$handlers = array(
			'connect' => function() {
				return new Handler\Connect\Sequential();
			},
			'reconnect' => function() {
				return new Handler\Reconnect\SequentialDelayed();
			},
			'disconnect' => function() {
				return $this['reconnect'] ?
					new Handler\Disconnect\Soft() :
					new Handler\Disconnect\Expunging();
			},
			'activity' => function() {
				return new Handler\Activity\Simple();
			}
		);

		return $handlers;
	}
}
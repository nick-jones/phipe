<?php

namespace Phipe\Connection\Decorating;

/**
 * Prober for BufferingConnections. This utilises the prober of the original connection.
 *
 * @package Phipe\Connection
 */
class DecoratingProber implements \Phipe\Connection\Prober {
	/**
	 * @var \Phipe\Connection\Prober
	 */
	protected $prober;

	/**
	 * @param \Phipe\Connection\Prober $prober
	 */
	public function __construct(\Phipe\Connection\Prober $prober) {
		$this->prober = $prober;
	}

	/**
	 * The original proxied connections are extracted from the DecoratingConnection and
	 * passed into the appropriate prober (as provided in the constructor)
	 *
	 * @param DecoratingConnection[] $connections
	 */
	public function probe(array $connections) {
		$proxied = array();

		foreach ($connections as $connection) {
			array_push($proxied, $connection->getConnection());
		}

		$this->prober->probe($proxied);
	}
}
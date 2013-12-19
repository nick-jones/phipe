<?php

namespace Phipe\Connection\Buffering;

use Phipe\Connection\Decorating\DecoratingProber;

/**
 * Factory class for creating connections and probers of the Buffering flavour. Since this
 * this family of classes just provide decoration, we must receive a factory instance for
 * true connectivity and probing. This must be provided in the constructor.
 *
 * @package Phipe\Connection\Buffering
 */
class BufferingFactory implements \Phipe\Connection\Factory {
	/**
	 * @param \Phipe\Connection\Factory $factory
	 */
	public function __construct(\Phipe\Connection\Factory $factory) {
		$this->factory = $factory;
	}

	/**
	 * @param string $host
	 * @param int $port
	 * @param bool $ssl
	 * @return BufferingConnection
	 */
	public function createConnection($host, $port, $ssl = FALSE) {
		return new BufferingConnection(
			$this->factory->createConnection($host, $port, $ssl)
		);
	}

	/**
	 * @return DecoratingProber
	 */
	public function createProber() {
		return new DecoratingProber(
			$this->factory->createProber()
		);
	}
}
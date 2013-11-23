<?php

namespace Phipe\Connection\Stream;

/**
 * Simple Factory implementation for Stream implementations of Connection and Prober.
 *
 * @package Phipe\Connection\Stream
 */
class StreamFactory implements \Phipe\Connection\Factory {
	/**
	 * @param string $host
	 * @param int $port
	 * @param bool $ssl
	 * @return StreamConnection
	 */
	public function createConnection($host, $port, $ssl = FALSE) {
		return new StreamConnection($host, $port, $ssl);
	}

	/**
	 * @return StreamProber
	 */
	public function createProber() {
		return new StreamProber(new Selector());
	}
}
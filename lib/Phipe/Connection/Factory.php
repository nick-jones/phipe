<?php

namespace Phipe\Connection;

/**
 * Generic factory for creating Connection related instances.
 *
 * @package Phipe
 */
interface Factory {
	/**
	 * @param $host
	 * @param $port
	 * @param bool $ssl
	 * @internal param array $config
	 * @return \Phipe\Connection\Connection
	 */
	public function createConnection($host, $port, $ssl = FALSE);

	/**
	 * @return \Phipe\Connection\Prober
	 */
	public function createProber();
}
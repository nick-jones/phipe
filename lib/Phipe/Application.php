<?php

namespace Phipe;

use Phipe\Connection\Connection;
use Phipe\Config\Container;

/**
 * This class aims to provide an easy interface to the various connection components. Based on the provided config,
 * it sets up everything required to connect to one or more connections, and manage it/them on an on-going basis.
 *
 * It is, of course, not required that this be used - please extract the plumbing used here if you wish to perform
 * more fine-grained connection handling using the various components.
 *
 * @package Phipe
 */
class Application {
	/**
	 * @var Container
	 */
	protected $config;

	/**
	 * The $config array can contain the following values:
	 *  - connections: array of array-based connection details (host/port/ssl), or actual instances themselves.
	 *  - pool: an instance of \Phipe\Pool (optional)
	 *  - factory: a concrete instance of \Phipe\Connection\Factory (optional, \Phipe\Stream\StreamFactory is default)
	 *  - loop_runner: an instance of \Phipe\Loop\Runner (optional)
	 *
	 * @param array|Container|NULL $config
	 */
	public function __construct($config = NULL) {
		$this->setConfig($config);
	}

	/**
	 * Sets up all the required components, and runs the Manager via a Loop Runner.
	 */
	public function execute() {
		$pool = $this->getPool();
		$this->preparePool($pool);

		$prober = $this->getFactory()
			->createProber();

		$handlers = $this->getHandlers();

		$session = new Session($pool, $prober, $handlers);

		$this->getLoop()
			->loop($session);
	}

	/**
	 * Prepares the provided Pool instance for work. Connections are added, and observers are attached.
	 *
	 * @param Pool $pool
	 */
	protected function preparePool(Pool $pool) {
		foreach ($this->createConnections() as $connection) {
			$this->attachObserversToConnection($connection);
			$pool->add($connection);
		}
	}

	/**
	 * @param Connection $connection
	 */
	protected function attachObserversToConnection(Connection $connection) {
		foreach ($this->getObservers() as $observer) {
			$connection->attach($observer);
		}
	}

	/**
	 * Creates connections based on the information provided in the config.
	 *
	 * @return Connection[]
	 */
	protected function createConnections() {
		$connections = array();

		foreach ($this->config['connections'] as $connection) {
			if (is_array($connection)) {
				$connection = $this->createConnectionFromConfig($connection);
			}

			array_push($connections, $connection);
		}

		return $connections;
	}

	/**
	 * Creates a connection instance from config information. The $config array should contain the following details:
	 *  - host: the hostname/IPv4 address to connect to
	 *  - port: the port number to connect to
	 *  - ssl: whether or not ssl should be used (optional, default is FALSE)
	 *
	 * @param array $config
	 * @return \Phipe\Connection\Connection
	 */
	protected function createConnectionFromConfig(array $config) {
		$host = $config['host'];
		$port = $config['port'];
		$ssl = isset($config['ssl']) ? $config['ssl'] : FALSE;

		return $this->getFactory()
			->createConnection($host, $port, $ssl);
	}

	/**
	 * Sets the config class property. If an array is provided, a Container instance is created containing the provided
	 * array, and a default set of factories relevant to this class.
	 *
	 * @param array|Container $config
	 */
	public function setConfig($config) {
		if (is_array($config)) {
			$config = new ApplicationContainer($config);
		}

		$this->config = $config;
	}

	/**
	 * @return array|Container
	 */
	protected function getHandlers() {
		return $this->config['handlers'];
	}

	/**
	 * Fetches any observers that were provided in the config.
	 *
	 * @return \SplObserver[]
	 */
	protected function getObservers() {
		return $this->config['observers'];
	}

	/**
	 * @return \Phipe\Connection\Factory
	 */
	protected function getFactory() {
		return $this->config['factory'];
	}

	/**
	 * @return \Phipe\Pool
	 */
	protected function getPool() {
		return $this->config['pool'];
	}

	/**
	 * @return \Phipe\Loop\Runner
	 */
	protected function getLoop() {
		return $this->config['loop_runner'];
	}
}
<?php

namespace Phipe\Connection;

/**
 * Base exception class for Connection related issues.
 *
 * @package Phipe
 */
class ConnectionException extends \RuntimeException {
	/**
	 * @var Connection
	 */
	protected $connection;

	/**
	 * @param string $message
	 * @param Connection $connection
	 * @param \Exception $previous
	 */
	public function __construct($message, Connection $connection, \Exception $previous = NULL) {
		parent::__construct($message, 0, $previous);

		$this->connection = $connection;
	}

	/**
	 * @return Connection
	 */
	public function getConnection() {
		return $this->connection;
	}
}
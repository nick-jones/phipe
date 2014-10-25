<?php

namespace Phipe\Connection;

use Phipe\Connection;

/**
 * Base exception class for Connection related issues.
 *
 * @package Phipe
 */
class Exception extends \RuntimeException
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param string $message
     * @param Connection $connection
     * @param \Exception $previous
     */
    public function __construct($message, Connection $connection, \Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
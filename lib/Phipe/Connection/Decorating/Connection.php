<?php

namespace Phipe\Connection\Decorating;

use Phipe\Connection as BaseConnection;
use Phipe\Connection\NotificationPropagator;

/**
 * This class provides a means to decorate transport implementing connections (e.g. Stream and Event based). Method
 * calls are pushed into the internal instance. Notifications are relayed via the NotificationPropagator. Concrete
 * decorating implementations can simply override relevant methods and adjust data as suited.
 *
 * @package Phipe\Connection
 */
abstract class Connection extends BaseConnection
{
    /**
     * @var BaseConnection
     */
    protected $connection;

    /**
     * @var NotificationPropagator
     */
    protected $notificationPropagator;

    /**
     * @var array
     */
    protected $eventIgnores = [];

    /**
     * @param BaseConnection $connection
     */
    public function __construct(BaseConnection $connection = null)
    {
        $this->setConnection($connection);

        parent::__construct(null);
    }

    /**
     * @param BaseConnection|null $connection
     */
    public function setConnection(BaseConnection $connection = null)
    {
        $this->connection = $connection;

        if ($connection !== null) {
            $this->setupNotificationPropagator($connection);
        }
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param BaseConnection $connection
     */
    protected function setupNotificationPropagator(BaseConnection $connection)
    {
        $notificationPropagator = new NotificationPropagator($this, $connection, $this->eventIgnores);
        $notificationPropagator->initialise();

        $this->setNotificationPropagator($notificationPropagator);
    }

    /**
     * @param NotificationPropagator $notificationPropagator
     */
    public function setNotificationPropagator(NotificationPropagator $notificationPropagator)
    {
        $this->notificationPropagator = $notificationPropagator;
    }

    /**
     * Connect to the registered host and port.
     */
    public function connect()
    {
        $this->connection->connect();
    }

    /**
     * Disconnect the active connection.
     */
    public function disconnect()
    {
        $this->connection->disconnect();
    }

    /**
     * @param string $data
     */
    public function write($data)
    {
        $this->connection->write($data);
    }

    /**
     * @return string
     */
    public function read()
    {
        return $this->connection->read();
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->connection->getState();
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->connection->setHost($host);
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->connection->setPort($port);
    }

    /**
     * @param bool $ssl
     */
    public function setSsl($ssl)
    {
        $this->connection->setSsl($ssl);
    }
}
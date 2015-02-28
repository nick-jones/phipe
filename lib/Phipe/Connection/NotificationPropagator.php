<?php

namespace Phipe\Connection;

use Phipe\Connection;

/**
 * This class provides a way to propagate notifications from on Connection instance to another. The
 * class attaches itself to the original, and relays notifications to the other instance. This is useful
 * when decorating connections, as the decorated object cannot be watched for activity, since it is
 * the proxied object that actually generates it.
 *
 * @package Phipe\Connection
 */
class NotificationPropagator implements \SplObserver
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Connection
     */
    protected $proxied;

    /**
     * @var array
     */
    protected $ignoreEvents = [];

    /**
     * @param Connection $connection Connection for propagating *to*
     * @param Connection $proxied Connection for propagating *from
     * @param array $ignoreEvents
     */
    public function __construct(Connection $connection, Connection $proxied, array $ignoreEvents = [])
    {
        $this->connection = $connection;
        $this->proxied = $proxied;
        $this->ignoreEvents = $ignoreEvents;
    }

    /**
     * Attaches this instance to the proxied connection.
     */
    public function initialise()
    {
        $this->proxied->attach($this);
    }

    /**
     * @param \SplSubject $subject
     * @param string|null $event
     * @param string|null $data
     */
    public function update(\SplSubject $subject, $event = null, $data = null)
    {
        if (!in_array($event, $this->ignoreEvents)) {
            $this->connection->notify($event, $data);
        }
    }
}
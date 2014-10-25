<?php

namespace Phipe\Strategy\Disconnect;

use Phipe\Pool;
use Phipe\Connection;

/**
 * An implementation of the Disconnect strategy interface that expunges Connections from the supplied Pool, if they
 * are disconnected. This is useful is we do not wish for disconnected instances to be reconnected.
 *
 * This class extends the Soft strategy to ensure that EOF connections are cleaned up.
 *
 * @package Phipe\Strategy\Disconnect
 */
class Expunging extends Soft
{
    /**
     * @param Pool $pool
     */
    public function performDisconnect(Pool $pool)
    {
        // Perform a soft disconnect first. This will ensure all EOF connections are disconnected.
        parent::performDisconnect($pool);

        $this->removeDisconnectedFromPool($pool);
    }

    /**
     * Removes all connection instances which report themselves as disconnected.
     *
     * @param Pool $pool The instance from which the connections should be fetched and removed
     */
    protected function removeDisconnectedFromPool(Pool $pool)
    {
        // Resolve the disconnected connections
        $connections = $pool->filter(
            function (Connection $connection) {
                return $connection->isDisconnected();
            }
        );

        // Remove each one from the Pool
        $connections->walk(
            function (Connection $connection) use ($pool) {
                $pool->remove($connection);
            }
        );
    }
}
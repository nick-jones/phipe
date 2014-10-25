<?php

namespace Phipe\Strategy\Disconnect;

use Phipe\Pool;
use Phipe\Connection;
use Phipe\Strategy\Disconnect;

/**
 * A disconnection strategy that acts in a "soft" manor. It simply looks for EOF connections and ensures they are then
 * subsequently disconnected. It does not remove any connections from the Pool, so this is useful in instances where
 * you wish to reconnect such connections.
 *
 * @package Phipe\Strategy\Disconnect
 */
class Soft implements Disconnect
{
    /**
     *
     */
    public function performDisconnect(Pool $pool)
    {
        $this->disconnectEndOfFileConnectionsInPool($pool);
    }

    /**
     * @param Pool $pool
     */
    protected function disconnectEndOfFileConnectionsInPool(Pool $pool)
    {
        $connections = $pool->getAllWithState(Connection::STATE_EOF);

        $connections->walk(
            function (Connection $connection) {
                $connection->disconnect();
            }
        );
    }
}
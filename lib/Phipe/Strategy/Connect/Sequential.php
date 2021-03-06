<?php

namespace Phipe\Strategy\Connect;

use Phipe\Pool;
use Phipe\Connection;
use Phipe\Connection\Exception;
use Phipe\Strategy\Connect;

/**
 * A simple implementation of a Connect strategy. This implementation connects all instances sequentially; this may
 * block depending on the Connection implementation, so a more intelligent implementation may be required where large
 * numbers of Connections are in use.
 *
 * @package Phipe\Strategy\Connect
 */
class Sequential implements Connect
{
    /**
     * @param Pool $pool
     */
    public function performConnect(Pool $pool)
    {
        $this->connectAllInPool($pool);
    }

    /**
     * @param Pool $pool
     */
    protected function connectAllInPool(Pool $pool)
    {
        $pool->walk(
            function (Connection $connection) {
                try {
                    $connection->connect();
                } catch (Exception $e) {
                    // If any cannot connect, we will retry later. No need for any one connection to
                    // disrupt other connections.
                }
            }
        );
    }
}
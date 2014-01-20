<?php

namespace Phipe\Strategy\Connect;

use Phipe\Pool;
use Phipe\Connection\Connection;

/**
 * A simple implementation of a Connect strategy. This implementation connects all instances sequentially; this may
 * block depending on the Connection implementation, so a more intelligent implementation may be required where large
 * numbers of Connections are in use.
 *
 * @package Phipe\Strategy\Connect
 */
class Sequential implements \Phipe\Strategy\Connect {
    /**
     * @param Pool $pool
     */
    public function performConnect(Pool $pool) {
        $this->connectAllInPool($pool);
    }

    /**
     * @param Pool $pool
     */
    protected function connectAllInPool(Pool $pool) {
        $pool->walk(function(Connection $connection) {
            $connection->connect();
        });
    }
}
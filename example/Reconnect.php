<?php

/*
 * This example connects to an IRC server. It will not respond to PING requests, resulting
 * is the connection being dropped by the server. This demonstrates reconnection handling,
 * as a reconnect should occur in a timely fashion.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Phipe\Executor;
use Phipe\Watcher;
use Phipe\Connection;
use Phipe\Connection\Buffering\Factory as BufferingFactory;
use Phipe\Connection\Stream\Factory as StreamFactory;

$watcher = new Watcher();

$watcher->on(
    'connect',
    function (Connection $connection) {
        echo 'Connected...' . PHP_EOL;

        $connection->write("NICK monkey_brain\n");
        $connection->write("USER monkey  8 * :monkey'\n");
    }
);

$watcher->on(
    'connect_fail',
    function (Connection $connection) {
        echo 'Connection failed' . PHP_EOL;
    }
);

$watcher->on(
    'read',
    function (Connection $connection) {
        $lines = preg_split("#\r?\n#", $connection->read(), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($lines as $line) {
            echo "← {$line}\n";
        }
    }
);

$watcher->on(
    'write',
    function (Connection $connection, $data) {
        echo "→ {$data}";
    }
);

$watcher->on(
    'eof',
    function (Connection $connection) {
        echo PHP_EOL;
        echo 'EOF' . PHP_EOL;
    }
);

$watcher->on(
    'disconnect',
    function (Connection $connection) {
        echo PHP_EOL;
        echo 'Disconnected' . PHP_EOL;
    }
);

$factory = new BufferingFactory(new StreamFactory());

$phipe = new Executor([
    'connections' => [
        ['host' => '108.61.240.240', 'port' => 6667], // DALnet
    ],
    'observers' => [
        $watcher
    ],
    'factory' => $factory,
    'reconnect' => true // If this were FALSE, the loop would exit.
]);

$phipe->execute();
<?php

/*
 * This example connects to an IRC server. It will not respond to PING requests, resulting
 * is the connection being dropped by the server. This demonstrates reconnection handling,
 * as a reconnect should occur in a timely fashion.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use \Phipe\Watcher;
use \Phipe\Connection\Connection;

$watcher = new Watcher();

$watcher->on('connect', function(Connection $connection) {
	echo 'Connected...' . PHP_EOL;

	$connection->write("NICK monkey_brain\n");
	$connection->write("USER monkey  8 * :monkey'\n");
});

$watcher->on('read', function(Connection $connection) {
	$lines = preg_split("#\r?\n#", $connection->read(), -1, PREG_SPLIT_NO_EMPTY);

	foreach ($lines as $line) {
		echo "← {$line}\n";
	}
});

$watcher->on('write', function(Connection $connection, $data) {
	echo "→ {$data}";
});

$watcher->on('eof', function(Connection $connection) {
	echo PHP_EOL;
	echo 'EOF' . PHP_EOL;
});

$watcher->on('disconnect', function(Connection $connection) {
	echo PHP_EOL;
	echo 'Disconnected' . PHP_EOL;
});

$phipe = new \Phipe\Application([
	'connections' => [
		['host' => '108.61.240.240', 'port' => 6667], // DALnet
	],
	'observers' => [
		$watcher
	],
	'factory' => $factory,
	'reconnect' => TRUE // If this were FALSE, the loop would exit.
]);

$phipe->execute();
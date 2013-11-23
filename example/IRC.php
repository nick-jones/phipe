<?php

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

		$command = array_shift(explode(' ', $line));

		if ($command === 'PING') {
			$token = array_pop(explode(':', $line, 2));
			$connection->write("PONG :{$token}\n");
		}
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

//$factory = new \Phipe\Connection\Event\EventFactory();
$factory = new \Phipe\Connection\Stream\StreamFactory();

$phipe = new \Phipe\Application([
	'connections' => [
		['host' => '108.61.240.240', 'port' => 6667], // DALnet
		['host' => '76.72.161.35', 'port' => 6667] // IRCHighway
	],
	'observers' => [
		$watcher
	],
	'factory' => $factory
]);

$phipe->execute();
# Phipe (Alpha)

Phipe is a parallel connection handling library, for PHP.

It provides abstractions on top of PHP streams and the PHP [Event extension](http://php.net/event) to provide a clean
and simple interface to manage and observe multiple connections at once.

__Caveat emptor__: This is not complete, and not production ready. Whilst a lot of the moving parts are functioning,
there is still work to be done.

## Installation

To pull down dependencies and check version compatibility you will need to run [composer](http://getcomposer.org) in
the project root. If you wish to use Phipe in a project, require it in your composer.json. Please note that as this is
still in development, there are no tagged versions; you will have to require "dev-master", and include the
repository details.

## Usage

If you wish to have your connections managed by the bundled connection session manager, then you simply need to supply
connection details to the `\Phipe\Application` class:

```php
$phipe = new \Phipe\Application([
    'connections' => [
        ['host' => '127.0.0.1', 'port' => 80],
        ['host' => '127.0.0.1', 'port' => 80]
    ]
]);

$phipe->execute();
```

This will do nothing more than connect to localhost on port 80 twice, and continually read data, until end of file
has been reached.

It is likely that you will wish to read from and write to the connection(s) being managed. This is made possible by
utilising observers that implement the SplObserver interface:

```php
class MyObserver implements \SplObserver {
    public function update(\SplSubject $subject, $event = NULL) {
        echo $event . PHP_EOL;
    }
}

$observer = new MyObserver();

$phipe = new \Phipe\Application([
    'connections' => [
        ['host' => '127.0.0.1', 'port' => 80]
    ],
    'observers' => [
        $observer
    ]
]);

$phipe->execute();
```

You can then go ahead and implement behaviour for event distinction and connection state probing.

For your convenience, there is an observer implementation that provides a simple interface for processing events:

```php
$observer = new \Phipe\Watcher();

$watcher->on('connect', function(\Phipe\Connection\Connection $connection) {
    echo 'Connected!' . PHP_EOL;
});

$watcher->on('disconnect', function(\Phipe\Connection\Connection $connection) {
    // etc
});

$phipe = new \Phipe\Application([
    'connections' => [
        ['host' => '127.0.0.1', 'port' => 80]
    ],
    'observers' => [
        $observer
    ]
]);

$phipe->execute();
```

Valid events are: `connect`, `disconnect`, `connect_fail`, `read`, `write`, `eof`. For an example of callback use,
please see the [`example/`](example/) directory.

### Advanced Usage

Should you wish to control the connection management, you will need to setup the various components yourself. Please
refer to the Application implementation for guidance.

The default connection class is the `Stream` flavour. If you wish to use the `Event` flavour, you will need to provide
a different factory to the `Application` class:

```php
$phipe = new \Phipe\Application([
    'connections' => [ /* connection details */ ],
    'factory' => new \Phipe\Connection\Event\EventFactory()
]);

$phipe->execute();
```

You can also inject different `Pool` and `Loop\Runner` implementations as part of the config array, is you so wish.

## Unit Tests

The unit tests for Phipe are built with PHPUnit. The tests are located within the [`test/unit/`](test/unit/) directory,
and configured by [`phpunit.xml`](phpunit.xml) in the project root.

PHPUnit is listed as a development dependency for this project; as such, you can simply run `./vendor/bin/phpunit` to
execute the tests.

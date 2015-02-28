<?php

namespace Phipe;

/**
 * @package Phipe
 */
class WatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Watcher
     */
    protected $watcher;

    protected function setUp()
    {
        $this->watcher = new Watcher();
    }

    /**
     * @return array
     */
    public function eventDataProvider()
    {
        return [
            [Connection::EVENT_CONNECT],
            [Connection::EVENT_CONNECT_FAIL],
            [Connection::EVENT_READ],
            [Connection::EVENT_WRITE],
            [Connection::EVENT_EOF],
            [Connection::EVENT_DISCONNECT]
        ];
    }

    /**
     * @dataProvider eventDataProvider
     */
    public function testOn($event)
    {
        $result = $this->watcher->on(
            $event,
            function () {
            }
        );
        $this->assertEquals($this->watcher, $result);
    }

    public function testOnWithInvalidEvent()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Event "mock" is unrecognised');

        $this->watcher->on(
            'mock',
            function () {
            }
        );
    }

    public function testUpdate()
    {
        $calls = 0;
        $event = Connection::EVENT_CONNECT;
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $this->watcher->on(
            $event,
            function (Connection $subject) use ($connection, &$calls) {
                $this->assertEquals($connection, $subject);
                $calls++;
            }
        );

        $this->watcher->update($connection, $event);
        $this->assertEquals(1, $calls);
    }

    public function testUpdateWithNonListeningEvent()
    {
        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $this->watcher->on(
            Connection::EVENT_CONNECT,
            function ($subject) {
                $this->fail('Not expecting callback to be executed');
            }
        );

        $this->watcher->update($connection, Connection::EVENT_DISCONNECT);
    }

    public function testUpdateWithNonConnection()
    {
        $subject = $this->getMock(\SplSubject::CLASS);

        $this->watcher->on(
            Connection::EVENT_CONNECT,
            function ($subject) {
                $this->fail('Not expecting callback to be executed');
            }
        );

        $this->watcher->update($subject, Connection::EVENT_CONNECT);
    }
}
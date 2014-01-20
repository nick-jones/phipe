<?php

namespace Phipe\Connection;

class NotificationPropagatorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var NotificationPropagator
     */
    protected $notificationPropagator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection
     */
    protected $connection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Connection\Connection
     */
    protected $proxied;

    protected function setUp() {
        $this->connection = $this->getMock('\Phipe\Connection\Connection');
        $this->proxied = $this->getMock('\Phipe\Connection\Connection');

        $this->notificationPropagator = new NotificationPropagator($this->connection, $this->proxied);
    }

    public function testInitialise() {
        $this->proxied
            ->expects($this->once())
            ->method('attach')
            ->with($this->equalTo($this->notificationPropagator));

        $this->notificationPropagator->initialise();
    }

    public function testUpdate() {
        $event = 'write';
        $data = 'mock';

        $this->connection
            ->expects($this->once())
            ->method('notify')
            ->with($this->equalTo($event), $this->equalTo($data));

        $this->notificationPropagator->update($this->proxied, $event, $data);
    }
}
<?php

namespace Phipe\Connection\Event;

require_once __DIR__ . '/../../../helper/Stub/EventBufferEvent.php';
require_once __DIR__ . '/../../../helper/Stub/EventBase.php';

class EventConnectionTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var EventConnection
     */
    protected $event;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Phipe\Stub\EventBufferEvent
     */
    protected $bufferEvent;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\SplObserver
     */
    protected $observer;

    protected function setUp() {
        $eventBase = $this->getMock('\Phipe\Stub\EventBase');
        $this->bufferEvent = $this->getMock('\Phipe\Stub\EventBufferEvent', array(), array($eventBase));
        $this->observer = $this->getMock('\SplObserver');

        $this->event = new EventConnection('127.0.0.1', 80);
        $this->event->setBufferEvent($this->bufferEvent);
        $this->event->attach($this->observer);
    }

    public function testConnect() {
        $this->bufferEvent->expects($this->once())
            ->method('setCallbacks');

        $this->bufferEvent->expects($this->once())
            ->method('enable')
            ->with(\Event::READ | \Event::WRITE);

        $this->bufferEvent->expects($this->once())
            ->method('connect');

        $this->event->connect();
    }

    public function testDisconnect() {
        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->event), $this->equalTo(\Phipe\Connection\Connection::EVENT_DISCONNECT));

        $this->bufferEvent->expects($this->once())
            ->method('free');

        $this->event->disconnect();
    }

    public function testRead() {
        $expected = 'foo';

        $input = $this->getMock('\EventBuffer');

        $input->expects($this->once())
            ->method('read')
            ->will($this->returnValue($expected));

        $this->bufferEvent->expects($this->any())
            ->method('getInput')
            ->will($this->returnValue($input));

        $result = $this->event->read();
        $this->assertEquals($expected, $result);
    }

    public function testWrite() {
        $data = 'foo';

        $output = $this->getMock('\EventBuffer');

        $output->expects($this->once())
            ->method('add')
            ->will($this->returnValue($data));

        $this->bufferEvent->expects($this->any())
            ->method('getOutput')
            ->will($this->returnValue($output));

        $this->event->write($data);
    }

    public function testGetState() {
        $this->assertEquals(0, $this->event->getState());
    }

    public function testEventRead() {
        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->event), $this->equalTo(\Phipe\Connection\Connection::EVENT_READ));

        $this->event->eventRead(NULL);
    }

    public function testEventStatus_Connected() {
        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->event), $this->equalTo(\Phipe\Connection\Connection::EVENT_CONNECT));

        $this->event->eventStatus(NULL, \EventBufferEvent::CONNECTED);
    }

    public function testEventStatus_EOF() {
        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->event), $this->equalTo(\Phipe\Connection\Connection::EVENT_EOF));

        $this->event->eventStatus(NULL, \EventBufferEvent::EOF);
    }

    public function testEventStatus_Error() {
        $this->setExpectedException('\Phipe\Connection\ConnectionException', 'EventBufferEvent received error status');

        $this->observer->expects($this->once())
            ->method('update')
            ->with($this->equalTo($this->event), $this->equalTo(\Phipe\Connection\Connection::EVENT_CONNECT_FAIL));

        $this->event->eventStatus(NULL, \EventBufferEvent::ERROR);
    }

    public function testCreateBufferEvent() {
        $this->event->setEventBase(new \EventBase());
        $bufferEvent = $this->event->createBufferEvent();

        $this->assertInstanceOf('\EventBufferEvent', $bufferEvent);
    }

    public function testCreateSslBufferEvent() {
        $this->event->setEventBase(new \EventBase());
        $bufferEvent = $this->event->createSslBufferEvent();

        $this->assertInstanceOf('\EventBufferEvent', $bufferEvent);
    }
}
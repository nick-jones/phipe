<?php

namespace Phipe\Connection\Stream;

class SelectorTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Selector
     */
    protected $selector;

    protected function setUp() {;
        $this->selector = new Selector();
    }

    public function testSelect() {
        $expectedStreams = array(fopen('php://memory', 'r'));
        $expectedTimeout = 500;

        $strategy = function($streams, $timeout) use($expectedStreams, $expectedTimeout) {
            $this->assertEquals($expectedStreams, $streams);
            $this->assertEquals($expectedTimeout, $timeout);
        };

        $this->selector->setSelectStrategy($strategy);
        $this->selector->select($expectedStreams, $expectedTimeout);
    }

    public function testSleepingStreamSelect_NoStreams() {
        $streams = array();

        $this->selector->setSelectStrategy(array('Phipe\Connection\Stream\Selector', 'sleepingStreamSelect'));
        $results = $this->selector->select($streams, 1);

        $this->assertEquals($streams, $results);
    }

    public function testSleepingStreamSelect_SingleStream() {
        $address = 'tcp://127.0.0.1:83751';

        $server = stream_socket_server($address);
        $client = stream_socket_client($address);

        $streams = array($client);

        $this->selector->setSelectStrategy(array('Phipe\Connection\Stream\Selector', 'sleepingStreamSelect'));
        $results = $this->selector->select($streams, 1);

        $this->assertEquals(array(), $results);

        fclose($client);
        fclose($server);
    }
}
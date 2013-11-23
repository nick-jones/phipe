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

	public function testSelect_Failure() {
		$this->setExpectedException(
			'Phipe\Connection\Stream\SelectFailureException',
			'Select strategy indicated failure'
		);

		$strategy = function() {
			return array(FALSE, array());
		};

		$this->selector->setSelectStrategy($strategy);
		$this->selector->select(array());
	}

	public function testSelectOrSleep_NoStreams() {
		$streams = array();
		$results = $this->selector->selectOrSleep($streams, 1);

		$this->assertEquals(array(0, $streams), $results);
	}

	public function testSelectOrSleep_SingleStream() {
		$address = 'tcp://127.0.0.1:83751';

		$server = stream_socket_server($address);
		$client = stream_socket_client($address);

		$streams = array($client);
		$results = $this->selector->selectOrSleep($streams, 1);

		$this->assertEquals(array(0, array()), $results);

		fclose($client);
		fclose($server);
	}
}
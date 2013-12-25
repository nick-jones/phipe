<?php

namespace Phipe;

/**
 * @package Phipe
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var Application
	 */
	protected $application;

	protected function setUp() {
		$this->application = new Application();
	}

	public function testExecute() {
		$observer = $this->getMock('\SplObserver');
		$prober = $this->getMock('\Phipe\Connection\Prober');

		$connection = $this->getMock('\Phipe\Connection\Connection', array(), array('127.0.0.1', 80));

		$connection->expects($this->once())
			->method('attach')
			->with($this->equalTo($observer));

		$factory = $this->getMock('\Phipe\Connection\Factory');

		$factory->expects($this->any())
			->method('createProber')
			->will($this->returnValue($prober));

		$factory->expects($this->any())
			->method('createConnection')
			->will($this->returnValue($connection));

		$pool = $this->getMock('\Phipe\Pool');

		$pool->expects($this->once())
			->method('add')
			->with($this->equalTo($connection));

		$runner = $this->getMock('\Phipe\Loop\Runner');

		$runner->expects($this->once())
			->method('loop')
			->with($this->isInstanceOf('\Phipe\Session'));

		$this->application->setConfig(array(
			'connections' => array(array(
				'host' => '127.0.0.1',
				'port' => 80
			)),
			'observers' => array($observer),
			'strategies' => array(),
			'factory' => $factory,
			'pool' => $pool,
			'loop_runner' => $runner
		));

		$this->application->execute();
	}
}
<?php

namespace Phipe;

use Phipe\Connection\Factory;
use Phipe\Connection\Prober;
use Phipe\Loop\Runner;

/**
 * @package Phipe
 */
class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Executor
     */
    protected $application;

    protected function setUp()
    {
        $this->application = new Executor();
    }

    public function testExecute()
    {
        $observer = $this->getMock(\SplObserver::CLASS);
        $prober = $this->getMock(Prober::CLASS);

        $connection = $this->getMock(Connection::CLASS, [], ['127.0.0.1', 80]);

        $connection->expects($this->once())
            ->method('attach')
            ->with($this->equalTo($observer));

        $factory = $this->getMock(Factory::CLASS);

        $factory->expects($this->any())
            ->method('createProber')
            ->will($this->returnValue($prober));

        $factory->expects($this->any())
            ->method('createConnection')
            ->will($this->returnValue($connection));

        $pool = $this->getMock(Pool::CLASS);

        $pool->expects($this->once())
            ->method('add')
            ->with($this->equalTo($connection));

        $runner = $this->getMock(Runner::CLASS);

        $runner->expects($this->once())
            ->method('loop')
            ->with($this->isInstanceOf(Session::CLASS));

        $this->application->setConfig([
            'connections' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 80
                ]
            ],
            'observers' => [$observer],
            'strategies' => [],
            'factory' => $factory,
            'pool' => $pool,
            'loop_runner' => $runner
        ]);

        $this->application->execute();
    }
}
<?php

namespace Phipe\Loop;

class RunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Runner
     */
    protected $runner;

    protected function setUp()
    {
        $this->runner = new Runner();
    }

    public function testLoop()
    {
        $worker = $this->getMock(Worker::CLASS);

        $worker->expects($this->exactly(2))
            ->method('hasWork')
            ->will($this->onConsecutiveCalls(true, false));

        $worker->expects($this->once())
            ->method('work');

        $this->runner->loop($worker);
    }

    public function testStop()
    {
        $worker = $this->getMock(Worker::CLASS);

        $worker->expects($this->once())
            ->method('hasWork')
            ->will($this->returnValue(true));

        $worker->expects($this->once())
            ->method('work')
            ->will(
                $this->returnCallback(
                    function () {
                        $this->runner->stop();
                    }
                )
            );

        $this->runner->loop($worker);
    }

    public function testIsRunning()
    {
        $worker = $this->getMock(Worker::CLASS);

        $worker->expects($this->exactly(2))
            ->method('hasWork')
            ->will($this->onConsecutiveCalls(true, false));

        $worker->expects($this->once())
            ->method('work')
            ->will(
                $this->returnCallback(
                    function () {
                        $this->assertTrue($this->runner->isRunning());
                    }
                )
            );

        $this->assertFalse($this->runner->isRunning());
        $this->runner->loop($worker);
        $this->assertFalse($this->runner->isRunning());
    }
}
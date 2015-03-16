<?php

namespace spec\GrumPHP\Runner;

use GrumPHP\Task\TaskInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TaskRunnerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('GrumPHP\Runner\TaskRunner');
    }

    function it_holds_tasks(TaskInterface $task1, TaskInterface $task2)
    {
        $this->addTask($task1);
        $this->addTask($task2);

        $this->getTasks()->shouldEqual(array($task1, $task2));
    }

    function it_does_not_add_the_same_task_twice(TaskInterface $task1, TaskInterface $task2)
    {
        $this->addTask($task1);
        $this->addTask($task1);
        $this->addTask($task2);

        $this->getTasks()->shouldEqual(array($task1, $task2));
    }

    function it_runs_all_tasks(TaskInterface $task1, TaskInterface $task2)
    {
        $this->addTask($task1);
        $this->addTask($task2);

        $task1->run(array('file1'))->shouldBeCalled();
        $task2->run(array('file1'))->shouldBeCalled();

        $this->run(array('file1'));
    }

    function it_throws_exception_if_task_fails(TaskInterface $task1)
    {
        $this->addTask($task1);

        $task1->run(array('file1'))->willThrow('GrumPHP\Exception\RuntimeException');

        $this->shouldThrow('GrumPHP\Exception\FailureException')->duringRun(array('file1'));
    }

    function it_runs_subsequent_tasks_if_one_fails(TaskInterface $task1, TaskInterface $task2)
    {
        $this->addTask($task1);
        $this->addTask($task2);

        $task1->run(array('file1'))->willThrow('GrumPHP\Exception\RuntimeException');
        $task2->run(array('file1'))->shouldBeCalled();

        $this->shouldThrow('GrumPHP\Exception\FailureException')->duringRun(array('file1'));
    }
}
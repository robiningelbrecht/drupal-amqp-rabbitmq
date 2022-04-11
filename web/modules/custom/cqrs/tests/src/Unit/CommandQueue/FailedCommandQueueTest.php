<?php

namespace Drupal\Tests\cqrs\Unit\CommandQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\cqrs\CommandQueue\FailedCommandQueue;
use Drupal\cqrs\CommandQueueWorker;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FailedCommandQueueTest extends UnitTestCase
{
  private FailedCommandQueue $failedCommandQueue;
  private TestQueue $testQueue;
  private MockObject $AMQPChannelFactory;

  public function testGetters(): void{
    $this->assertEquals('test-queue-failed', $this->failedCommandQueue->getName());

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Failed command queues do not have workers');

    $this->failedCommandQueue->getWorker();
  }

  protected function setUp()
  {
    parent::setUp();

    $this->testQueue = new TestQueue();
    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);

    $this->failedCommandQueue =  new FailedCommandQueue(
      $this->testQueue,
      $this->AMQPChannelFactory,
    );
  }

}

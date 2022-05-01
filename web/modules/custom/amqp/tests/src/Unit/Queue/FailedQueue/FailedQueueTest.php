<?php

namespace Drupal\Tests\amqp\Unit\Queue\FailedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\FailedQueue\FailedQueue;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FailedQueueTest extends UnitTestCase
{
  private FailedQueue $failedQueue;
  private TestQueue $testQueue;
  private MockObject $AMQPChannelFactory;

  public function testGetters(): void{
    $this->assertEquals('test-queue-failed', $this->failedQueue->getName());

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Failed queues do not have workers');

    $this->failedQueue->getWorker();
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->testQueue = new TestQueue();
    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);

    $this->failedQueue =  new FailedQueue(
      $this->testQueue,
      $this->AMQPChannelFactory,
    );
  }

}

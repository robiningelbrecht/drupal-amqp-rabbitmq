<?php

namespace Drupal\Tests\amqp\Unit\Queue\FailedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\FailedQueue\FailedQueue;
use Drupal\amqp\Queue\FailedQueue\FailedQueueFactory;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FailedQueueFactoryTest extends UnitTestCase
{
  private FailedQueueFactory $failedQueueFactory;
  private MockObject $AMQPChannelFactory;

  public function testBuildFor(): void
  {
    $queue = new TestQueue();
    $expectedFailedQueue = new FailedQueue($queue, $this->AMQPChannelFactory);

    $this->assertEquals(
      $expectedFailedQueue,
      $this->failedQueueFactory->buildFor($queue)
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);

    $this->failedQueueFactory = new FailedQueueFactory(
      $this->AMQPChannelFactory
    );
  }
}

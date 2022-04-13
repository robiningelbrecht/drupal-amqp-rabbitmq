<?php

namespace Drupal\Tests\amqp\Unit\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueue;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueueFactory;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DelayedQueueFactoryTest extends UnitTestCase
{
  private DelayedQueueFactory $delayedQueueFactory;
  private MockObject $AMQPChannelFactory;

  public function testBuildWithDelayForQueue(): void
  {
    $queue = new TestDelayedQueue();
    $delayedQueue = new DelayedQueue(
      $queue,
      10,
      $this->AMQPChannelFactory
    );

    $this->assertEquals(
      $delayedQueue,
      $this->delayedQueueFactory->buildWithDelayForQueue(10, $queue)
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);
    $this->delayedQueueFactory = new DelayedQueueFactory(
      $this->AMQPChannelFactory
    );
  }
}

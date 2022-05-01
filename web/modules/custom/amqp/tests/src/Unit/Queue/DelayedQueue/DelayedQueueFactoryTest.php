<?php

namespace Drupal\Tests\amqp\Unit\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPClient;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueue;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueueFactory;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DelayedQueueFactoryTest extends UnitTestCase
{
  private DelayedQueueFactory $delayedQueueFactory;
  private MockObject $AMQPChannelFactory;
  private MockObject $AMQPClient;

  public function testBuildWithDelayForQueue(): void
  {

    $this->AMQPClient
      ->expects($this->once())
      ->method('getExchangeBindings')
      ->with('dlx')
      ->willReturn([
        [
          'destination' => 'test-queue',
          'routing_key' => 'test-queue',
          'destination_type' => 'queue',
        ],
      ]);

    $this->assertInstanceOf(
      DelayedQueue::class,
      $this->delayedQueueFactory->buildWithDelayForQueue(10, new TestQueue())
    );
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);
    $this->AMQPClient = $this->createMock(AMQPClient::class);

    $this->delayedQueueFactory = new DelayedQueueFactory(
      $this->AMQPChannelFactory,
      $this->AMQPClient,
    );
  }
}

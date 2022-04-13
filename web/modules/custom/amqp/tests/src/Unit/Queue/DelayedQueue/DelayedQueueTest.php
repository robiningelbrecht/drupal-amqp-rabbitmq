<?php

namespace Drupal\Tests\amqp\Unit\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPChannelOptions;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueue;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DelayedQueueTest extends UnitTestCase
{
  private MockObject $AMQPChannelFactory;

  public function testGetName(): void
  {
    $delayedQueue = new DelayedQueue(
      new TestDelayedQueue(),
      10,
      $this->AMQPChannelFactory
    );

    $this->assertEquals('delayed-10s-test-queue', $delayedQueue->getName());
  }

  public function testQueue(): void
  {
    $delayedQueue = new DelayedQueue(
      new TestDelayedQueue(),
      10,
      $this->AMQPChannelFactory
    );

    $options = new AMQPChannelOptions(false, true, false, false, false, [
      'x-dead-letter-exchange' => ['S', 'dlx'],
      'x-dead-letter-routing-key' => ['S', 'test-queue'],
      'x-message-ttl' => ['I', 10000],
      'x-expires' => ['I', 10000 + 100000], // Keep the Q for 100s after the last message,
    ]);

    $this->AMQPChannelFactory
      ->expects($this->once())
      ->method('getForQueue')
      ->with($delayedQueue, $options);

    $delayedQueue->queue(AMQPEnvelope::fromContentAndDate(
      'content',
      new \DateTimeImmutable('2022-04-13 24:09:44')
    ));
  }

  public function testGetWorker(): void
  {
    $delayedQueue = new DelayedQueue(
      new TestDelayedQueue(),
      10,
      $this->AMQPChannelFactory
    );

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Delayed queues do not have workers');

    $delayedQueue->getWorker();
  }

  public function testItShouldThrowOnInvalidQueue(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Queue "test-queue" does not support delayed queueing');

    new DelayedQueue(
      new TestQueue(),
      10,
      $this->AMQPChannelFactory
    );
  }

  public function testItShouldThrowWhenInvalidSeconds(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Delay cannot be less than 1 second');

    new DelayedQueue(
      new TestDelayedQueue(),
      0,
      $this->AMQPChannelFactory
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);
  }
}

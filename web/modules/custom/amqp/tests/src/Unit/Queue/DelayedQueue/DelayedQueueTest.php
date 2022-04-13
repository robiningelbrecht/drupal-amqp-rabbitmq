<?php

namespace Drupal\Tests\amqp\Unit\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPChannelOptions;
use Drupal\amqp\AMQPClient;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueue;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DelayedQueueTest extends UnitTestCase
{
  private MockObject $AMQPChannelFactory;
  private MockObject $AMQPClient;

  public function testGetName(): void
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

    $delayedQueue = new DelayedQueue(
      new TestQueue(),
      10,
      $this->AMQPChannelFactory,
      $this->AMQPClient
    );

    $this->assertEquals('delayed-10s-test-queue', $delayedQueue->getName());
  }

  public function testQueue(): void
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

    $delayedQueue = new DelayedQueue(
      new TestQueue(),
      10,
      $this->AMQPChannelFactory,
      $this->AMQPClient
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

    $delayedQueue = new DelayedQueue(
      new TestQueue(),
      10,
      $this->AMQPChannelFactory,
      $this->AMQPClient
    );

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Delayed queues do not have workers');

    $delayedQueue->getWorker();
  }

  public function testItShouldThrowOnInvalidQueue(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Queue "test-queue" does not support delayed queueing. Make sure the exchange "dlx" has a binding with a routing key and a destination "test-queue"');

    $this->AMQPClient
      ->expects($this->once())
      ->method('getExchangeBindings')
      ->with('dlx')
      ->willReturn([
        [
          'destination' => 'test-delayed-queue',
          'routing_key' => 'test-delayed-queue',
          'destination_type' => 'queue',
        ],
      ]);

    new DelayedQueue(
      new TestQueue(),
      10,
      $this->AMQPChannelFactory,
      $this->AMQPClient
    );
  }

  public function testItShouldThrowWhenInvalidSeconds(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('Delay cannot be less than 1 second');

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

    new DelayedQueue(
      new TestQueue(),
      0,
      $this->AMQPChannelFactory,
      $this->AMQPClient
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);
    $this->AMQPClient = $this->createMock(AMQPClient::class);
  }
}

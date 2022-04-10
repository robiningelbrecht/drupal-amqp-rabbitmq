<?php

namespace Drupal\Tests\amqp\Unit\DrushCommand;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Clock\PausedClock;
use Drupal\amqp\Consumer;
use Drupal\amqp\DrushCommand\AmqpCommands;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AmqpCommandsTest extends UnitTestCase
{
  private AmqpCommands $amqpCommands;
  private MockObject $consumer;
  private MockObject $queueFactory;
  private Clock $clock;

  public function testConsume(): void
  {
    $queueName = 'queue-name';
    $queue = new TestQueue();

    $this->queueFactory
      ->expects($this->once())
      ->method('getQueue')
      ->with($queueName)
      ->willReturn($queue);

    $this->consumer
      ->expects($this->once())
      ->method('consume')
      ->with($queue);

    $this->amqpCommands->consume($queueName);
  }

  public function testSimpleQueueTest(): void
  {
    $queue = $this->createMock(Queue::class);

    $this->queueFactory
      ->expects($this->once())
      ->method('getQueue')
      ->with('simple-queue')
      ->willReturn($queue);

    $queue
      ->expects($this->once())
      ->method('queue')
      ->with(AMQPEnvelope::fromContentAndDate('test one', $this->clock->getCurrentDateTimeImmutable()));

    $queue
      ->expects($this->once())
      ->method('queueBatch')
      ->with([
        AMQPEnvelope::fromContentAndDate('test batch one', $this->clock->getCurrentDateTimeImmutable()),
        AMQPEnvelope::fromContentAndDate('test batch two', $this->clock->getCurrentDateTimeImmutable()),
      ]);

    $this->amqpCommands->simpleQueueTest();
  }

  protected function setUp()
  {
    parent::setUp();

    $this->consumer = $this->createMock(Consumer::class);
    $this->queueFactory = $this->createMock(QueueFactory::class);
    $this->clock = PausedClock::on(new \DateTimeImmutable('2022-04-10 20:10:04'));

    $this->amqpCommands = new AmqpCommands(
      $this->consumer,
      $this->queueFactory,
      $this->clock
    );
  }
}

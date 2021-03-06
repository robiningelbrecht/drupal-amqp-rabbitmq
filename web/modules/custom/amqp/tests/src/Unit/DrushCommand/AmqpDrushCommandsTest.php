<?php

namespace Drupal\Tests\amqp\Unit\DrushCommand;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Clock\PausedClock;
use Drupal\amqp\Consumer;
use Drupal\amqp\DrushCommand\AmqpDrushCommands;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\amqp\SupervisordConfig;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AmqpDrushCommandsTest extends UnitTestCase
{
  private AmqpDrushCommands $amqpDrushCommands;
  private MockObject $consumer;
  private MockObject $queueFactory;
  private MockObject $supervisordConfig;
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

    $this->amqpDrushCommands->consume($queueName);
  }

  public function testBuildSupervisordConfig(): void
  {
    $this->supervisordConfig
      ->expects($this->once())
      ->method('writeAll');

    $this->amqpDrushCommands->buildSupervisordConfig();
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

    $this->amqpDrushCommands->simpleQueueTest();
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->consumer = $this->createMock(Consumer::class);
    $this->queueFactory = $this->createMock(QueueFactory::class);
    $this->supervisordConfig = $this->createMock(SupervisordConfig::class);
    $this->clock = PausedClock::on(new \DateTimeImmutable('2022-04-10 20:10:04'));

    $this->amqpDrushCommands = new AmqpDrushCommands(
      $this->consumer,
      $this->queueFactory,
      $this->supervisordConfig,
      $this->clock
    );
  }
}

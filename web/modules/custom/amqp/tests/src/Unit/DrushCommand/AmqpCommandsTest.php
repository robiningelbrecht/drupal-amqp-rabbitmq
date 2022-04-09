<?php

namespace Drupal\Tests\amqp\Unit\DrushCommand;

use Drupal\amqp\Consumer;
use Drupal\amqp\DrushCommand\AmqpCommands;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AmqpCommandsTest extends UnitTestCase
{
  private AmqpCommands $amqpCommands;
  private MockObject $consumer;
  private MockObject $queueFactory;

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
      ->with(AMQPEnvelope::fromContentAndDate('test one', new DateTimePlus('2022-04-09')));

    $queue
      ->expects($this->once())
      ->method('queueBatch')
      ->with([
        AMQPEnvelope::fromContentAndDate('test batch one', new DateTimePlus('2022-04-09')),
        AMQPEnvelope::fromContentAndDate('test batch two', new DateTimePlus('2022-04-09')),
      ]);

    $this->amqpCommands->simpleQueueTest('2022-04-09');
  }

  protected function setUp()
  {
    parent::setUp();

    $this->consumer = $this->createMock(Consumer::class);
    $this->queueFactory = $this->createMock(QueueFactory::class);

    $this->amqpCommands = new AmqpCommands(
      $this->consumer,
      $this->queueFactory,
    );
  }
}

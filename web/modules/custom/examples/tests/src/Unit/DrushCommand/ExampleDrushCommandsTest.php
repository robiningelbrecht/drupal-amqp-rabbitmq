<?php

namespace Drupal\Tests\examples\Unit\DrushCommand;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Clock\PausedClock;
use Drupal\amqp\Consumer;
use Drupal\amqp\DrushCommand\AmqpDrushCommands;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\examples\AddDatabaseLog\AddDatabaseLog;
use Drupal\examples\DrushCommand\ExampleDrushCommands;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ExampleDrushCommandsTest extends UnitTestCase
{
  private ExampleDrushCommands $exampleDrushCommands;
  private MockObject $queueFactory;
  private Clock $clock;

  public function testGeneralCommandQueueTest(): void
  {
    $queue = $this->createMock(Queue::class);

    $this->queueFactory
      ->expects($this->once())
      ->method('getQueue')
      ->with('general-command-queue')
      ->willReturn($queue);

    $queue
      ->expects($this->once())
      ->method('queue')
      ->with(
        new AddDatabaseLog(
          'This message originated from a queued command',
          $this->clock->getCurrentDateTimeImmutable()
        )
      );

    $this->exampleDrushCommands->generalCommandQueueTest();
  }

  protected function setUp()
  {
    parent::setUp();

    $this->queueFactory = $this->createMock(QueueFactory::class);
    $this->clock = PausedClock::on(new \DateTimeImmutable('2022-04-10 20:10:04'));

    $this->exampleDrushCommands = new ExampleDrushCommands(
      $this->queueFactory,
      $this->clock
    );
  }
}

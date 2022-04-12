<?php

namespace Drupal\Tests\cqrs\Unit;

use Drupal\amqp\Clock\PausedClock;
use Drupal\amqp\ConsoleLogger;
use Drupal\amqp\Queue\FailedQueue\FailedQueue;
use Drupal\amqp\Queue\FailedQueue\FailedQueueFactory;
use Drupal\cqrs\CommandDispatcher;
use Drupal\cqrs\CommandQueueWorker;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;

class CommandQueueWorkerTest extends UnitTestCase
{

  private CommandQueueWorker $commandQueueWorker;
  private MockObject $commandDispatcher;
  private MockObject $failedQueueFactory;
  private MockObject $logger;

  public function testProcessMessage(): void
  {
    $command = new TestCommand(new \DateTimeImmutable('2022-04-11 08:09:22'));
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($command), $properties);

    $this->commandDispatcher
      ->expects($this->once())
      ->method('dispatch')
      ->with($command);

    $this->logger
      ->expects($this->once())
      ->method('success')
      ->with('Worker "Command worker" dispatched command "TestCommand"');

    $this->commandQueueWorker->processMessage($command, $message);
  }

  public function testProcessFailure(): void
  {
    $queue = new TestQueue();
    $command = new TestCommand(new \DateTimeImmutable('2022-04-11 08:09:22'));
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($command), $properties);

    $failedQueue = $this->createMock(FailedQueue::class);
    $this->failedQueueFactory
      ->expects($this->once())
      ->method('buildFor')
      ->with($queue)
      ->willReturn($failedQueue);

    $this->logger
      ->expects($this->once())
      ->method('warning')
      ->with('Message has been send to failed queue ""');

    $failedQueue
      ->expects($this->once())
      ->method('queue');

    $this->commandQueueWorker->processFailure($command, $message, new \RuntimeException('FAIL'), $queue);
  }

  protected function setUp()
  {
    parent::setUp();

    $this->commandDispatcher = $this->createMock(CommandDispatcher::class);
    $this->failedQueueFactory = $this->createMock(FailedQueueFactory::class);
    $this->logger = $this->createMock(ConsoleLogger::class);

    $this->commandQueueWorker = new CommandQueueWorker(
      $this->commandDispatcher,
      $this->failedQueueFactory,
      $this->logger,
      PausedClock::on(new \DateTimeImmutable('2022-04-11 08:09:22')),
    );
  }
}

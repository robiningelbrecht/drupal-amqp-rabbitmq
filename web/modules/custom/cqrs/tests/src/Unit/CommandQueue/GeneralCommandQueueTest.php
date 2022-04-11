<?php

namespace Drupal\Tests\cqrs\Unit\CommandQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\cqrs\CommandQueue\GeneralCommandQueue;
use Drupal\cqrs\CommandQueueWorker;
use Drupal\Tests\cqrs\Unit\TestCommand;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GeneralCommandQueueTest extends UnitTestCase
{
  private GeneralCommandQueue $generalCommandQueue;
  private MockObject $commandQueueWorker;
  private MockObject $AMQPChannelFactory;

  public function testGetters(): void
  {
    $this->assertEquals('general-command-queue', $this->generalCommandQueue->getName());
    $this->assertEquals($this->commandQueueWorker, $this->generalCommandQueue->getWorker());
  }

  public function testQueue(): void
  {
    $command = new TestCommand(new \DateTimeImmutable('2022-04-11 08:09:22'));

    $this->AMQPChannelFactory
      ->expects($this->once())
      ->method('getForQueue')
      ->with($this->generalCommandQueue);

    $this->generalCommandQueue->queue($command);
  }

  public function testQueueItShouldThrowWhenInvalidEnvelope(): void
  {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Queue "general-command-queue" requires a command to be queued, Drupal\amqp\Envelope\AMQPEnvelope given');

    $this->generalCommandQueue->queue(
      AMQPEnvelope::fromContentAndDate('content', new \DateTimeImmutable('2022-04-11 08:09:22'))
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->commandQueueWorker = $this->createMock(CommandQueueWorker::class);
    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);

    $this->generalCommandQueue = new GeneralCommandQueue(
      $this->commandQueueWorker,
      $this->AMQPChannelFactory,
    );
  }
}

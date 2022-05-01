<?php

namespace Drupal\Tests\amqp\Unit\Queue;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Clock\PausedClock;
use Drupal\amqp\ConsoleLogger;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Worker\SimpleQueueWorker;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;

class SimpleQueueWorkerTest extends UnitTestCase
{
  private SimpleQueueWorker $simpleQueueWorker;
  private MockObject $logger;
  private Clock $clock;

  public function testMaxIterations(): void
  {
    $this->assertEquals('Simple queue worker', $this->simpleQueueWorker->getName());
    $this->assertEquals(1000, $this->simpleQueueWorker->getMaxIterations());

    $this->assertFalse($this->simpleQueueWorker->maxIterationsReached());
    for ($i = 0; $i < $this->simpleQueueWorker->getMaxIterations(); $i++) {
      $this->simpleQueueWorker->maxIterationsReached();
    }

    $this->assertTrue($this->simpleQueueWorker->maxIterationsReached());
  }

  public function testMaxLifeTime(): void
  {
    $this->assertEquals(
      '2022-04-10 21:10:04',
      $this->simpleQueueWorker->getMaxLifeTime()->format('Y-m-d H:i:s')
    );

    $this->assertEquals(
      new \DateInterval('PT1H'),
      $this->simpleQueueWorker->getMaxLifeTimeInterval()
    );

    $this->assertFalse($this->simpleQueueWorker->maxLifeTimeReached());
  }

  public function testProcessMessage(): void
  {
    $envelope = AMQPEnvelope::fromContentAndDate('some-content', new \DateTimeImmutable('2022-02-10 14:20:01'));
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($envelope), $properties);

    $this->logger
      ->expects($this->once())
      ->method('success')
      ->with('Processed message with content "some-content", queued on 14:20:01');

    $this->simpleQueueWorker->processMessage($envelope, $message);
  }

  public function testProcessFailure(): void
  {
    $envelope = AMQPEnvelope::fromContentAndDate('some-content', new \DateTimeImmutable('2022-02-10 14:20:01'));
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($envelope), $properties);
    $queue = new TestQueue();
    $exception = new \RuntimeException('Some fancy exception');

    $this->logger
      ->expects($this->exactly(2))
      ->method('error')
      ->withConsecutive(
        ['Could not processes message with content "some-content", queued on 14:20:01'],
        ['Exception: Some fancy exception'],
      );

    $this->simpleQueueWorker->processFailure($envelope, $message, $exception, $queue);
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->logger = $this->createMock(ConsoleLogger::class);
    $this->clock = PausedClock::on(new \DateTimeImmutable('2022-04-10 20:10:04'));

    $this->simpleQueueWorker = new SimpleQueueWorker(
      $this->logger,
      $this->clock
    );
  }
}

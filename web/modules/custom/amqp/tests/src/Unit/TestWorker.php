<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;
use PhpAmqpLib\Message\AMQPMessage;

class TestWorker implements Worker
{
  private const MAX_LIFE_TIME_INTERVAL = 'PT1H';

  private int $counter = 0;

  public function __construct(
    private \DateTimeImmutable $maxLifeTimeDateTime
  )
  {
  }

  public function getName(): string
  {
    return 'test-worker';
  }

  public function processMessage(Envelope $envelope, AMQPMessage $message): void
  {
    // TODO: Implement processMessage() method.
  }

  public function processFailure(Envelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void
  {
    // TODO: Implement processFailure() method.
  }

  public function maxIterationsReached(): bool
  {
    return $this->counter++ >= $this->getMaxIterations();
  }

  public function maxLifeTimeReached(): bool
  {
    return new \DateTimeImmutable('now') >= $this->maxLifeTimeDateTime;
  }

  public function getMaxIterations(): int
  {
    return 2;
  }

  public function getMaxLifeTime(): \DateTimeImmutable
  {
    return $this->maxLifeTimeDateTime;
  }

  public function getMaxLifeTimeInterval(): \DateInterval
  {
    return new \DateInterval(self::MAX_LIFE_TIME_INTERVAL);
  }
}

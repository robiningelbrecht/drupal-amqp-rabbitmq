<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Datetime\DrupalDateTime;
use PhpAmqpLib\Message\AMQPMessage;

class TestWorker implements Worker
{
  private const MAX_LIFE_TIME_INTERVAL = 'PT1H';
  private const MAX_ITERATIONS = 100000;

  private int $counter = 0;

  public function __construct(
    private DrupalDateTime $maxLifeTimeDateTime
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
    return new DateTimePlus('now') >= $this->maxLifeTimeDateTime;
  }

  public function getMaxIterations(): int
  {
    return self::MAX_ITERATIONS;
  }

  public function getMaxLifeTime(): DrupalDateTime
  {
    return $this->maxLifeTimeDateTime;
  }

  public function getMaxLifeTimeInterval(): \DateInterval
  {
    return new \DateInterval(self::MAX_LIFE_TIME_INTERVAL);
  }
}

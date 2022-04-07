<?php

namespace Drupal\amqp;

use Drupal\Core\Datetime\DrupalDateTime;
use PhpAmqpLib\Message\AMQPMessage;

class Worker
{

  private const MAX_LIFE_TIME_INTERVAL = 'PT1H';
  private const MAX_ITERATIONS = 100000;

  private int $counter = 0;
  private DrupalDateTime $maxLifeTimeDateTime;

  public function __construct()
  {
    $this->maxLifeTimeDateTime = (new DrupalDateTime('now'))->add(new \DateInterval(self::MAX_LIFE_TIME_INTERVAL));
  }

  public function processMessage(AMQPMessage $message): void
  {

  }

  public function processFailure(AMQPMessage $message, \Throwable $exception): void
  {

  }

  public function maxIterationsReached(): bool
  {
    return $this->counter++ >= self::MAX_ITERATIONS;
  }

  public function maxLifeTimeReached(): bool
  {
    return new DrupalDateTime('now') >= $this->maxLifeTimeDateTime;
  }

  public function getMaxIterations(): int
  {
    return self::MAX_ITERATIONS;
  }

  public function getMaxLifeTime(): DrupalDateTime
  {
    return $this->maxLifeTimeDateTime;
  }
}

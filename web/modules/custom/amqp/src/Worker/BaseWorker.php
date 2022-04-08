<?php

namespace Drupal\amqp\Worker;

use Drupal\Core\Datetime\DrupalDateTime;

abstract class BaseWorker implements Worker
{
  private const MAX_LIFE_TIME_INTERVAL = 'PT1H';
  private const MAX_ITERATIONS = 100000;

  private int $counter = 0;
  private DrupalDateTime $maxLifeTimeDateTime;

  public function __construct()
  {
    $this->maxLifeTimeDateTime = (new DrupalDateTime('now'))->add($this->getMaxLifeTimeInterval());
  }

  public function maxIterationsReached(): bool
  {
    return $this->counter++ >= $this->getMaxIterations();
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

  public function getMaxLifeTimeInterval(): \DateInterval
  {
    return new \DateInterval(self::MAX_LIFE_TIME_INTERVAL);
  }
}

<?php

namespace Drupal\amqp\Worker;

use Drupal\Core\Datetime\DrupalDateTime;
use PhpAmqpLib\Message\AMQPMessage;

interface Worker
{
  public function getName(): string;

  public function processMessage(AMQPMessage $message): void;

  public function processFailure(AMQPMessage $message, \Throwable $exception): void;

  public function maxIterationsReached(): bool;

  public function maxLifeTimeReached(): bool;

  public function getMaxIterations(): int;

  public function getMaxLifeTime(): DrupalDateTime;
}

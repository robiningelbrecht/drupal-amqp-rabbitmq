<?php

namespace Drupal\amqp\Worker;

use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\Queue\Queue;
use Drupal\Core\Datetime\DrupalDateTime;
use PhpAmqpLib\Message\AMQPMessage;

interface Worker
{
  public function getName(): string;

  public function processMessage(AMQPEnvelope $envelope, AMQPMessage $message): void;

  public function processFailure(AMQPEnvelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void;

  public function maxIterationsReached(): bool;

  public function maxLifeTimeReached(): bool;

  public function getMaxIterations(): int;

  public function getMaxLifeTime(): DrupalDateTime;

  public function getMaxLifeTimeInterval(): \DateInterval;
}

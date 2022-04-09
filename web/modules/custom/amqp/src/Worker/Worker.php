<?php

namespace Drupal\amqp\Worker;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use Drupal\Component\Datetime\DateTimePlus;
use PhpAmqpLib\Message\AMQPMessage;

interface Worker
{
  public function getName(): string;

  public function processMessage(Envelope $envelope, AMQPMessage $message): void;

  public function processFailure(Envelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void;

  public function maxIterationsReached(): bool;

  public function maxLifeTimeReached(): bool;

  public function getMaxIterations(): int;

  public function getMaxLifeTime(): DateTimePlus;

  public function getMaxLifeTimeInterval(): \DateInterval;
}

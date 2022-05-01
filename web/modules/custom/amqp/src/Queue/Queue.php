<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Worker\Worker;

interface Queue
{
  public function getName(): string;

  public function getWorker(): Worker;

  public function getNumberOfConsumers(): int;

  public function queue(Envelope $envelope): void;

  public function queueBatch(array $envelopes): void;
}

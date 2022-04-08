<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\Worker\Worker;

interface Queue
{
  public function getName(): string;

  public function getWorker(): Worker;

  public function queue(AMQPEnvelope $envelope): void;

  public function queueBatch(array $envelopes): void;
}

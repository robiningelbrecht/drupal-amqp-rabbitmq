<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\Worker\Worker;

interface Queue
{
  public function getName(): string;

  public function getWorker(): Worker;

  public function queue(string $amqpMessage): void;

  public function queueBatch(array $amqpMessages): void;
}

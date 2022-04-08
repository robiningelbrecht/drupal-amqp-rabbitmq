<?php

namespace Drupal\amqp\Queue;

class QueueFactory
{
  private array $queues = [];

  public function addQueue(Queue $queue): void
  {
    $this->queues[$queue->getName()] = $queue;
  }

  public function getQueue(string $name): Queue
  {
    if (isset($this->queues[$name])) {
      return $this->queues[$name];
    }

    throw new \RuntimeException(sprintf('Queue "%s" not registered in factory', $name));
  }
}

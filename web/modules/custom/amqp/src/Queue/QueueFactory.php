<?php

namespace Drupal\amqp\Queue;

class QueueFactory
{
  private array $queues = [];

  public function registerQueue(Queue $queue): void
  {
    $this->queues[$queue->getName()] = $queue;
  }

  public function getQueue(string $name): Queue
  {
    return $this->queues[$name] ?? throw new \RuntimeException(sprintf('Queue "%s" not registered in factory', $name));
  }

  public function getQueues(): array
  {
    return $this->queues;
  }
}

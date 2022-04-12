<?php

namespace Drupal\amqp\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\Queue;

class DelayedQueueFactory
{

  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {
  }

  public function buildWithDelay(int $delayInSeconds, Queue $queue): DelayedQueue
  {
    if (!is_subclass_of($queue, SupportsDelay::class)) {
      throw new \InvalidArgumentException('This queue does not support delayed queueing');
    }

    return new DelayedQueue(
      $queue,
      $delayInSeconds,
      $this->AMQPChannelFactory
    );
  }
}

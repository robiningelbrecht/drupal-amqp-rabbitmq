<?php

namespace Drupal\amqp\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPClient;
use Drupal\amqp\Queue\Queue;

class DelayedQueueFactory
{

  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
    private AMQPClient $AMQPClient,
  )
  {
  }

  public function buildWithDelayForQueue(int $delayInSeconds, Queue $queue): DelayedQueue
  {
    return new DelayedQueue(
      $queue,
      $delayInSeconds,
      $this->AMQPChannelFactory,
      $this->AMQPClient
    );
  }
}

<?php

namespace Drupal\amqp\Queue\FailedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\Queue;

class FailedQueueFactory
{
  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {

  }

  public function buildFor(Queue $queue): FailedQueue{
    return new FailedQueue(
      $queue,
      $this->AMQPChannelFactory
    );
  }
}

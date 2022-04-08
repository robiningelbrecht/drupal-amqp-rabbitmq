<?php

namespace Drupal\cqrs\Queue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\Queue;

class FailedCommandQueueFactory
{
  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {

  }

  public function buildFor(Queue $queue): FailedCommandQueue{
    return new FailedCommandQueue(
      $queue,
      $this->AMQPChannelFactory
    );
  }
}

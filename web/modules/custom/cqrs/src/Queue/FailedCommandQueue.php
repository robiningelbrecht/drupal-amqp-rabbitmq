<?php

namespace Drupal\cqrs\Queue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Queue\BaseQueue;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;

class FailedCommandQueue extends BaseQueue
{
  public function __construct(
    private Queue $queue,
    AMQPChannelFactory $AMQPChannelFactory
  )
  {
    parent::__construct($AMQPChannelFactory);
  }

  public function getName(): string
  {
    return $this->queue->getName() . '-failed';
  }

  public function getWorker(): Worker
  {
    throw new \RuntimeException('Failed command queues do not have workers');
  }

}

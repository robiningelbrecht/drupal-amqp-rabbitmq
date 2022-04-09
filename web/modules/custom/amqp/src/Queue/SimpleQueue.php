<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Worker\SimpleQueueWorker;
use Drupal\amqp\Worker\Worker;

class SimpleQueue extends BaseQueue
{

  public function __construct(
    private SimpleQueueWorker $simpleQueueWorker,
    AMQPChannelFactory $AMQPChannelFactory
  )
  {
    parent::__construct($AMQPChannelFactory);
  }

  public function getName(): string
  {
    return 'simple-queue';
  }

  public function getWorker(): Worker
  {
    return $this->simpleQueueWorker;
  }
}

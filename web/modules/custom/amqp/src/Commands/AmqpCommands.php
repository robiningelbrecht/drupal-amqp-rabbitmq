<?php

namespace Drupal\amqp\Commands;

use Drupal\amqp\Consumer;
use Drupal\amqp\Queue\QueueFactory;
use Drush\Commands\DrushCommands;

class AmqpCommands extends DrushCommands
{

  public function __construct(
    private Consumer $consumer,
    private QueueFactory $queueFactory,
  )
  {
    parent::__construct();
  }

  /**
   * @command amqp:consumer
   */
  public function consumer(string $queueName)
  {
    $queue = $this->queueFactory->getQueue($queueName);
    $this->consumer->consume($queue);
  }

  /**
   * @command amqp:queue
   */
  public function queue(string $queueName)
  {
    $queue = $this->queueFactory->getQueue($queueName);
    $queue->queue('test one');
    $queue->queueBatch(['test batch one', 'test batch two']);
  }

}

<?php

namespace Drupal\amqp\Commands;

use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\Consumer;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\Core\Datetime\DrupalDateTime;
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
   * @command amqp:consume
   */
  public function consume(string $queueName)
  {
    $queue = $this->queueFactory->getQueue($queueName);
    $this->consumer->consume($queue);
  }

  /**
   * @command amqp:queue-test
   */
  public function queueTest()
  {
    $queue = $this->queueFactory->getQueue('simple-queue');

    $queue->queue(AMQPEnvelope::fromContentAndDate('test one', new DrupalDateTime('now')));
    $queue->queueBatch([
      AMQPEnvelope::fromContentAndDate('test batch one', new DrupalDateTime('now')),
      AMQPEnvelope::fromContentAndDate('test batch two', new DrupalDateTime('now')),
    ]);
  }

}

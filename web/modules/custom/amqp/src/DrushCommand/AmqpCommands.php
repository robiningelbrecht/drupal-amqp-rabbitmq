<?php

namespace Drupal\amqp\DrushCommand;

use Drupal\amqp\Consumer;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\Component\Datetime\DateTimePlus;
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
   * @command amqp:simple-queue-test
   */
  public function simpleQueueTest(string $stampTime = null)
  {
    $stampDateTime = new DateTimePlus($stampTime ?? 'now');

    $queue = $this->queueFactory->getQueue('simple-queue');
    $queue->queue(AMQPEnvelope::fromContentAndDate('test one', $stampDateTime));
    $queue->queueBatch([
      AMQPEnvelope::fromContentAndDate('test batch one', $stampDateTime),
      AMQPEnvelope::fromContentAndDate('test batch two', $stampDateTime),
    ]);
  }
}

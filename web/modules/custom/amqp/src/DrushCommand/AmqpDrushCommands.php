<?php

namespace Drupal\amqp\DrushCommand;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Consumer;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\QueueFactory;
use Drush\Commands\DrushCommands;

class AmqpDrushCommands extends DrushCommands
{

  public function __construct(
    private Consumer $consumer,
    private QueueFactory $queueFactory,
    private Clock $clock,
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
  public function simpleQueueTest()
  {
    $queue = $this->queueFactory->getQueue('simple-queue');
    $queue->queue(AMQPEnvelope::fromContentAndDate('test one', $this->clock->getCurrentDateTimeImmutable()));
    /*$queue->queueBatch([
      AMQPEnvelope::fromContentAndDate('test batch one', $this->clock->getCurrentDateTimeImmutable()),
      AMQPEnvelope::fromContentAndDate('test batch two', $this->clock->getCurrentDateTimeImmutable()),
    ]);*/
  }
}

<?php

namespace Drupal\amqp;

use \PhpAmqpLib\Channel\AMQPChannel;
use \Drupal\amqp\Queue\Queue;

class AMQPChannelFactory
{
  private array $channels = [];

  public function __construct(
    private AMQPStreamConnectionFactory $AMQPStreamConnectionFactory
  )
  {

  }

  public function getForQueue(Queue $queue): AMQPChannel
  {
    if (!array_key_exists($queue->getName(), $this->channels)) {
      $this->channels[$queue->getName()] = $this->AMQPStreamConnectionFactory->get()->channel();

      /*
       * name: $queue
       * passive: false
       * durable: true // the queue will survive server restarts
       * exclusive: false // the queue can be accessed in other channels
       * auto_delete: false //the queue won't be deleted once the channel is closed.
       */
      $this->channels[$queue->getName()]->queue_declare($queue->getName(), false, true, false, false);
      $this->channels[$queue->getName()]->basic_qos(null, 1, null);
    }

    return $this->channels[$queue->getName()];
  }


}

<?php

namespace Drupal\amqp;

use \PhpAmqpLib\Channel\AMQPChannel;
use \Drupal\amqp\Queue\Queue;

class AMQPChannelFactory
{
  private ?AMQPChannel $channel = null;

  public function __construct(
    private AMQPStreamConnectionFactory $AMQPStreamConnectionFactory
  )
  {

  }

  public function getForQueue(Queue $queue): AMQPChannel
  {
    if (null === $this->channel) {
      $this->channel = $this->AMQPStreamConnectionFactory->get()->channel();

      /*
       * name: $queue
       * passive: false
       * durable: true // the queue will survive server restarts
       * exclusive: false // the queue can be accessed in other channels
       * auto_delete: false //the queue won't be deleted once the channel is closed.
       */
      $this->channel->queue_declare($queue->getName(), false, true, false, false);
      $this->channel->basic_qos(null, 1, null);
    }

    return $this->channel;
  }


}

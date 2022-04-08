<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\AMQPChannelFactory;
use PhpAmqpLib\Message\AMQPMessage;

abstract class BaseQueue implements Queue
{

  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {

  }

  public function queue(string $amqpMessage): void
  {
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage($amqpMessage, $properties);
    $this->AMQPChannelFactory->getForQueue($this)->basic_publish($message, null, $this->getName());
  }

  public function queueBatch(array $amqpMessages): void
  {
    if (empty($amqpMessages)) {
      return;
    }

    $channel = $this->AMQPChannelFactory->getForQueue($this);

    foreach ($amqpMessages as $amqpMessage) {
      $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
      $message = new AMQPMessage($amqpMessage, $properties);
      $channel->batch_basic_publish($message, null, $this->getName());
    }
    $channel->publish_batch();
  }
}

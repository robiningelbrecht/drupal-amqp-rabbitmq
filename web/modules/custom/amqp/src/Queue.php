<?php

namespace Drupal\amqp;

use PhpAmqpLib\Message\AMQPMessage;

class Queue
{

  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {

  }

  public function getName(): string
  {
    return 'some-queue';
  }

  public function getWorker(): Worker
  {
    return new Worker();
  }

  public function queue(string $amqpMessage): void
  {
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage($amqpMessage, $properties);
    $this->AMQPChannelFactory->getForQueue($this)->basic_publish($message, null, $this->getName());
  }

  public function queueBatch(): void
  {

  }
}

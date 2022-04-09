<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\Envelope\Envelope;
use PhpAmqpLib\Message\AMQPMessage;

abstract class BaseQueue implements Queue
{

  public function __construct(
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {

  }

  public function queue(Envelope $envelope): void
  {
    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($envelope), $properties);
    $this->AMQPChannelFactory->getForQueue($this)->basic_publish($message, null, $this->getName());
  }

  public function queueBatch(array $envelopes): void
  {
    if (empty($envelopes)) {
      return;
    }

    if (!empty(array_filter($envelopes, fn($envelope) => !$envelope instanceof Envelope))) {
      throw new \RuntimeException(sprintf('All envelopes need to implement %s', Envelope::class));
    }

    $channel = $this->AMQPChannelFactory->getForQueue($this);

    foreach ($envelopes as $envelope) {
      $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
      $message = new AMQPMessage(serialize($envelope), $properties);
      $channel->batch_basic_publish($message, null, $this->getName());
    }
    $channel->publish_batch();
  }
}

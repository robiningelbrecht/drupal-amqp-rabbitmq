<?php

namespace Drupal\amqp\Queue\DelayedQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPChannelOptions;
use Drupal\amqp\AMQPClient;
use Drupal\amqp\Queue\BaseQueue;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;
use PhpAmqpLib\Channel\AMQPChannel;

class DelayedQueue extends BaseQueue
{

  private const X_DEAD_LETTER_EXCHANGE = 'dlx';

  public function __construct(
    private Queue $queue,
    private int $delayInSeconds,
    private AMQPChannelFactory $AMQPChannelFactory,
    private AMQPClient $AMQPClient,
  )
  {
    $this->guardThatExchangeHasBindingForQueue();

    if ($this->delayInSeconds < 1) {
      throw new \InvalidArgumentException('Delay cannot be less than 1 second');
    }
    parent::__construct($AMQPChannelFactory);
  }

  public function getName(): string
  {
    return 'delayed-' . $this->delayInSeconds . 's-' . $this->queue->getName();
  }

  public function getWorker(): Worker
  {
    throw new \RuntimeException('Delayed queues do not have workers');
  }

  protected function getChannel(): AMQPChannel
  {
    $options = new AMQPChannelOptions(false, true, false, false, false, [
      'x-dead-letter-exchange' => ['S', self::X_DEAD_LETTER_EXCHANGE],
      'x-dead-letter-routing-key' => ['S', $this->queue->getName()],
      'x-message-ttl' => ['I', $this->delayInSeconds * 1000],
      'x-expires' => ['I', $this->delayInSeconds * 1000 + 100000], // Keep the Q for 100s after the last message,
    ]);
    return $this->AMQPChannelFactory->getForQueue($this, $options);
  }

  private function guardThatExchangeHasBindingForQueue(): void
  {
    $bindings = $this->AMQPClient->getExchangeBindings(self::X_DEAD_LETTER_EXCHANGE);

    foreach ($bindings as $binding) {
      if ($binding['destination'] === $this->queue->getName()
        && $binding['routing_key'] === $this->queue->getName()
        && $binding['destination_type'] === 'queue') {
        return;
      }
    }

    // Make sure that every Q that implements this interface, is defined as a binding on the DLX exchange.
    // Routing key of the binding has to be the command queue name to where it has to be routed.
    throw new \InvalidArgumentException(sprintf(
      'Queue "%s" does not support delayed queueing. Make sure the exchange "%s" has a binding with a routing key and a destination "%s"',
      $this->queue->getName(),
      self::X_DEAD_LETTER_EXCHANGE,
      $this->queue->getName()
    ));
  }

}

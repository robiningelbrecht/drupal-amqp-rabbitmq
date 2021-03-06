<?php

namespace Drupal\cqrs\CommandQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\BaseQueue;
use Drupal\amqp\Worker\Worker;
use Drupal\cqrs\Command;
use Drupal\cqrs\CommandQueueWorker;

abstract class CommandQueue extends BaseQueue
{

  public function __construct(
    private CommandQueueWorker $commandQueueWorker,
    AMQPChannelFactory $AMQPChannelFactory,
  )
  {
    parent::__construct($AMQPChannelFactory);
  }

  public function getWorker(): Worker
  {
    return $this->commandQueueWorker;
  }

  public function queue(Envelope $envelope): void
  {
    if(!$envelope instanceof Command){
      throw new \RuntimeException(sprintf('Queue "%s" requires a command to be queued, %s given', $this->getName(), $envelope::class));
    }

    parent::queue($envelope);
  }

}

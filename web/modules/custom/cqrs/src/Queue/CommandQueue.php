<?php

namespace Drupal\cqrs\Queue;

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
      throw new \RuntimeException('This queue requires a command to be queued');
    }

    parent::queue($envelope);
  }

}

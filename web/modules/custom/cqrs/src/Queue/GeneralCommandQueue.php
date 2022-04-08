<?php

namespace Drupal\cqrs\Queue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\Queue\BaseQueue;
use Drupal\amqp\Worker\Worker;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\cqrs\Command;
use Drupal\cqrs\Worker\CommandWorker;

class GeneralCommandQueue extends BaseQueue
{
  public function __construct(
    private CommandWorker $commandWorker,
    AMQPChannelFactory $AMQPChannelFactory,
  )
  {
    parent::__construct($AMQPChannelFactory);
  }

  public function getName(): string
  {
    return 'general-command-queue';
  }

  public function getWorker(): Worker
  {
    return $this->commandWorker;
  }

  public function queueCommand(Command $command): void{
    $envelope = AMQPEnvelope::fromContentAndDate(serialize($command), new DrupalDateTime('now'));
    $this->queue($envelope);
  }

}

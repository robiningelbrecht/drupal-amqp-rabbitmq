<?php

namespace Drupal\cqrs\Worker;

use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\BaseWorker;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\cqrs\Command;
use Drupal\cqrs\CommandDispatcher;
use Drupal\cqrs\Queue\FailedCommandQueueFactory;
use PhpAmqpLib\Message\AMQPMessage;

class CommandWorker extends BaseWorker
{
  public function __construct(
    private CommandDispatcher $commandDispatcher,
    private FailedCommandQueueFactory $failedCommandQueueFactory,
  )
  {
    parent::__construct();
  }

  public function getName(): string
  {
    return 'Command worker';
  }

  public function processMessage(AMQPEnvelope $envelope, AMQPMessage $message): void
  {
    /** @var Command $command */
    $command = unserialize($envelope->getContent());
    $this->commandDispatcher->dispatch($command);
  }

  public function processFailure(AMQPEnvelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void
  {
    /** @var Command $command */
    $command = unserialize($envelope->getContent());
    $command->setMetaData([
      'exceptionMessage' => $exception->getMessage(),
      'traceAsString' => $exception->getTraceAsString(),
    ]);

    $envelope = AMQPEnvelope::fromContentAndDate(
      serialize($command),
      new DrupalDateTime('now'),
    );

    $this->failedCommandQueueFactory->buildFor($queue)->queue($envelope);
  }

}

<?php

namespace Drupal\cqrs;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\ConsoleLogger;
use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\FailedQueue\FailedQueueFactory;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\BaseWorker;
use PhpAmqpLib\Message\AMQPMessage;

class CommandQueueWorker extends BaseWorker
{
  public function __construct(
    private CommandDispatcher $commandDispatcher,
    private FailedQueueFactory $failedQueueFactory,
    private ConsoleLogger $logger,
    Clock $clock
  )
  {
    parent::__construct($clock);
  }

  public function getName(): string
  {
    return 'Command worker';
  }

  public function processMessage(Envelope $envelope, AMQPMessage $message): void
  {
    /** @var Command $command */
    $command = $envelope;
    $this->commandDispatcher->dispatch($command);
    $this->logger->success(sprintf(
      'Worker "%s" dispatched command "%s"',
      $this->getName(),
      (new \ReflectionClass($command))->getShortName(),
    ));
  }

  public function processFailure(Envelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void
  {
    /** @var Command $command */
    $command = $envelope;
    $command->setMetaData([
      'exceptionMessage' => $exception->getMessage(),
      'traceAsString' => $exception->getTraceAsString(),
    ]);

    $failedQueue = $this->failedQueueFactory->buildFor($queue);
    $this->logger->warning(sprintf('Message has been send to failed queue "%s"', $failedQueue->getName()));

    $failedQueue->queue($command);
  }

}

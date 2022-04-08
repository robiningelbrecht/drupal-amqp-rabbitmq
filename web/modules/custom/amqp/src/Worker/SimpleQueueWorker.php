<?php

namespace Drupal\amqp\Worker;

use Drupal\amqp\ConsoleLog;
use PhpAmqpLib\Message\AMQPMessage;

class SimpleQueueWorker extends BaseWorker
{
  private ConsoleLog $logger;

  public function __construct()
  {
    parent::__construct();
    $this->logger = ConsoleLog::create();
  }

  public function getName(): string
  {
    return 'Simple queue worker';
  }

  public function processMessage(AMQPMessage $message): void
  {
    $this->logger->success(sprintf('Processed message with body "%s"', $message->getBody()));
  }

  public function processFailure(AMQPMessage $message, \Throwable $exception): void
  {
    $this->logger->success(sprintf('Could not processes message with body "%s"', $message->getBody()));
  }
}

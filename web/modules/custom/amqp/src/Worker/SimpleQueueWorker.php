<?php

namespace Drupal\amqp\Worker;

use Drupal\amqp\AMQPEnvelope;
use Drupal\amqp\ConsoleLogger;
use PhpAmqpLib\Message\AMQPMessage;

class SimpleQueueWorker extends BaseWorker
{
  private ConsoleLogger $logger;

  public function __construct()
  {
    parent::__construct();
    $this->logger = ConsoleLogger::create();
  }

  public function getName(): string
  {
    return 'Simple queue worker';
  }

  public function processMessage(AMQPEnvelope $envelope, AMQPMessage $message): void
  {
    $this->logger->success(sprintf(
      'Processed message with content "%s", queued on %s',
      $envelope->getContent(),
      $envelope->getStampTime()->format('H:i:s')
    ));
  }

  public function processFailure(AMQPEnvelope $envelope, AMQPMessage $message, \Throwable $exception): void
  {
    $this->logger->error(sprintf(
      'Could not processes message with content "%s", queued on %s',
      $envelope->getContent(),
      $envelope->getStampTime()->format('H:i:s')
    ));
    $this->logger->error(sprintf(
      'Exception: %s',
      $exception->getMessage(),
    ));
  }
}

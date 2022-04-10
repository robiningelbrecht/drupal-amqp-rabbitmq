<?php

namespace Drupal\amqp\Worker;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\ConsoleLogger;
use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use PhpAmqpLib\Message\AMQPMessage;

class SimpleQueueWorker extends BaseWorker
{

  public function __construct(
    private ConsoleLogger $logger,
    Clock $clock
  )
  {
    parent::__construct($clock);
  }

  public function getName(): string
  {
    return 'Simple queue worker';
  }

  public function processMessage(Envelope $envelope, AMQPMessage $message): void
  {
    $this->logger->success(sprintf(
      'Processed message with content "%s", queued on %s',
      $envelope->getContent(),
      $envelope->getStampTime()->format('H:i:s')
    ));
  }

  public function processFailure(Envelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void
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

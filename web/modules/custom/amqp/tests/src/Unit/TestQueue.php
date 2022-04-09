<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;
use Drupal\Component\Datetime\DateTimePlus;

class TestQueue implements Queue
{
  public function getName(): string
  {
    return 'test-queue';
  }

  public function getWorker(): Worker
  {
    return new TestWorker(new DateTimePlus('now'));
  }

  public function queue(Envelope $envelope): void
  {
    // TODO: Implement queue() method.
  }

  public function queueBatch(array $envelopes): void
  {
    // TODO: Implement queueBatch() method.
  }

}
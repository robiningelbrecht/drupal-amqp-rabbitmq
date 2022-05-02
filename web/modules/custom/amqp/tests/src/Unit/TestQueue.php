<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;

class TestQueue implements Queue
{
  public function getName(): string
  {
    return 'test-queue';
  }

  public function getNumberOfConsumers(): int
  {
    return 1;
  }

  public function getWorker(): Worker
  {
    return new TestWorker(new \DateTimeImmutable('now'));
  }

  public function queue(Envelope $envelope): void
  {

  }

  public function queueBatch(array $envelopes): void
  {

  }

}

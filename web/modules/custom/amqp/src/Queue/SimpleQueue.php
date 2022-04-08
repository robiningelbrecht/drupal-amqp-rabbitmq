<?php

namespace Drupal\amqp\Queue;

use Drupal\amqp\Worker\SimpleQueueWorker;
use Drupal\amqp\Worker\Worker;

class SimpleQueue extends BaseQueue
{

  public function getName(): string
  {
    return 'simple-queue';
  }

  public function getWorker(): Worker
  {
    return new SimpleQueueWorker();
  }
}

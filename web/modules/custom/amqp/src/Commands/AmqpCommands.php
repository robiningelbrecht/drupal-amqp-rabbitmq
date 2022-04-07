<?php

namespace Drupal\amqp\Commands;

use Drupal\amqp\Consumer;
use Drupal\amqp\Queue;
use Drush\Commands\DrushCommands;

class AmqpCommands extends DrushCommands {

  public function __construct(
    private Consumer $consumer,
    private Queue $queue,
  )
  {
    parent::__construct();
  }

  /**
   * @command amqp:consumer
   */
  public function consumer() {
    $this->consumer->consume($this->queue);
  }

  /**
   * @command amqp:queue
   */
  public function queue() {
    $this->queue->queue('test');
  }

}

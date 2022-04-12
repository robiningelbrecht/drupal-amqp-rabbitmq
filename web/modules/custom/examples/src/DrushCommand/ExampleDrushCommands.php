<?php

namespace Drupal\examples\DrushCommand;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\examples\AddDatabaseLog\AddDatabaseLog;
use Drush\Commands\DrushCommands;

class ExampleDrushCommands extends DrushCommands
{

  public function __construct(
    private QueueFactory $queueFactory,
    private Clock $clock,
  )
  {
    parent::__construct();
  }

  /**
   * @command examples:general-command-queue-test
   */
  public function generalCommandQueueTest()
  {
    $queue = $this->queueFactory->getQueue('general-command-queue');

    $queue->queue(new AddDatabaseLog(
      'This message originated from a queued command',
      $this->clock->getCurrentDateTimeImmutable()
    ));
  }
}

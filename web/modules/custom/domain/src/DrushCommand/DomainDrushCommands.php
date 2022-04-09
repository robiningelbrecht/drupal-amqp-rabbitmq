<?php

namespace Drupal\domain\DrushCommand;

use Drupal\amqp\Queue\QueueFactory;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\domain\AddDatabaseLog\AddDatabaseLog;
use Drush\Commands\DrushCommands;

class DomainDrushCommands extends DrushCommands
{

  public function __construct(
    private QueueFactory $queueFactory,
  )
  {
    parent::__construct();
  }

  /**
   * @command domain:general-command-queue-test
   */
  public function generalCommandQueueTest()
  {
    $queue = $this->queueFactory->getQueue('general-command-queue');

    $queue->queue(new AddDatabaseLog(
      'This message originated from a queued command',
      new DrupalDateTime('now')
    ));
  }
}

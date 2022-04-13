<?php

namespace Drupal\examples\DrushCommand;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Queue\DelayedQueue\DelayedQueueFactory;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\examples\AddDatabaseLog\AddDatabaseLog;
use Drush\Commands\DrushCommands;

class ExampleDrushCommands extends DrushCommands
{

  public function __construct(
    private QueueFactory $queueFactory,
    private DelayedQueueFactory $delayedQueueFactory,
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

  /**
   * @command examples:general-command-delayed-queue-test
   */
  public function generalCommandDelayedQueueTest()
  {
    $queue = $this->queueFactory->getQueue('general-command-queue');
    $delayedQueue = $this->delayedQueueFactory->buildWithDelayForQueue(
      10,
      $queue
    );

    $delayedQueue->queue(new AddDatabaseLog(
      'This message originated from a queued command with a delay of 10s',
      $this->clock->getCurrentDateTimeImmutable()
    ));
  }
}

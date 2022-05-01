<?php

namespace Drupal\amqp;

use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Queue\QueueFactory;

class SupervisordConfig
{

  public function __construct(
    private QueueFactory $queueFactory,
    private FilePutContentsWrapper $filePutContentsWrapper,
  )
  {
  }

  public function writeAll(): void
  {
    foreach ($this->queueFactory->getQueues() as $queue) {
      $this->write($queue);
    }
  }

  public function write(Queue $queue): void
  {
    $queueName = $queue->getName();
    $numberOfConsumers = $queue->getNumberOfConsumers();

    $contents = <<<FILE
[program:ampq-consume-$queueName]
process_name=%(program_name)s-%(process_num)02d
command=drush amqp:consume $queueName
numprocs=$numberOfConsumers
autostart=true
autorestart=true
FILE;

    $this->filePutContentsWrapper->filePutContents(DRUPAL_ROOT . '/../supervisord/queues/' . $queue->getName() . '.conf', $contents);
  }
}

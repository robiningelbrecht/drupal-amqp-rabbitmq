<?php

namespace Drupal\cqrs\CommandQueue;

use Drupal\amqp\Queue\DelayedQueue\SupportsDelay;

class GeneralCommandQueue extends CommandQueue implements SupportsDelay
{

  public function getName(): string
  {
    return 'general-command-queue';
  }


}

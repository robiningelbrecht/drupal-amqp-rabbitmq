<?php

namespace Drupal\cqrs\CommandQueue;

class GeneralCommandQueue extends CommandQueue
{

  public function getName(): string
  {
    return 'general-command-queue';
  }


}

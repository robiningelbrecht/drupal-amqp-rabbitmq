<?php

namespace Drupal\cqrs\Queue;

class GeneralCommandQueue extends CommandQueue
{

  public function getName(): string
  {
    return 'general-command-queue';
  }


}

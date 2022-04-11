<?php

namespace Drupal\Tests\cqrs\Unit\RunUnitTest;

use Drupal\cqrs\Command;
use Drupal\cqrs\CommandHandler;

class RunUnitTestWithoutCommandHandler implements CommandHandler
{
  public function handle(Command $command)
  {
    // TODO: Implement handle() method.
  }

}

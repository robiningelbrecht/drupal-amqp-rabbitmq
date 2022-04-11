<?php

namespace Drupal\Tests\cqrs\Unit\RunUnitTest;

use Drupal\cqrs\Command;
use Drupal\cqrs\CommandHandler;

class InvalidHandlerOfTheCommands implements CommandHandler
{
  public function handle(Command $command)
  {
    // TODO: Implement handle() method.
  }

}

<?php

namespace Drupal\examples\AddDatabaseLog;

use Drupal\cqrs\Command;
use Drupal\cqrs\CommandHandler;
use Drupal\cqrs\ProvideObjectInstanceGuard;

class AddDatabaseLogCommandHandler implements CommandHandler
{

  use ProvideObjectInstanceGuard;

  public function handle(Command $command)
  {
    $this->guardThatObjectIsInstanceOf($command, AddDatabaseLog::class);
    /** @var AddDatabaseLog $command */
    \Drupal::logger('domain')->debug($command->getMessage());
  }

}

<?php

namespace Drupal\examples\AddDatabaseLog;

use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\cqrs\Command;
use Drupal\cqrs\CommandHandler;
use Drupal\cqrs\ProvideObjectInstanceGuard;

class AddDatabaseLogCommandHandler implements CommandHandler
{

  use ProvideObjectInstanceGuard;

  public function __construct(
    private LoggerChannelFactory $loggerChannelFactory
  )
  {
  }

  public function handle(Command $command)
  {
    $this->guardThatObjectIsInstanceOf($command, AddDatabaseLog::class);
    /** @var AddDatabaseLog $command */
    $this->loggerChannelFactory->get('examples')->debug($command->getMessage());
  }

}

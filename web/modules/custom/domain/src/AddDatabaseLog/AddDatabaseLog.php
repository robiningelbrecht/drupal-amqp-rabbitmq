<?php

namespace Drupal\domain\AddDatabaseLog;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\cqrs\Command;

class AddDatabaseLog extends Command
{
  public function __construct(
    private string $message,
    DrupalDateTime $stampTime
  )
  {
    parent::__construct($stampTime);
  }

  public function getMessage(): string
  {
    return $this->message;
  }
}

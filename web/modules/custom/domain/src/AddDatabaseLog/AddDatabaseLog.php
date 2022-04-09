<?php

namespace Drupal\domain\AddDatabaseLog;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\cqrs\Command;

class AddDatabaseLog extends Command
{
  public function __construct(
    private string $message,
    DateTimePlus $stampTime
  )
  {
    parent::__construct($stampTime);
  }

  public function getMessage(): string
  {
    return $this->message;
  }
}

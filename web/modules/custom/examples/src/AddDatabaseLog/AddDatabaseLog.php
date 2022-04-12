<?php

namespace Drupal\examples\AddDatabaseLog;

use Drupal\cqrs\Command;

class AddDatabaseLog extends Command
{
  public function __construct(
    private string $message,
    \DateTimeImmutable $stampTime
  )
  {
    parent::__construct($stampTime);
  }

  public function getMessage(): string
  {
    return $this->message;
  }
}

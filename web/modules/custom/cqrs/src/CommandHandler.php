<?php

namespace Drupal\cqrs;

interface CommandHandler
{
  public function handle(Command $command);
}

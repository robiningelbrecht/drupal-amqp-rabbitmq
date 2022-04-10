<?php

namespace Drupal\amqp\Clock;

interface Clock
{
  public function getCurrentDateTimeImmutable(): \DateTimeImmutable;
}

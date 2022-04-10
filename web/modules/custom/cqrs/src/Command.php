<?php

namespace Drupal\cqrs;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Envelope\MetadataAware;

abstract class Command implements Envelope
{
  use MetadataAware;

  public function __construct(
    private \DateTimeImmutable $stampTime,
  )
  {
  }

  public function getStampTime(): \DateTimeImmutable
  {
    return $this->stampTime;
  }

  public function getContent(): string
  {
    return serialize($this);
  }

}

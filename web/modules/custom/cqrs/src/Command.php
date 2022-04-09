<?php

namespace Drupal\cqrs;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Envelope\MetadataAware;
use Drupal\Component\Datetime\DateTimePlus;

abstract class Command implements Envelope
{
  use MetadataAware;

  public function __construct(
    private DateTimePlus $stampTime,
  )
  {
  }

  public function getStampTime(): DateTimePlus
  {
    return $this->stampTime;
  }

  public function getContent(): string
  {
    return serialize($this);
  }

}

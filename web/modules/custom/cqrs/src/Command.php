<?php

namespace Drupal\cqrs;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Envelope\MetadataAware;
use Drupal\Component\Annotation\Doctrine\Compatibility\ReflectionClass;
use Drupal\Core\Datetime\DrupalDateTime;

abstract class Command implements Envelope
{
  use MetadataAware;

  public function __construct(
    private DrupalDateTime $stampTime,
  )
  {
  }

  public function getStampTime(): DrupalDateTime
  {
    return $this->stampTime;
  }

  public function getContent(): string
  {
    return serialize($this);
  }

}

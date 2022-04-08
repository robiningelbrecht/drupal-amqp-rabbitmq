<?php

namespace Drupal\cqrs;

use Drupal\amqp\AMQPEnvelope;

abstract class Command extends AMQPEnvelope
{

  private array $metaData = [];

  public function setMetaData(array $metaData): void
  {
    $this->metaData = $metaData;
  }
}

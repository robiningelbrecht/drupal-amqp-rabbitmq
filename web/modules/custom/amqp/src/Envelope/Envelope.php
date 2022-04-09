<?php

namespace Drupal\amqp\Envelope;

use Drupal\Core\Datetime\DrupalDateTime;

interface Envelope
{
  public function getContent(): string;

  public function getStampTime(): DrupalDateTime;
}

<?php

namespace Drupal\amqp\Envelope;

use Drupal\Component\Datetime\DateTimePlus;

interface Envelope
{
  public function getContent(): string;

  public function getStampTime(): DateTimePlus;
}

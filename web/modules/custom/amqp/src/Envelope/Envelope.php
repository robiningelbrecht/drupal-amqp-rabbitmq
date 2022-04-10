<?php

namespace Drupal\amqp\Envelope;

interface Envelope
{
  public function getContent(): string;

  public function getStampTime(): \DateTimeImmutable;
}

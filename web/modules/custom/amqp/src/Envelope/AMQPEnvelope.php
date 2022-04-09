<?php

namespace Drupal\amqp\Envelope;

use Drupal\Core\Datetime\DrupalDateTime;

class AMQPEnvelope implements Envelope
{

  private function __construct(
    private string $content,
    private DrupalDateTime $stampTime,
  )
  {
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public function getStampTime(): DrupalDateTime
  {
    return $this->stampTime;
  }

  public static function fromContentAndDate(string $content, DrupalDateTime $stampTime): self
  {
    return new self($content, $stampTime);
  }
}

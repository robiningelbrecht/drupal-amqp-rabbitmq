<?php

namespace Drupal\amqp\Envelope;

use Drupal\Component\Datetime\DateTimePlus;

class AMQPEnvelope implements Envelope
{

  private function __construct(
    private string $content,
    private DateTimePlus $stampTime,
  )
  {
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public function getStampTime(): DateTimePlus
  {
    return $this->stampTime;
  }

  public static function fromContentAndDate(string $content, DateTimePlus $stampTime): self
  {
    return new self($content, $stampTime);
  }
}

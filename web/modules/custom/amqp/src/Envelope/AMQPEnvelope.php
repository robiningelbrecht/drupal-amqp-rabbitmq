<?php

namespace Drupal\amqp\Envelope;

class AMQPEnvelope implements Envelope
{

  private function __construct(
    private string $content,
    private \DateTimeImmutable $stampTime,
  )
  {
  }

  public function getContent(): string
  {
    return $this->content;
  }

  public function getStampTime(): \DateTimeImmutable
  {
    return $this->stampTime;
  }

  public static function fromContentAndDate(string $content, \DateTimeImmutable $stampTime): self
  {
    return new self($content, $stampTime);
  }
}

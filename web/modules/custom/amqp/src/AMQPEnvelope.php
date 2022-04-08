<?php

namespace Drupal\amqp;

use Drupal\Core\Datetime\DrupalDateTime;

class AMQPEnvelope
{
  private function __construct(
    private string $content,
    private DrupalDateTime $stampTime,
    private array $metadata = [],
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

  public function getMetadata(): array
  {
    return $this->metadata;
  }

  public static function fromContentAndDate(string $content, DrupalDateTime $stampTime): self
  {
    return new self($content, $stampTime);
  }
}

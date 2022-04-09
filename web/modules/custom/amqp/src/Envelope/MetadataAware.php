<?php

namespace Drupal\amqp\Envelope;

trait MetadataAware
{
  private array $metadata = [];

  public function getMetadata(): array
  {
    return $this->metadata;
  }

  public function setMetadata(array $metadata): void
  {
    $this->metadata = $metadata;
  }
}

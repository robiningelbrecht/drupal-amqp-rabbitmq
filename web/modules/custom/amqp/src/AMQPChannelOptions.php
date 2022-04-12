<?php

namespace Drupal\amqp;

class AMQPChannelOptions
{
  // See PhpAmqpLib\Channel\AMQPChannel::queue_declare() for explanation for params.
  public function __construct(
    private bool $passive = false,
    private bool $durable = false,
    private bool $exclusive = false,
    private bool $autoDelete = true,
    private bool $nowait = false,
    private array $arguments = [],
    private ?int $ticket = null
  )
  {
  }

  public function isPassive(): bool
  {
    return $this->passive;
  }

  public function isDurable(): bool
  {
    return $this->durable;
  }

  public function isExclusive(): bool
  {
    return $this->exclusive;
  }

  public function isAutoDelete(): bool
  {
    return $this->autoDelete;
  }

  public function isNowait(): bool
  {
    return $this->nowait;
  }

  public function getArguments(): array
  {
    return $this->arguments;
  }

  public function getTicket(): ?int
  {
    return $this->ticket;
  }

  public static function default(): self
  {
    return new self(false, true, false, false);
  }
}

<?php

namespace Drupal\Tests\amqp\Unit\Envelope;

use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Tests\UnitTestCase;

class AMQPEnvelopeTest extends UnitTestCase
{
  public function testFromContentAndDate(): void
  {
    $envelope = AMQPEnvelope::fromContentAndDate('the content', new DateTimePlus('2022-04-10'));

    $this->assertEquals('the content', $envelope->getContent());
    $this->assertEquals(new DateTimePlus('2022-04-10'), $envelope->getStampTime());
  }
}

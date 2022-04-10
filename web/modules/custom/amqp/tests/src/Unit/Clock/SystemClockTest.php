<?php

namespace Drupal\Tests\amqp\Unit\Clock;

use Drupal\amqp\Clock\SystemClock;
use Drupal\Tests\UnitTestCase;

class SystemClockTest extends UnitTestCase
{
  public function testGetCurrentDateTimeImmutable(): void
  {
    $clock = new SystemClock();

    $this->assertEquals(
      (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Brussels')))->format('Y-m-d H:i:s'),
      $clock->getCurrentDateTimeImmutable()->format('Y-m-d H:i:s'),
    );
  }
}

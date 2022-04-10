<?php

namespace Drupal\Tests\cqrs\Unit;

use Drupal\Tests\UnitTestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CommandTest extends UnitTestCase
{
  use MatchesSnapshots;

  public function testCommand(): void
  {
    $time = new \DateTimeImmutable('2022-04-10 20:04:33');
    $command = new TestCommand($time);
    $command->setMetadata([
      'key1' => 'value1',
      'ley2' => 'value2',
    ]);

    $this->assertEquals([
      'key1' => 'value1',
      'ley2' => 'value2',
    ], $command->getMetadata());

    $this->assertEquals($time, $command->getStampTime());
    $this->assertMatchesTextSnapshot($command->getContent());
  }
}

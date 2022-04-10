<?php

namespace Drupal\Tests\cqrs\Unit;

use Drupal\Tests\UnitTestCase;

class CommandTest extends UnitTestCase
{
  public function testCommand(): void
  {
    $command = new TestCommand(new \DateTimeImmutable('now'));
    $command->setMetadata([
      'key1' => 'value1',
      'ley2' => 'value2',
    ]);

    $this->assertEquals([
      'key1' => 'value1',
      'ley2' => 'value2',
    ], $command->getMetadata());
  }
}

<?php

namespace Drupal\Tests\cqrs\Unit;

use Drupal\cqrs\CommandDispatcher;
use Drupal\cqrs\ProvideObjectInstanceGuard;
use Drupal\Tests\UnitTestCase;

class ProvideObjectInstanceGuardTest extends UnitTestCase
{
  use ProvideObjectInstanceGuard;

  public function testGuardThatObjectIsInstanceOf(): void
  {
    $object = new TestCommand(new \DateTimeImmutable('now'));
    $this->guardThatObjectIsInstanceOf($object, TestCommand::class);
    $this->addToAssertionCount(1);
  }

  public function testGuardThatObjectIsInstanceOfItShouldThrow(): void
  {
    $object = new TestCommand(new \DateTimeImmutable('now'));

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Expected object to be of type Drupal\cqrs\CommandDispatcher, got Drupal\Tests\cqrs\Unit\TestCommand');

    $this->guardThatObjectIsInstanceOf($object, CommandDispatcher::class);
  }
}

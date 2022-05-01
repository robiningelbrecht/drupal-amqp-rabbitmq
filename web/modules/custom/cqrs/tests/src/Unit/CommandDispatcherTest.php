<?php

namespace Drupal\Tests\cqrs\Unit;

use Drupal\cqrs\CommandDispatcher;
use Drupal\cqrs\CommandHandler;
use Drupal\cqrs\CommandHandlerFactory;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CommandDispatcherTest extends UnitTestCase
{
  private CommandDispatcher $commandDispatcher;
  private MockObject $commandHandlerFactory;

  public function testDispatch(): void
  {
    $time = new \DateTimeImmutable('2022-04-10 20:04:33');
    $command = new TestCommand($time);

    $commandHandler = $this->createMock(CommandHandler::class);
    $this->commandHandlerFactory
      ->expects($this->once())
      ->method('getHandlerForCommand')
      ->with($command)
      ->willReturn($commandHandler);

    $commandHandler
      ->expects($this->once())
      ->method('handle')
      ->with($command);

    $this->commandDispatcher->dispatch($command);
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->commandHandlerFactory = $this->createMock(CommandHandlerFactory::class);
    $this->commandDispatcher = new CommandDispatcher(
      $this->commandHandlerFactory
    );
  }
}

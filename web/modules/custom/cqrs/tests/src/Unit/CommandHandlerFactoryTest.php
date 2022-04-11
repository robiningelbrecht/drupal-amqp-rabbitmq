<?php

namespace Drupal\Tests\cqrs\Unit;

use Drupal\cqrs\CanNotRegisterCommandHandler;
use Drupal\cqrs\CommandHandlerFactory;
use Drupal\Tests\cqrs\Unit\RunUnitTest\InvalidHandlerOfTheCommands;
use Drupal\Tests\cqrs\Unit\RunUnitTest\RunUnitTest;
use Drupal\Tests\cqrs\Unit\RunUnitTest\RunUnitTestCommandHandler;
use Drupal\Tests\cqrs\Unit\RunUnitTest\RunUnitTestWithoutCommandHandler;
use Drupal\Tests\UnitTestCase;

class CommandHandlerFactoryTest extends UnitTestCase
{
  private CommandHandlerFactory $commandHandlerFactory;

  public function testRegisterAndGet(): void
  {
    $commandHandler = new RunUnitTestCommandHandler();
    $this->commandHandlerFactory->registerCommandHandler($commandHandler);

    $this->assertEquals(
      $this->commandHandlerFactory->getHandlerForCommand(new RunUnitTest(new \DateTimeImmutable('2022-04-10 08:09:22'))),
      $commandHandler
    );
  }

  public function testItShouldThrowWhenInvalidFqcn(): void
  {
    $this->expectException(CanNotRegisterCommandHandler::class);
    $this->expectExceptionMessage('Fqcn "Drupal\Tests\cqrs\Unit\RunUnitTest\InvalidHandlerOfTheCommands" does not end with "CommandHandler"');
    $this->commandHandlerFactory->registerCommandHandler(new InvalidHandlerOfTheCommands());
  }

  public function testItShouldThrowWhenNoCorrespondingCommand(): void
  {
    $this->expectException(CanNotRegisterCommandHandler::class);
    $this->expectExceptionMessage('No corresponding command for commandHandler "Drupal\Tests\cqrs\Unit\RunUnitTest\RunUnitTestWithoutCommandHandler" found');
    $this->commandHandlerFactory->registerCommandHandler(new RunUnitTestWithoutCommandHandler());
  }

  protected function setUp()
  {
    parent::setUp();

    $this->commandHandlerFactory = new CommandHandlerFactory();
  }
}

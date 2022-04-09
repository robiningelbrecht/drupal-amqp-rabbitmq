<?php

namespace Drupal\cqrs;

class CommandHandlerFactory
{
  private const COMMAND_HANDLER_SUFFIX = 'CommandHandler';
  private array $commandHandlers = [];

  public function registerCommandHandler(CommandHandler $commandHandler): void
  {
    $this->guardThatFqcnEndsInCommandHandler($commandHandler::class);
    $this->guardThatThereIsACorrespondingCommand($commandHandler);

    $commandFqcn = str_replace(self::COMMAND_HANDLER_SUFFIX, '', $commandHandler::class);
    $this->commandHandlers[$commandFqcn] = $commandHandler;
  }

  public function getHandlerForCommand(Command $command): CommandHandler
  {
    return $this->commandHandlers[$command::class] ??
      throw new \RuntimeException(sprintf('CommandHandler for command "%s" not registered in factory', $command::class));
  }

  private function guardThatFqcnEndsInCommandHandler(string $fqcn): void
  {
    if (str_ends_with($fqcn, self::COMMAND_HANDLER_SUFFIX)) {
      return;
    }

    throw new CanNotRegisterCommandHandler(sprintf('Fqcn "%s" does not end with "CommandHandler"', $fqcn));
  }

  private function guardThatThereIsACorrespondingCommand(CommandHandler $commandHandler): void
  {
    $commandFqcn = str_replace(self::COMMAND_HANDLER_SUFFIX, '', $commandHandler::class);
    if (!class_exists($commandFqcn)) {
      throw new CanNotRegisterCommandHandler(sprintf('No corresponding command for commandHandler "%s" found', $commandHandler::class));
    }

    if (str_ends_with($commandFqcn, 'Command')) {
      throw new CanNotRegisterCommandHandler(sprintf('Command class names should not end in "Command", "%s" given', $commandFqcn));
    }
  }
}

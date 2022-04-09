<?php

namespace Drupal\cqrs;

class CommandDispatcher
{

  public function __construct(
    private CommandHandlerFactory $commandHandlerFactory
  )
  {
  }

  public function dispatch(Command $command)
  {
    $this->commandHandlerFactory->getHandlerForCommand($command)->handle($command);
  }
}

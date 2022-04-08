<?php

namespace Drupal\cqrs;

class CommandDispatcher
{
  public function dispatch(Command $domainCommand)
  {
    //$this->guardThatFqcnDoesntEndInCommand($domainCommand::class);

    //$commandHandler = $this->getCommandHandler($domainCommand);
    //$commandHandler->handle($domainCommand);
  }
}

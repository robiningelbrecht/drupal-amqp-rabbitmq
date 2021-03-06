<?php

namespace Drupal\amqp;

use Drupal\amqp\Clock\Clock;
use Drupal\Core\Logger\RfcLoggerTrait;
use Drupal\Core\Logger\RfcLogLevel;
use Psr\Log\LoggerInterface;
use Robo\Common\IO;
use Robo\Contract\IOAwareInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLogger implements LoggerInterface, IOAwareInterface
{
  public const SUCCESS = 8;

  use IO;
  use RfcLoggerTrait;

  public function __construct(
    OutputInterface $output,
    private Clock $clock
  )
  {
    $this->setOutput($output);
  }

  public function success($message, array $context = []): void
  {
    $this->log(self::SUCCESS, $message, $context);
  }

  public function log($level, $message, array $context = [])
  {
    $format = match ($level) {
      RfcLogLevel::EMERGENCY => ' [%s] <fg=white;bg=red;options=bold>[emergency]</fg=white;bg=red;options=bold> %s',
      RfcLogLevel::ALERT => ' [%s] <fg=white;bg=red;options=bold>[alert]</fg=white;bg=red;options=bold>     %s',
      RfcLogLevel::ERROR => ' [%s] <fg=white;bg=red;options=bold>[error]</fg=white;bg=red;options=bold>     %s',
      RfcLogLevel::CRITICAL => ' [%s] <fg=white;bg=red;options=bold>[critical]</fg=white;bg=red;options=bold>  %s',
      RfcLogLevel::WARNING => ' [%s] <fg=black;bg=yellow;options=bold>[warning]</fg=black;bg=yellow;options=bold>   %s',
      RfcLogLevel::NOTICE => ' [%s] <fg=black;bg=yellow;options=bold>[notice]</fg=black;bg=yellow;options=bold>    %s',
      RfcLogLevel::INFO => ' [%s] <fg=default;bg=blue;options=bold>[info]</fg=default;bg=blue;options=bold>      %s',
      RfcLogLevel::DEBUG => ' [%s] <fg=default;bg=blue;options=bold>[debug]</fg=default;bg=blue;options=bold>     %s',
      self::SUCCESS => ' [%s] <fg=default;bg=green;options=bold>[success]</fg=default;bg=green;options=bold>   %s',
    };

    $this->writeln(sprintf($format, $this->clock->getCurrentDateTimeImmutable()->format('H:i:s'), $message));
  }
}

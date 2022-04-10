<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\Clock\Clock;
use Drupal\amqp\Clock\PausedClock;
use Drupal\amqp\ConsoleLogger;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerTest extends UnitTestCase
{
  use MatchesSnapshots;

  private string $snapshotFileName;
  private ConsoleLogger $logger;
  private MockObject $output;
  private Clock $clock;

  public function testLog(): void
  {
    $this->output
      ->expects($this->exactly(8))
      ->method('writeln')
      ->willReturnCallback(function ($text) {
        $this->assertMatchesTextSnapshot($text);
      });

    foreach (range(1, 8) as $level) {
      $this->snapshotFileName = 'level_' . $level;
      $this->logger->log($level, sprintf('Logging for level %s', $level));
    }
  }

  public function testSuccess(): void
  {
    $this->output
      ->expects($this->once())
      ->method('writeln')
      ->with(' [20:10:04] <fg=default;bg=green;options=bold>[success]</fg=default;bg=green;options=bold>   success');

    $this->logger->success('success');
  }

  public function setUp()
  {
    $this->output = $this->createMock(OutputInterface::class);
    $this->clock = PausedClock::on(new \DateTimeImmutable('2022-04-10 20:10:04'));

    $this->logger = new ConsoleLogger($this->output, $this->clock);
  }

  protected function getSnapshotId(): string
  {
    return (new \ReflectionClass($this))->getShortName() . '__' .
      $this->getName() . '__' .
      $this->snapshotFileName;
  }
}

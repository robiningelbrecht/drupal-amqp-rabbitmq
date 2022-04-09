<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\ConsoleLogger;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerTest extends UnitTestCase
{
  use MatchesSnapshots;

  private string $snapshotFileName;
  private ConsoleLogger $logger;
  private MockObject $languageManager;
  private MockObject $logDateTime;
  private MockObject $output;

  public function testLog(): void
  {
    $this->logDateTime
      ->expects($this->exactly(8))
      ->method('format')
      ->willReturn('08:11');

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
    $this->logDateTime
      ->expects($this->once())
      ->method('format')
      ->willReturn('08:11');

    $this->output
      ->expects($this->once())
      ->method('writeln')
      ->with(' [08:11] <fg=default;bg=green;options=bold>[success]</fg=default;bg=green;options=bold>   success');

    $this->logger->success('success');
  }

  public function testCreate(): void
  {
    $logger = ConsoleLogger::create();
    $this->assertInstanceOf(ConsoleLogger::class, $logger);
  }

  public function setUp()
  {
    $this->output = $this->createMock(OutputInterface::class);
    $this->logDateTime = $this->createMock(DateTimePlus::class);
    $this->logger = new ConsoleLogger($this->output, $this->logDateTime);
  }

  protected function getSnapshotId(): string
  {
    return (new \ReflectionClass($this))->getShortName() . '__' .
      $this->getName() . '__' .
      $this->snapshotFileName;
  }
}

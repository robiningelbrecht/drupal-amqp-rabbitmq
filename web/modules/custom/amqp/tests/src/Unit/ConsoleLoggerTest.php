<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\ConsoleLogger;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Logger\RfcLogLevel;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerTest extends UnitTestCase
{
  use MatchesSnapshots;

  private string $snapshotFileName;
  private MockObject $languageManager;

  public function testLog(): void
  {
    $output = $this->createMock(OutputInterface::class);
    $logDateTime = $this->createMock(DrupalDateTime::class);
    $logger = new ConsoleLogger($output, $logDateTime);

    $logDateTime
      ->expects($this->exactly(8))
      ->method('format')
      ->willReturn('08:11');

    $output
      ->expects($this->exactly(8))
      ->method('writeln')
      ->willReturnCallback(function ($text) {
        $this->assertMatchesTextSnapshot($text);
      });

    foreach (range(1, 8) as $level) {
      $this->snapshotFileName = 'level_' . $level;
      $logger->log($level, sprintf('Logging for level %s', $level));
    }
  }

  public function testSuccess(): void
  {
    $output = $this->createMock(OutputInterface::class);
    $logDateTime = $this->createMock(DrupalDateTime::class);
    $logger = new ConsoleLogger($output, $logDateTime);

    $logDateTime
      ->expects($this->once())
      ->method('format')
      ->willReturn('08:11');

    $output
      ->expects($this->once())
      ->method('writeln')
      ->with(' [08:11] <fg=default;bg=green;options=bold>[success]</fg=default;bg=green;options=bold>   success');

    $logger->success('success');
  }

  public function testCreate(): void
  {
    $this->languageManager
      ->expects($this->once())
      ->method('getCurrentLanguage')
      ->willReturn($this->createMock(LanguageInterface::class));

    $logger = ConsoleLogger::create();

    $this->assertInstanceOf(ConsoleLogger::class, $logger);
  }

  public function setUp()
  {
    \Drupal::unsetContainer();
    $container = new ContainerBuilder();

    $this->languageManager = $this->getMockBuilder('Drupal\Core\Language\LanguageManager')
      ->disableOriginalConstructor()
      ->getMock();

    $container->set('language_manager', $this->languageManager);

    \Drupal::setContainer($container);
  }

  protected function getSnapshotId(): string
  {
    return (new \ReflectionClass($this))->getShortName() . '__' .
      $this->getName() . '__' .
      $this->snapshotFileName;
  }
}

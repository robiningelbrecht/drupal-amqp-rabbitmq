<?php

namespace Drupal\Tests\examples\Unit\AddDatabaseLog;

use Drupal\Core\Logger\LoggerChannel;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\examples\AddDatabaseLog\AddDatabaseLog;
use Drupal\examples\AddDatabaseLog\AddDatabaseLogCommandHandler;
use Drupal\Tests\cqrs\Unit\TestCommand;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AddDatabaseLogCommandHandlerTest extends UnitTestCase
{
  private AddDatabaseLogCommandHandler $addDatabaseLogCommandHandler;
  private MockObject $loggerChannelFactory;

  public function testHandle(): void
  {
    $loggerChannel = $this->createMock(LoggerChannel::class);

    $this->loggerChannelFactory
      ->expects($this->once())
      ->method('get')
      ->with('examples')
      ->willReturn($loggerChannel);

    $loggerChannel
      ->expects($this->once())
      ->method('debug')
      ->with('db log');

    $this->addDatabaseLogCommandHandler->handle(
      new AddDatabaseLog('db log', new \DateTimeImmutable('2022-04-12 12:10:55'))
    );
  }

  public function testHandleItShouldThrowOnInvalidCommand(): void
  {
    $this->loggerChannelFactory
      ->expects($this->never())
      ->method('get');

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Expected object to be of type Drupal\examples\AddDatabaseLog\AddDatabaseLog, got Drupal\Tests\cqrs\Unit\TestCommand');

    $this->addDatabaseLogCommandHandler->handle(
      new TestCommand(new \DateTimeImmutable('2022-04-12 12:10:55'))
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->loggerChannelFactory = $this->createMock(LoggerChannelFactory::class);

    $this->addDatabaseLogCommandHandler = new AddDatabaseLogCommandHandler(
      $this->loggerChannelFactory
    );
  }
}

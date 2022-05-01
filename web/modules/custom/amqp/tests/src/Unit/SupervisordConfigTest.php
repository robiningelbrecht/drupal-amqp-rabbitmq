<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\FilePutContentsWrapper;
use Drupal\amqp\Queue\QueueFactory;
use Drupal\amqp\SupervisordConfig;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\Snapshots\MatchesSnapshots;

class SupervisordConfigTest extends UnitTestCase
{
  use MatchesSnapshots;

  private SupervisordConfig $supervisordConfig;
  private MockObject $queueFactory;
  private MockObject $filePutContentsWrapper;

  public function testWriteAll(): void
  {

    $this->queueFactory
      ->expects($this->once())
      ->method('getQueues')
      ->willReturn([new TestQueue()]);

    $this->filePutContentsWrapper
      ->expects($this->once())
      ->method('filePutContents')
      ->willReturnCallback(function (string $fileName, string $contents) {
        $this->assertEquals('/app/web/../supervisord/queues/test-queue.conf', $fileName);
        $this->assertMatchesTextSnapshot($contents);
      });

    $this->supervisordConfig->writeAll();
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->queueFactory = $this->createMock(QueueFactory::class);
    $this->filePutContentsWrapper = $this->createMock(FilePutContentsWrapper::class);

    $this->supervisordConfig = new SupervisordConfig(
      $this->queueFactory,
      $this->filePutContentsWrapper,
    );
  }
}

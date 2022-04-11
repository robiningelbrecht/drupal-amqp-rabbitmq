<?php

namespace Drupal\Tests\cqrs\Unit\CommandQueue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\cqrs\CommandQueue\FailedCommandQueue;
use Drupal\cqrs\CommandQueue\FailedCommandQueueFactory;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FailedCommandQueueFactoryTest extends UnitTestCase
{
  private FailedCommandQueueFactory $failedCommandQueueFactory;
  private MockObject $AMQPChannelFactory;

  public function testBuildFor(): void
  {
    $queue = new TestQueue();
    $expectedFailedCommandQueue = new FailedCommandQueue($queue, $this->AMQPChannelFactory);

    $this->assertEquals(
      $expectedFailedCommandQueue,
      $this->failedCommandQueueFactory->buildFor($queue)
    );
  }

  protected function setUp()
  {
    parent::setUp();

    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);

    $this->failedCommandQueueFactory = new FailedCommandQueueFactory(
      $this->AMQPChannelFactory
    );
  }
}

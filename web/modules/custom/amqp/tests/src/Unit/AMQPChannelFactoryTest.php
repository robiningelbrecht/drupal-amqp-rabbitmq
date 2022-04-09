<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPStreamConnectionFactory;
use Drupal\Tests\UnitTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPUnit\Framework\MockObject\MockObject;

class AMQPChannelFactoryTest extends UnitTestCase
{
  private AMQPChannelFactory $AMQPChannelFactory;
  private MockObject $AMQPStreamConnectionFactory;

  public function testGetForQueue(): void
  {
    $queue = new TestQueue();
    $connection = $this->createMock(AMQPStreamConnection::class);
    $this->AMQPStreamConnectionFactory
      ->expects($this->once())
      ->method('get')
      ->willReturn($connection);

    $channel = $this->createMock(AMQPChannel::class);
    $connection
      ->expects($this->once())
      ->method('channel')
      ->willReturn($channel);

    $channel
      ->expects($this->once())
      ->method('queue_declare')
      ->with($queue->getName(), false, true, false, false);

    $channel
      ->expects($this->once())
      ->method('basic_qos')
      ->with(null, 1, null);

    $this->assertEquals($channel, $this->AMQPChannelFactory->getForQueue($queue));
    // Call again to verify static cache.
    $this->AMQPChannelFactory->getForQueue($queue);
  }

  protected function setUp()
  {
    parent::setUp();

    $this->AMQPStreamConnectionFactory = $this->createMock(AMQPStreamConnectionFactory::class);
    $this->AMQPChannelFactory = new AMQPChannelFactory($this->AMQPStreamConnectionFactory);
  }
}

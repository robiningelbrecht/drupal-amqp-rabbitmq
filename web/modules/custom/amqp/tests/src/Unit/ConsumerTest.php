<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\AMQPStreamConnectionFactory;
use Drupal\amqp\ConsoleLogger;
use Drupal\amqp\Consumer;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\WorkerMaxLifeTimeOrIterationsExceeded;
use Drupal\Tests\UnitTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;
use Spatie\Snapshots\MatchesSnapshots;

class ConsumerTest extends UnitTestCase
{
  use MatchesSnapshots;

  private Consumer $consumer;
  private MockObject $AMQPStreamConnectionFactory;
  private MockObject $AMQPChannelFactory;
  private MockObject $logger;

  public function testConsume(): void
  {
    $queue = $this->createMock(Queue::class);

    $queue
      ->expects($this->once())
      ->method('getWorker')
      ->willReturn(new TestWorker(new \DateTimeImmutable('2022-04-10 01:11:14')));

    $this->logger
      ->expects($this->exactly(5))
      ->method('debug')
      ->willReturnCallback(function (string $message) {
        $this->assertMatchesTextSnapshot($message);
      });

    $channel = $this->createMock(AMQPChannel::class);
    $this->AMQPChannelFactory
      ->expects($this->once())
      ->method('getForQueue')
      ->with($queue)
      ->willReturn($channel);

    $message = new AMQPMessage(
      'message',
      ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
    );
    $message->setChannel($channel);
    $message->setDeliveryInfo('tag', false, null, null);

    $channel
      ->expects($this->once())
      ->method('basic_consume')
      ->with($queue->getName(), '', false, false, false, false)
      ->willReturnCallback(function () use ($message, &$callbackCalled) {
        self::assertEquals('message', $message->getBody());
        $callbackCalled = true;
      });

    $channel
      ->expects($this->atLeastOnce())
      ->method('is_open')
      ->willReturn(false);

    $this->logger
      ->expects($this->never())
      ->method('warning');

    $channel
      ->expects($this->never())
      ->method('close');

    $this->AMQPStreamConnectionFactory
      ->expects($this->never())
      ->method('get');

    $this->consumer->consume($queue);
    $this->assertTrue($callbackCalled);
  }

  public function testConsumeOnWorkerMaxLifeTimeOrIterationsExceeded(): void
  {
    $queue = $this->createMock(Queue::class);

    $queue
      ->expects($this->once())
      ->method('getWorker')
      ->willReturn(new TestWorker(new \DateTimeImmutable()));

    $this->logger
      ->expects($this->exactly(5))
      ->method('debug');

    $channel = $this->createMock(AMQPChannel::class);
    $this->AMQPChannelFactory
      ->expects($this->once())
      ->method('getForQueue')
      ->with($queue)
      ->willReturn($channel);

    $message = new AMQPMessage(
      'message',
      ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
    );
    $message->setChannel($channel);
    $message->setDeliveryInfo('tag', false, null, null);

    $channel
      ->expects($this->once())
      ->method('basic_consume')
      ->with($queue->getName(), '', false, false, false, false)
      ->willReturn($this->returnCallback(function () use ($message, &$callbackCalled) {
        self::assertEquals('message', $message->getBody());
        $callbackCalled = true;
        throw  new WorkerMaxLifeTimeOrIterationsExceeded();
      }));

    $channel
      ->expects($this->never())
      ->method('is_open');

    $this->logger
      ->expects($this->once())
      ->method('warning')
      ->with('Closing connection...');

    $channel
      ->expects($this->once())
      ->method('close');

    $this->AMQPStreamConnectionFactory
      ->expects($this->once())
      ->method('get');

    $this->consumer->consume($queue);
    $this->assertTrue($callbackCalled);
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->AMQPStreamConnectionFactory = $this->createMock(AMQPStreamConnectionFactory::class);
    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);
    $this->logger = $this->createMock(ConsoleLogger::class);

    $this->consumer = new Consumer(
      $this->AMQPStreamConnectionFactory,
      $this->AMQPChannelFactory,
      $this->logger
    );
  }
}

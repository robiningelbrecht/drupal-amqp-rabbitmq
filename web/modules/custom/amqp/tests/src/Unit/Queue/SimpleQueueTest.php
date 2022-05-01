<?php

namespace Drupal\Tests\amqp\Unit\Queue;

use Drupal\amqp\AMQPChannelFactory;
use Drupal\amqp\Envelope\AMQPEnvelope;
use Drupal\amqp\Queue\SimpleQueue;
use Drupal\amqp\Worker\SimpleQueueWorker;
use Drupal\Tests\UnitTestCase;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\MockObject\MockObject;

class SimpleQueueTest extends UnitTestCase
{
  private SimpleQueue $simpleQueue;
  private MockObject $simpleQueueWorker;
  private MockObject $AMQPChannelFactory;

  public function testGetters(): void
  {
    $this->assertEquals('simple-queue', $this->simpleQueue->getName());
    $this->assertEquals($this->simpleQueueWorker, $this->simpleQueue->getWorker());
    $this->assertEquals(1, $this->simpleQueue->getNumberOfConsumers());
  }

  public function testQueue(): void
  {
    $envelope = AMQPEnvelope::fromContentAndDate('some-content', new \DateTimeImmutable('2022-02-10'));

    $channel = $this->createMock(AMQPChannel::class);
    $this->AMQPChannelFactory
      ->expects($this->once())
      ->method('getForQueue')
      ->with($this->simpleQueue, null)
      ->willReturn($channel);

    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($envelope), $properties);

    $channel
      ->expects($this->once())
      ->method('basic_publish')
      ->with($message, null, $this->simpleQueue->getName());

    $this->simpleQueue->queue($envelope);
  }

  public function testQueueBatch(): void
  {
    $envelope = AMQPEnvelope::fromContentAndDate('some-content', new \DateTimeImmutable('2022-02-10'));

    $channel = $this->createMock(AMQPChannel::class);
    $this->AMQPChannelFactory
      ->expects($this->once())
      ->method('getForQueue')
      ->with($this->simpleQueue, null)
      ->willReturn($channel);

    $properties = ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT];
    $message = new AMQPMessage(serialize($envelope), $properties);

    $channel
      ->expects($this->exactly(2))
      ->method('batch_basic_publish')
      ->with($message, null, $this->simpleQueue->getName());

    $channel
      ->expects($this->once())
      ->method('publish_batch');

    $this->simpleQueue->queueBatch([$envelope, $envelope]);
  }

  public function testQueueBatchWhenEmpty(): void
  {
    $channel = $this->createMock(AMQPChannel::class);
    $this->AMQPChannelFactory
      ->expects($this->never())
      ->method('getForQueue');

    $channel
      ->expects($this->never())
      ->method('batch_basic_publish');

    $channel
      ->expects($this->never())
      ->method('publish_batch');

    $this->simpleQueue->queueBatch([]);
  }

  public function testQueueBatchItShouldThrowWhenInvalidEnvelope(): void
  {
    $channel = $this->createMock(AMQPChannel::class);
    $this->AMQPChannelFactory
      ->expects($this->never())
      ->method('getForQueue');

    $channel
      ->expects($this->never())
      ->method('batch_basic_publish');

    $channel
      ->expects($this->never())
      ->method('publish_batch');

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('All envelopes need to implement Drupal\amqp\Envelope\Envelope');

    $this->simpleQueue->queueBatch(['test']);
  }

  protected function setUp(): void
  {
    parent::setUp();

    $this->simpleQueueWorker = $this->createMock(SimpleQueueWorker::class);
    $this->AMQPChannelFactory = $this->createMock(AMQPChannelFactory::class);

    $this->simpleQueue = new SimpleQueue(
      $this->simpleQueueWorker,
      $this->AMQPChannelFactory,
    );
  }
}

<?php

namespace Drupal\Tests\amqp\Unit\Queue;

use Drupal\amqp\Queue\QueueFactory;
use Drupal\Tests\amqp\Unit\TestQueue;
use Drupal\Tests\UnitTestCase;

class QueueFactoryTest extends UnitTestCase
{
  private QueueFactory $queueFactory;

  public function testRegisterQueue(): void
  {
    $this->queueFactory->registerQueue(new TestQueue());
    $this->assertEquals(new TestQueue(), $this->queueFactory->getQueue('test-queue'));
  }

  public function testRegisterQueueItShouldThrow(): void
  {
    $this->queueFactory->registerQueue(new TestQueue());

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Queue "non-existing-queue" not registered in factory');

    $this->queueFactory->getQueue('non-existing-queue');
  }

  protected function setUp()
  {
    parent::setUp();

    $this->queueFactory = new QueueFactory();
  }
}

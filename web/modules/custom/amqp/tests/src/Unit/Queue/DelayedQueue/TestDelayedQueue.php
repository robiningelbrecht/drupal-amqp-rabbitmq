<?php

namespace Drupal\Tests\amqp\Unit\Queue\DelayedQueue;

use Drupal\amqp\Queue\DelayedQueue\SupportsDelay;
use Drupal\Tests\amqp\Unit\TestQueue;

class TestDelayedQueue extends TestQueue implements SupportsDelay
{

}

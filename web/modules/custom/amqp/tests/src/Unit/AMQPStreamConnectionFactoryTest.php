<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\AMQPStreamConnectionFactory;
use Drupal\Core\Site\Settings;
use Drupal\Tests\UnitTestCase;
use PhpAmqpLib\Exception\AMQPIOException;

class AMQPStreamConnectionFactoryTest extends UnitTestCase
{
  public function testGetItShouldThrow(): void
  {
    Settings::initialize(dirname(__DIR__), 'Unit', $classLoader);
    $AMQPStreamConnectionFactory = new AMQPStreamConnectionFactory(Settings::getInstance());

    $this->expectException(AMQPIOException::class);
    $this->expectExceptionMessage('stream_socket_client(): Unable to connect to tcp://some-host:port (php_network_getaddresses: getaddrinfo for some-host failed: Name or service not known)');

    $AMQPStreamConnectionFactory->get();
  }
}

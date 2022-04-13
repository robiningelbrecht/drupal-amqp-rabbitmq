<?php

namespace Drupal\amqp;

use Drupal\Core\Site\Settings;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AMQPStreamConnectionFactory
{
  public const CREDENTIALS = 'amqp_credentials';

  private ?AMQPStreamConnection $AMQPStreamConnection = null;

  public function __construct(
    private Settings $settings
  )
  {
    if (!class_exists('\PhpAmqpLib\Connection\AMQPStreamConnection')) {
      throw new \RuntimeException('Could not find php-amqplib. Install it with composer.');
    }
  }

  public function get(): AMQPStreamConnection
  {
    if (null === $this->AMQPStreamConnection) {
      $credentials = $this->settings->get(self::CREDENTIALS, []);

      $this->AMQPStreamConnection = new AMQPStreamConnection(
        $credentials['host'],
        $credentials['port'],
        $credentials['username'],
        $credentials['password'],
        $credentials['vhost'],
      );
    }

    return $this->AMQPStreamConnection;
  }
}

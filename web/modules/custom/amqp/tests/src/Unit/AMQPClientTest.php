<?php

namespace Drupal\Tests\amqp\Unit;

use Drupal\amqp\AMQPClient;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Site\Settings;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\MockObject\MockObject;

class AMQPClientTest extends UnitTestCase
{
  private AMQPClient $AMQPClient;
  private Settings $settings;
  private MockObject $clientFactory;
  private MockObject $client;

  public function testGetExchangeBindings(): void
  {
    $this->client
      ->expects($this->once())
      ->method('request')
      ->with(
        'GET',
        '/api/exchanges/%2F/dlx/bindings/source'
      )
      ->willReturn(new Response(200, [], '[]'));

    $this->AMQPClient->getExchangeBindings('dlx');
  }

  protected function setUp(): void
  {
    parent::setUp();
    Settings::initialize(dirname(__DIR__), 'Unit', $classLoader);

    $this->settings = Settings::getInstance();
    $this->clientFactory = $this->createMock(ClientFactory::class);
    $this->client = $this->createMock(Client::class);

    $this->clientFactory
      ->expects($this->once())
      ->method('fromOptions')
      ->with([
        'base_uri' => 'http://rabbit.lndo.site/',
        RequestOptions::AUTH => [
          'guest',
          'guest',
        ],
      ])
      ->willReturn($this->client);

    $this->AMQPClient = new AMQPClient(
      $this->settings,
      $this->clientFactory
    );
  }
}

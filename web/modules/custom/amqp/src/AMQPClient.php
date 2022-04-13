<?php

namespace Drupal\amqp;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Site\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class AMQPClient
{

  private Client $client;
  private array $credentials;

  public function __construct(
    private Settings $settings,
    private ClientFactory $clientFactory
  )
  {
    $this->credentials = $this->settings->get(AMQPStreamConnectionFactory::CREDENTIALS, []);

    $this->client = $this->clientFactory->fromOptions([
      'base_uri' => $this->credentials['api'],
      RequestOptions::AUTH => [
        $this->credentials['username'],
        $this->credentials['password'],
      ],
    ]);
  }

  public function getExchangeBindings(string $name): array
  {
    $response = $this->client->request(
      'GET',
      sprintf('/api/exchanges/%s/%s/bindings/source', urlencode($this->credentials['vhost']), $name),
    );

    return Json::decode($response->getBody()->getContents());
  }
}

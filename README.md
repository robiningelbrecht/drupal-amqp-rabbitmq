<h1 align="center">Drupal AMQP example</h1>

<p align="center">
	<img src="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/raw/master/readme/rabbitmq.png" alt="RabbitMQ">
</p>

<p align="center">
<a href="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/actions/workflows/ci.yml"><img src="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/actions/workflows/ci.yml/badge.svg" alt="CI/CD"></a>
<a href="https://codecov.io/gh/robiningelbrecht/drupal-amqp-rabbitmq"><img src="https://codecov.io/gh/robiningelbrecht/drupal-amqp-rabbitmq/branch/master/graph/badge.svg?token=QUZxuZ49V4" alt="codecov.io"></a>
<a href="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/blob/master/LICENSE"><img src="https://img.shields.io/github/license/robiningelbrecht/continuous-integration-example?color=428f7e&logo=open%20source%20initiative&logoColor=white" alt="License"></a>
<a href="https://php.net/"><img src="https://img.shields.io/packagist/php-v/robiningelbrecht/drupal-amqp-rabbitmq/dev-master?color=777bb3&logo=php&logoColor=white" alt="PHP"></a>
</p>

------

This repository aims to illustrate how to setup AMQP within Drupal. It contains a base structure with some working examples that use CommandHandlers to handle AMQP messages.

## Installation

* Start by installing [Docker](https://docs.docker.com/get-docker/) and [Lando](https://docs.lando.dev/getting-started/)
* Clone this repository `git clone git@github.com:robiningelbrecht/drupal-amqp-rabbitmq.git`
* Run `lando start` to build the necessary docker containers
* Run `lando composer install` to download vendor dependencies
* Make sure following config is added to `settings.php`

```php
$databases['default']['default'] = [
  'database' => 'drupal9',
  'username' => 'drupal9',
  'password' => 'drupal9',
  'prefix' => '',
  'host' => 'database',
  'port' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
];
$settings['config_sync_directory'] = '../config/sync';

$settings['amqp_credentials'] = [
  'host' => '172.21.0.3', // The AMQP host IP address is outputted n your CLI while running `lando start`
  'port' => '5672',
  'username' => 'guest',
  'password' => 'guest',
  'vhost' => '/',
  'api' => 'http://rabbit.lndo.site/',
];
```

* Import the database dump by running `lando drush sql-cli < init.sql`

## The basic idea and setup

There are basically 3 important terms to keep in mind:

* **Worker**: A specific class that processes a message, also handles failures in case a message could not be processed
* **Queue**: A class that represents a RabbitMQ queue, allows for messages to be pushed to the corresponding queue. Each queue is linked to a worker
* **Consumer**: Process that consumes a specific queue and it's messages, each queue can have zero or more consumers

The `amqp` module provides a basic framework that allows you to 

* Define queues and workers
* Consume queues with a *drush* command
* Push messages to queues with a *drush* command

<img src="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/raw/master/readme/rmq-drupal.svg" alt="RabbitMQ">

## Pushing messages and consuming them

The `amqp` module contains a `SimpleQueue` and a `SimpleQueueWorker`. Let's take a look
at an example of pushing and consuming messages:

<img src="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/raw/master/readme/consume-push-example.gif" alt="Consume - Push example">

## Adding a new queue

```yaml
  Drupal\your_module\Queue\NewQueue:
    autowire: true
    tags:
      - { name: amqp_queue }
```

### Use a delayed Q to postpone consuming a message:

```php
  $this->delayedQueueFactory->buildWithDelayForQueue(10, $queue)->queue($message);
```

### Push a message to it's corresponding failed Q:

```php
  $this->failedQueueFactory->buildFor($queue)->queue(message);
```

## Define a new CommandHandler:


```yaml
  Drupal\your_module\DoSomething\DoSomethingCommandHandler:
    autowire: true
    tags:
      - { name: cqrs_command_handler }
```

## Real-time migration example

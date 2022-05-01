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
  'host' => '172.21.0.3', // The AMQP host IP address is outputted in your CLI while running `lando start`
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

It's recommended to add a queue for each type of task, for example:

* Sending out notifications: `send-notification-queue`
* Migrating articles: `migrate-article-queue`
* Calculate product prices: `calculate-product-price-queue`
* ...

This approach ensures that tasks of one type cannot block other ones. It also has the advantage
that you can log failed messages on the corresponding failed queues of each queue:

* `send-notification-queue-failed`
* `migrate-article-queue-failed`
* `calculate-product-price-queue-failed`


To declare a new queue, just add a new entry to your `services.yml` and tag it with `ampq_queue`:

```yaml
  Drupal\your_module\Queue\NewQueue:
    autowire: true
    tags:
      - { name: amqp_queue }
```

Make sure this class extends `BaseQueue`, so you don't have to bother queueing messages yourself.

### Push a message to it's corresponding failed Q

If, fore some reason, a message could not be processed, you might want to log it somewhere.
A "failed queue" could be a solution here.\
To push a message to it's corresponding failed queue, you can use the `FailedQueueFactory`:

```php
  $this->failedQueueFactory->buildFor($queue)->queue(message);
```

This factory can for example be used in the `processFailure` callback of your worker:

```php
  public function processFailure(Envelope $envelope, AMQPMessage $message, \Throwable $exception, Queue $queue): void
  {
    /** @var Command $command */
    $command = $envelope;
    $command->setMetaData([
      'exceptionMessage' => $exception->getMessage(),
      'traceAsString' => $exception->getTraceAsString(),
    ]);

    $failedQueue = $this->failedQueueFactory->buildFor($queue)->queue($command);
  }
```

**note**: a failed queue has no worker attached to it, and thus, cannot be consumed. This means
that the messages will stay on the queue until they are manually deletd.

### Use a delayed Q to postpone consuming a message

In some more advanced use cases you might want to delay the consumption of messsages, for example:

* a digist mail that summarizes all content changes occured in the last 30 minutes
* requeue a failed message automatically after 15 seconds
* ...

You can achieve this by pushing the message to it's correspondng delayed queue:

```php
  $this->delayedQueueFactory->buildWithDelayForQueue(15, $queue)->queue($message);
```

For a delayed queue to work properly you'll have to do two things:

* Add a new exchange with the name `dlx`
* Make sure the queue is defined as a binding on the `dlx` exchange, where the
routing key of the binding is the command queue name to where it has to be routed.

<p align="center">
	<img src="https://github.com/robiningelbrecht/drupal-amqp-rabbitmq/raw/master/readme/dlx-binding-example.png" width="400" alt="Dlx binding example">
</p>

## Define a new CommandHandler

I like to use Commands and CommandHandlers to persist changes to the database. That is basically what
the `cqrs` module allows is for. It provides a simple framework that

* Allows you to define new commands and their corresponding command handlers
* Allows you to push messages to command queues
* Provides a command worker and dispatcher to process the commands comming in from the different queues

To add a new command (and command handler), just add a new entry to your `services.yml`
and tag it with `cqrs_command_handler`:

```yaml
  Drupal\your_module\DoSomething\DoSomethingCommandHandler:
    autowire: true
    tags:
      - { name: cqrs_command_handler }
```

## Real-time migration example

## Run a consumer as a background process

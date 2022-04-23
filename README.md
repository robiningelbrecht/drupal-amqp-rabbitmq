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

@TODO: Explain consumer vs queue vs worker

Define a new Q:

```yaml
  Drupal\your_module\Queue\NewQueue:
    autowire: true
    tags:
      - { name: amqp_queue }
```

Define a new Command with CommandHandler:


```yaml
  Drupal\your_module\DoSomething\DoSomethingCommandHandler:
    autowire: true
    tags:
      - { name: cqrs_command_handler }
```

Use a delayed Q to postpone consuming a message:

```php
  $this->delayedQueueFactory->buildWithDelayForQueue(10, $queue)->queue($message);
```

Push a message to it's corresponding failed Q:

```php
  $this->failedQueueFactory->buildFor($queue)->queue(message);
```

<?php

namespace Drupal\amqp;

use Drupal\amqp\Envelope\Envelope;
use Drupal\amqp\Queue\Queue;
use Drupal\amqp\Worker\Worker;
use Drupal\amqp\Worker\WorkerMaxLifeTimeOrIterationsExceeded;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{

  private ?AMQPChannel $channel = null;

  public function __construct(
    private AMQPStreamConnectionFactory $AMQPStreamConnectionFactory,
    private AMQPChannelFactory $AMQPChannelFactory,
    private ConsoleLogger $logger,
  )
  {

  }

  public function __destruct()
  {
    $this->channel?->close();
  }

  public function consume(Queue $queue)
  {
    $this->logStartupInfo($queue);
    $channel = $this->AMQPChannelFactory->getForQueue($queue);
    $logger = $this->logger;

    $callback = static function (AMQPMessage $message) use ($queue, $logger) {
      Consumer::consumeCallback($message, $queue, $logger);
    };

    try {
      $channel->basic_consume($queue->getName(), '', false, false, false, false, $callback);

      while ($channel->is_open()) {
        $channel->wait();
      }
    } catch (WorkerMaxLifeTimeOrIterationsExceeded) {
      $this->logger->warning('Closing connection...');
      $channel->close();
      $this->AMQPStreamConnectionFactory->get()->close();
    }
  }

  public static function consumeCallback(
    AMQPMessage $message,
    Queue $queue,
    ConsoleLogger $logger): void
  {
    $worker = $queue->getWorker();
    $envelope = unserialize($message->getBody());

    try {
      if ($worker->maxLifeTimeReached() || $worker->maxIterationsReached()) {
        throw new WorkerMaxLifeTimeOrIterationsExceeded();
      }

      $logger->debug(sprintf('Worker "%s" started processing message %s', $worker->getName(), $message->getDeliveryTag()));
      $worker->processMessage($envelope, $message);
      $message->getChannel()->basic_ack($message->getDeliveryTag());
    } catch (WorkerMaxLifeTimeOrIterationsExceeded $exception) {
      // Requeue message to make sure next consumer can process it.
      $logger->warning('Worker max life time or iterations exceeded. Re-queueing message for next consumer.');
      $message->getChannel()->basic_nack($message->getDeliveryTag(), false, true);
      throw $exception;
    } catch (\Throwable $exception) {
      $logger->error(sprintf('Worker "%s" could not process message %s', $worker->getName(), $message->getDeliveryTag()));
      $worker->processFailure($envelope, $message, $exception, $queue);
      // Ack the message to unblock queue. Worker should handle failed messages.
      $message->getChannel()->basic_ack($message->getDeliveryTag());
    }
  }

  private function logStartupInfo(Queue $queue): void
  {
    $worker = $queue->getWorker();

    $this->logger->debug('Waiting for messages, to exit press CTRL+C');
    $this->logger->debug(sprintf(
      'Worker "%s" for queue "%s" ready to receive up to:',
      $worker->getName(),
      $queue->getName(),
    ));
    $this->logger->debug(sprintf(
      '  ??? %s messages',
      $worker->getMaxIterations(),
    ));
    $this->logger->debug(sprintf(
      '  ??? until %s (max life time is %s)',
      $worker->getMaxLifeTime()->format('Y-m-d H:i:s'),
      $worker->getMaxLifeTimeInterval()->format('%h hour(s), %i minutes')
    ));
    $this->logger->debug('=================================================================================');
  }

}

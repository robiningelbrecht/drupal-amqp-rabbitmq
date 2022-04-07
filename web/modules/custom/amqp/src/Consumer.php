<?php

namespace Drupal\amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{

  private ?AMQPChannel $channel = null;

  public function __construct(
    private AMQPStreamConnection $connection,
    private AMQPChannelFactory $AMQPChannelFactory,
  )
  {

  }

  public function __destruct()
  {
    $this->channel?->close();
  }

  public function consume(Queue $queue)
  {
    $channel = $this->AMQPChannelFactory->getForQueue($queue);
    $this->log('Waiting for messages. To exit press CTRL+C');

    $worker = $queue->getWorker();
    $this->log(sprintf(
      'Worker ready to receive up to %s messages until %s.',
      $worker->getMaxIterations(),
      $worker->getMaxLifeTime()->format('Y-m-d H:i:s'),
    ));

    $callback = static function (AMQPMessage $message) use ($worker) {
      try {
        if ($worker->maxLifeTimeReached() || $worker->maxIterationsReached()) {
          throw new WorkerMaxLifeTimeOrIterationsExceeded();
        }

        $this->log(sprintf('Worker %s processing message %s', $worker::class, $message->getDeliveryTag()));
        $worker->processMessage($message);
        $message->getChannel()->basic_ack($message->getDeliveryTag());
      } catch (WorkerMaxLifeTimeOrIterationsExceeded $e) {
        // Requeue message to make sure next consumer can process it.
        $message->getChannel()->basic_nack($message->getDeliveryTag(), false, true);
        throw $e;
      } catch (\Exception|\Error $exception) {
        $this->error(sprintf('Worker %s could not process message: %s', $worker::class, $message->getDeliveryTag()));
        $worker->processFailure($message, $exception);
      }
    };

    try {
      $channel->basic_consume($queue->getName(), '', false, false, false, false, $callback);

      while ($channel->is_open()) {
        $channel->wait();
      }
    } catch (WorkerMaxLifeTimeOrIterationsExceeded) {
      $this->log('Worker max life time or iterations exceeded. Closing connection.');
      $channel->close();
      $this->connection->close();
    }
  }

  public function log(string $message): void
  {
    echo ' [*] ' . $message . PHP_EOL;
  }

  public function error(string $message): void
  {
    echo ' [X] ERROR: ' . $message . PHP_EOL;
  }
}

<?php

namespace Drupal\amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Consumer
{

  private ?AMQPChannel $channel = null;

  public function __construct(
    private AMQPStreamConnectionFactory $AMQPStreamConnectionFactory,
    private AMQPChannelFactory $AMQPChannelFactory,
    private ConsoleLog $consoleLog,
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
    $this->consoleLog->debug('Waiting for messages. To exit press CTRL+C');

    $worker = $queue->getWorker();
    $this->consoleLog->debug(sprintf(
      'Worker ready to receive up to %s messages until %s.',
      $worker->getMaxIterations(),
      $worker->getMaxLifeTime()->format('Y-m-d H:i:s'),
    ));

    $callback = static function (AMQPMessage $message) use ($worker) {
      $consoleLog = new ConsoleLog();
      try {
        if ($worker->maxLifeTimeReached() || $worker->maxIterationsReached()) {
          throw new WorkerMaxLifeTimeOrIterationsExceeded();
        }

        $consoleLog->success(sprintf('Worker %s processing message %s', $worker::class, $message->getDeliveryTag()));
        $worker->processMessage($message);
        $message->getChannel()->basic_ack($message->getDeliveryTag());
      } catch (WorkerMaxLifeTimeOrIterationsExceeded $e) {
        // Requeue message to make sure next consumer can process it.
        $this->consoleLog->warning('Worker max life time or iterations exceeded. Re-queueing message for next consumer.');
        $message->getChannel()->basic_nack($message->getDeliveryTag(), false, true);
        throw $e;
      } catch (\Exception|\Error $exception) {
        $consoleLog->error(sprintf('Worker %s could not process message: %s', $worker::class, $message->getDeliveryTag()));
        $worker->processFailure($message, $exception);
      }
    };

    try {
      $channel->basic_consume($queue->getName(), '', false, false, false, false, $callback);

      while ($channel->is_open()) {
        $channel->wait();
      }
    } catch (WorkerMaxLifeTimeOrIterationsExceeded) {
      $this->consoleLog->warning('Worker max life time or iterations exceeded. Closing connection.');
      $channel->close();
      $this->AMQPStreamConnectionFactory->get()->close();
    }
  }
}

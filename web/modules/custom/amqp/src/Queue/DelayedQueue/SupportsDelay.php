<?php

namespace Drupal\amqp\Queue\DelayedQueue;

interface SupportsDelay
{
  // Make sure that every Q that implements this interface, is defined as a binding on the DLX exchange.
  // Routing key of the binding has to be the command queue name to where it has to be routed.
}

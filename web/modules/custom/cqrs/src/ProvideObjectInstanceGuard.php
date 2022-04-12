<?php

namespace Drupal\cqrs;

trait ProvideObjectInstanceGuard
{
  public function guardThatObjectIsInstanceOf(mixed $object, string $fqcn): void
  {
    if (!$object instanceof $fqcn) {
      throw new \RuntimeException(sprintf('Expected object to be of type %s, got %s', $fqcn, get_class($object)));
    }
  }
}

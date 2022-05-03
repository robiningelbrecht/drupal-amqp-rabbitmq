<?php

namespace Drupal\examples\MigrateBreakingNewsArticle;

use Drupal\cqrs\Command;

class MigrateBreakingNewsArticle extends Command
{
  public function __construct(
    private array $jsonData,
    \DateTimeImmutable $stampTime
  )
  {
    parent::__construct($stampTime);
  }

  public function getJsonData(): array
  {
    return $this->jsonData;
  }
}

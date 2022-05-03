<?php

namespace Drupal\examples\MigrateBreakingNewsArticle;

use Drupal\amqp\ConsoleLogger;
use Drupal\cqrs\Command;
use Drupal\cqrs\CommandHandler;
use Drupal\cqrs\ProvideObjectInstanceGuard;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\migrate_tools\MigrateExecutable;

class MigrateBreakingNewsArticleCommandHandler implements CommandHandler
{
  use ProvideObjectInstanceGuard;

  public function __construct(
    private readonly ConsoleLogger $logger,
    private MigrationPluginManager $migrationPluginManager
  )
  {
  }

  public function handle(Command $command)
  {
    /** @var MigrateBreakingNewsArticle $command */
    $this->guardThatObjectIsInstanceOf($command, MigrateBreakingNewsArticle::class);

    /** @var MigrationInterface $migration */
    $pluginDefinition = $this->migrationPluginManager->createInstance('breaking_news_node')->getPluginDefinition();
    $pluginDefinition['source']['data_rows'] = [$command->getJsonData()];
    $migration = $this->migrationPluginManager->createStubMigration($pluginDefinition);

    $migration->getIdMap()->prepareUpdate();
    $migration->setStatus(MigrationInterface::STATUS_IDLE);

    $executable = new MigrateExecutable($migration, new MigrateMessage());
    $executable->import();

    $this->logger->success(sprintf(
      'Migrated breaking news article with title "%s"',
      $command->getJsonData()['title'],
    ));
  }

}

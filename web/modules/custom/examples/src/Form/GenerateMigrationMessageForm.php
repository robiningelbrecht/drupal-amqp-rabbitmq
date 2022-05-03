<?php

namespace Drupal\examples\Form;

use Drupal\amqp\Queue\QueueFactory;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\examples\MigrateBreakingNewsArticle\MigrateBreakingNewsArticle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateMigrationMessageForm extends FormBase
{

  public function __construct(
    private QueueFactory $queueFactory
  )
  {

  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('Drupal\amqp\Queue\QueueFactory')
    );
  }

  public function getFormId(): string
  {
    return 'generate-migration-message-form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array
  {
    try {
      $queue = $this->queueFactory->getQueue('general-command-queue');
    } catch (\Exception $e) {
      $this->messenger()->addError($e->getMessage());
      return [];
    }

    $form['admin_label'] = [
      '#type' => 'item',
      '#plain_text' => 'This form will push a migration message to the general command Q
      and will create/update nodes of type "Breaking news". It serves as an example how a real time migration could work.',
    ];

    $form['external_id'] = [
      '#type' => 'textfield',
      '#title' => 'External ID',
      '#required' => true,
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Title',
      '#required' => true,
    ];

    $form['body'] = [
      '#type' => 'text_format',
      '#title' => 'Body',
      '#default_value' => '',
      '#format' => 'full_html',
      '#required' => true,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#button_type' => 'primary',
        '#value' => 'Push message to Q',
      ],
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void
  {
    $queue = $this->queueFactory->getQueue('general-command-queue');

    $queue->queue(new MigrateBreakingNewsArticle(
      [
        'external_id' => $form_state->getValue('external_id'),
        'title' => $form_state->getValue('title'),
        'body' => $form_state->getValue('body'),
      ],
      new \DateTimeImmutable('now')
    ));

    $this->messenger()->addMessage(Markup::create(sprintf(
      'Message has been pushed to the <a href="%s" target="_blank">general command queue</a>', 'http://rabbit.lndo.site/#/queues/%2F/general-command-queue'
    )));
  }

}

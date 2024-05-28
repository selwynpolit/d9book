<?php

declare(strict_types = 1);

namespace Drupal\derivative_examples\Plugin\QueueWorker;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines 'derivative_examples_queue_worker' queue worker.
 *
 * @QueueWorker(
 *   id = "derivative_examples_queue_worker",
 *   title = @Translation("Queue Derivative Example"),
 *   cron = {"time" = 10},
 *   deriver = "Drupal\derivative_examples\Plugin\Derivative\DerivativeExamplesQueueDerivative"
 * )
 */
final class DerivativeExamplesQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {
  use StringTranslationTrait;

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected LoggerChannelInterface $logger;

  /**
   * Constructs a new Queue Derivative instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly LoggerChannelFactoryInterface $loggerFactory,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->logger = $loggerFactory->get('booking');

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data): void {

    $id = $this->getDerivativeId();

    $this->logger->info($id . 'Queue Processed.');
    if (!empty($data)) {
      $this->logger->info($this->t('Data available to Process.'));
    }
    else {
      $this->logger->warning($this->t('No Data available to Process.'));
    }

  }

}

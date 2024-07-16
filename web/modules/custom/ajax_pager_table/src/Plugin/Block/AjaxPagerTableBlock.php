<?php
namespace Drupal\ajax_pager_table\Plugin\Block;

use Drupal\ajax_pager_table\Service\TableContentService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an AJAX Pager Table Block.
 *
 * @Block(
 *   id = "ajax_pager_table_block",
 *   admin_label = @Translation("AJAX Pager Table Block"),
 *   category = @Translation("DOH"),
 * )
 */
class AjaxPagerTableBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The table content service.
   *
   * @var \Drupal\ajax_pager_table\Service\TableContentService
   */
  protected $tableContentService;

  /**
   * Constructs a new AjaxPagerTableBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param $plugin_id
   *   The plugin ID for the plugin instance.
   * @param $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ajax_pager_table\Service\TableContentService $tableContentService
   *   The table content service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MessengerInterface $messenger,
    TableContentService $tableContentService,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->tableContentService = $tableContentService;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('messenger'),
      $container->get('ajax_pager_table.table_content_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $page = \Drupal::request()->query->get('page') ?? 0;
    $build = $this->tableContentService->getTableContent($page);
    return $build;
  }

}

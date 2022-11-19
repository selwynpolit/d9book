<?php

namespace Drupal\phil\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenDialogCommand;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\OpenOffCanvasDialogCommand;

/**
 * Returns responses for Phil routes.
 */
class PhilController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a new DialogController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct($entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  public function createDialog() {
    // Load a specific node.
    $node = $this->entityTypeManager->getStorage('node')->load(34);

    // Convert node into a render array.
    $viewBuilder = $this->entityTypeManager->getViewBuilder('node');
    $content = $viewBuilder->view($node, 'teaser');

    // Get the title of the node.
    $title = $node->getTitle();

    // Create the AjaxResponse object.
    $response = new AjaxResponse();

    // Attach the library needed to use the OpenDialogCommand response.
    $attachments['library'][] = 'core/drupal.dialog.ajax';
    $attachments['library'][] = 'core/drupal.dialog.off_canvas';
    $response->setAttachments($attachments);

    // Add the open dialog command to the ajax response.
//    $response->addCommand(new OpenDialogCommand('#my-dialog-selector', $title, $content, ['width' => '70%']));
//    $response->addCommand(new OpenModalDialogCommand($title, $content, ['width' => '70%']));
    $response->addCommand(new OpenOffCanvasDialogCommand($title, $content, ['width' => '70%']));


    return $response;
  }

  public function displayPage() {
    $output = [];

    $output['a_dialog_link'] = [
      '#type' => 'link',
      '#url' => new Url('phil_display_dialog'),
      '#title' => 'node in dialog',
      '#attributes' => [
        'class' => ['use-ajax'],
      ],
    ];

    $output['#attached']['library'][] = 'core/drupal.dialog.ajax';
//    $output['#attached']['library'][] = 'core/drupal.dialog.off_canvas';

    return $output;
  }


  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}

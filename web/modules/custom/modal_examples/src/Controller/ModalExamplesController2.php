<?php

/**
 * @file
 *
 * Contains \Drupal\modal_examples\Controller\ModalExamplesController2.
 */
namespace Drupal\modal_examples\Controller;

use Drupal\Core\Ajax\OpenDialogCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;


/**
 * ModalExamplesController2 class.
 */
class ModalExamplesController2 extends ControllerBase {

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The ModalFormExampleController constructor.
   *
   * @param \Drupal\Core\Form\FormBuilder $formBuilder
   *   The form builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(FormBuilder $formBuilder, EntityTypeManagerInterface $entity_type_manager) {
    $this->formBuilder = $formBuilder;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Callback for opening the modal form.
   */
  public function openModalForm() {
    $response = new AjaxResponse();

    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\modal_examples\Form\ExampleModalForm');

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand('My Modal Form', $modal_form, ['width' => '800']));

    return $response;
  }

  /*
   * Phil's open a dialog
   */
  public function createDialogFromNode() {
    // Load a specific node.
//    $node = Node::load(34);
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
    $response->setAttachments($attachments);

    // Add the open dialog command to the ajax response.
    $response->addCommand(new OpenDialogCommand('#my-dialog-selector', $title, $content, ['width' => '70%']));
    return $response;
  }

}

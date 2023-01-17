<?php

namespace Drupal\modal_examples\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * ExampleForm class.
 */
class ExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $options = NULL) {

    $form['#prefix'] = '<div id="example_form">';
    $form['#suffix'] = '</div>';

    $form['info']['instructions'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Please fill out the form below and click the button for more info.'),
    ];

    $form['info']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#size' => 20,
      '#default_value' => 'Mary Vasquez',
      '#required' => FALSE,
    ];

    $form['actions']['open_modal'] = [
      '#type' => 'link',
      '#title' => $this->t('Click to see the Modal Form'),
      '#url' => Url::fromRoute('modal_examples.modal_form'),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
        ],
      ],
    ];

    //$node = $this->entityTypeManager->getStorage('node')->load(34);
    $node = Node::load(34);
    $url = $node->toUrl()->setAbsolute();

    $form['actions']['open_slide_modal'] = [
      '#type' => 'link',
      '#title' => $this->t('Click to see the Slide-in or off-canvas Modal Form'),
//      '#url' => Url::fromRoute('modal_examples.modal_form'),
      '#url' => $url,
      '#attributes' => [
        'class' => ['use-ajax', 'button',],
//        'id' => 'example-dialog-link-' . '12345',
        'data-dialog-type' => 'dialog',
        'data-dialog-renderer' => 'off_canvas',
        'data-dialog-options' => Json::encode(['width' => '70%',]
        ),
      ],
      '#attached' => [
        'library' => [
          'core/drupal.dialog.ajax',
        ],
      ],
    ];


    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#attributes' => [
      ],
    ];

    // Attach the library for pop-up dialogs/modals.
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['##attached']['library'][] = 'core/drupal.dialog.off_canvas';


    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @TODO.
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'modal_examples_example_form';
  }

}

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
class ExampleForm2 extends FormBase {

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
    return 'modal_examples_example_form2';
  }

}

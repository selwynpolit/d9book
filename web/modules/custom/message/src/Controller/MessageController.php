<?php

namespace Drupal\message\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Message routes.
 */
class MessageController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

//    \Drupal::messenger()->addMessage($message, $type, $repeat);
    \Drupal::messenger()->addMessage("Aren't you special!");
    \Drupal::messenger()->addMessage("And Aren't you special!",'status' );
    \Drupal::messenger()->addMessage("And Aren't you special!",'status', TRUE );
    \Drupal::messenger()->addMessage("And Aren't you special!",'warning' );
    \Drupal::messenger()->addMessage("And Aren't you special!",'error' );

    \Drupal::messenger()->addStatus("migration failed");
    \Drupal::messenger()->addWarning("migration failed");
    \Drupal::messenger()->addError("migration failed");

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}

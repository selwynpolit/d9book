<?php

namespace Drupal\hook_examples\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Hook Examples routes.
 */
class HookExamplesController extends ControllerBase {

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

<?php

namespace Drupal\route_examples\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Route Examples routes.
 */
class RouteExamplesController extends ControllerBase {

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

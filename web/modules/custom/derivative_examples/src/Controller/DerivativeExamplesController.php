<?php

declare(strict_types = 1);

namespace Drupal\derivative_examples\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Derivative Examples routes.
 */
final class DerivativeExamplesController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Products List Controller!'),
    ];

    return $build;
  }

}

<?php

declare(strict_types = 1);

namespace Drupal\derivative_examples\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Derivative Examples Block.
 *
 * @Block(
 *   id = "derivative_examples_block",
 *   admin_label = @Translation("Derivative Examples Block"),
 *   category = @Translation("Derivative Examples"),
 *   module = "derivative_examples",
 *   deriver = "Drupal\derivative_examples\Plugin\Derivative\DerivativeExamplesBlockDerivative"
 * )
 */
final class DerivativeExamplesBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build['content'] = [
      '#markup' => $this->t('Derivative Examples Block!...'),
    ];
    return $build;
  }

}

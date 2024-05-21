<?php

declare(strict_types=1);

namespace Drupal\derivative_examples\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Derivative class that provides the data for Queue plugins.
 */
class DerivativeExamplesQueueDerivative extends DeriverBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {

    // Products can be anything with key, value pair.
    // Here we are defining sample array.
    $products = [
      'cooking_gear' => 'Cooking Gear',
      'tents' => 'Tents',
      'sleeping_bags' => 'Sleeping Bags',
      'rope' => 'Rope',
      'safety' => 'Safety',
      'packs' => 'Packs',
    ];

    if (!empty($products)) {

      foreach ($products as $key => $product) {

        $this->derivatives[$key] = $base_plugin_definition;
        $this->derivatives[$key]['title'] = $this->t('Queue Name : @type', ['@type' => $product]);
      }
    }

    return $this->derivatives;

  }

}

<?php

declare(strict_types=1);

namespace Drupal\derivative_examples\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Derivative class that provides the data for Menu plugins.
 */
class DerivativeExamplesMenuDerivative extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {

    $links = [];

    $products = [
      'cooking_gear' => 'Cooking Gear',
      'tents' => 'Tents',
      'sleeping_bags' => 'Sleeping Bags',
      'rope' => 'Rope',
      'safety' => 'Safety',
      'packs' => 'Packs',
    ];

    foreach ($products as $key => $value) {
      $links['derivative_examples_products_menu_' . $key] = [
        'title' => $value . ' Controller',
        'parent' => 'derivative_examples.base',
        'route_name' => 'derivative_examples.dynamic_routes' . $key,
      ] + $base_plugin_definition;
    }

    return $links;

  }

}

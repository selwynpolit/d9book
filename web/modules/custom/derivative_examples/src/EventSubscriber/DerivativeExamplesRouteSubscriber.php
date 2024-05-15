<?php

declare(strict_types = 1);

namespace Drupal\derivative_examples\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Dynamic Route Subscriber Examples route subscriber.
 */
final class DerivativeExamplesRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {

    $products = [
      'cooking_gear' => 'Cooking Gear',
      'tents' => 'Tents',
      'sleeping_bags' => 'Sleeping Bags',
      'rope' => 'Rope',
      'safety' => 'Safety',
      'packs' => 'Packs',
    ];

    foreach ($products as $key => $value) {

      $url = preg_replace('/_/', '-', $key);

      $route = new Route(
        // The url path to match.
        'derivative-examples/product-details/' . $url,
        [
          '_title' => $value . ' Controller',
          '_controller' => '\Drupal\derivative_examples\Controller\DerivativeExamplesController::build',
          'type' => $value,
        ],
        // The requirements.
        [
          '_permission' => 'administer site configuration',
        ]
      );

      // Add our route to the collection with a unique key.
      $collection->add('derivative_examples.dynamic_routes' . $key, $route);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {

    // Use a lower priority than \Drupal\views\EventSubscriber\RouteSubscriber
    // to ensure the requirement will be added to its routes.
    return [
      RoutingEvents::ALTER => ['onAlterRoutes', -300],
    ];
  }

}

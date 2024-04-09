<?php

declare(strict_types=1);

namespace Drupal\general\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CachePlay1 extends ControllerBase {
  /**
   * Example of a simple controller method that returns a JSON response.
   *
   * Note. You must enable the RESTful Web Services module to run this.
   *
   * @param int $nid
   *  The node ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
    public function jsonExample1(int $nid): JsonResponse {
      if ($nid == 0) {
        //$build['#cache']['tags'][] = 'node_list';
        $data = [
          'nid' => $nid,
          'name' => 'Fred Bloggs.',
          'age' => 45,
          'occupation' => 'Builder',
        ];
      }
      else {
        $data = [
          'nid' => $nid,
          'name' => 'Mary Smith',
          'age' => 35,
          'occupation' => 'Rocket Scientist',
          ];
      }

      return new JsonResponse($data, 200, [
      'Cache-Control' => 'public, max-age=3607',

    ]);
  }

  /**
   * Returns a JSON response with site configuration data.
   *
   * This method retrieves the site's name, slogan, and email from the system.site configuration.
   * It then creates a CacheableJsonResponse with this data and adds the configuration as a cacheable dependency.
   * This means that the response will be invalidated whenever the system.site configuration changes.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the site's name, slogan, and email.
   */
  public function jsonExample2(): JsonResponse {
    $config = $this->config('system.site');
    $response = new CacheableJsonResponse([
      'name' => $config->get('name'),
      'slogan' => $config->get('slogan'),
      'email' => $config->get('mail'),
    ]);

    // Add the system.site configuration as a cacheable dependency.
    $response->addCacheableDependency($config);

    // Set the Cache-Control header to make the response publicly cacheable for 3607 seconds.
    // And add the 'url.query_args' cache context so Drupal will cache.
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray([
      '#cache' => [
        'max-age' => 3607,
        'contexts' => ['url.query_args'],
      ],
    ]));

    // Add a cache tag for node 25.
    $node = Node::load(25);
    $cache_tag = $node->getCacheTags();
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray([
      '#cache' => [
        'tags' => $cache_tag,
      ],
    ]));
    // Response header shows: X-Drupal-Cache-Tags: config:system.site http_response node:25




    // Set the Cache-Control header to make the response publicly cacheable for 3607 seconds.
    //$response->headers->set('Cache-Control', 'public, max-age=3607');


    return $response;
  }


  public function cacheExample1(int $nid): array{

    if ($nid == 0) {
//        $build['#cache']['tags'][] = 'node_list';
      $data = [
        'nid' => $nid,
        'name' => 'Fred Bloggs.',
        'age' => 45,
        'occupation' => 'Builder',
      ];
    }
    else {
      $data = [
        'nid' => $nid,
        'name' => 'Mary Smith',
        'age' => 35,
        'occupation' => 'Rocket Scientist',
      ];
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t("Node ID: $nid, Name: $data[name], Age: $data[age], Occupation: $data[occupation]"),
    ];

    $build['#cache']['max-age'] = 3601;

    // Make this dependent on any changes to nodes.
    $build['#cache']['tags'][] = 'node_list';
    // Or make this dependent on any changes to taxonomy terms.
    // $build['#cache']['tags'][] = 'taxonomy_term_list';
    return $build;


    // This barfs???
//    // Create a response and add cacheable metadata.
//    $build['#cache']['tags'][] = 'node_list';
//    $response = new ResourceResponse($data, 200);
//    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($build));
//    return $response;

    // This ignores the theme and just returns the data.
    // It probably is useful for an API.
    // Create a response and add content and set cache-control.
    $response = new Response("abc", 200, ['#cache' => ['max-age' => 3603]]);
    $response->setContent("Node ID: $nid, Name: $data[name], Age: $data[age], Occupation: $data[occupation]");
    $response->headers->set('Cache-Control', 'public, max-age=3601');
    return $response;
  }

}

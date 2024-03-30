<?php

declare(strict_types=1);

namespace Drupal\general\Controller;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
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

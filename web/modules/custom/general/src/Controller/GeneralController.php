<?php

namespace Drupal\general\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Returns responses for General routes.
 */
class GeneralController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    if (FALSE) {

    $node_path = "/node/32";
    $new_alias = "/test-node";

    /** @var \Drupal\path_alias\PathAliasInterface $path_alias */
    $my_node_alias = \Drupal::entityTypeManager()->getStorage('path_alias')->create([
      'path' => $node_path,
      'alias' => $new_alias,
      'langcode' => 'en',
    ]);
    $my_node_alias->save();
    }


    $nid = 32;
    $options = ['absolute' => FALSE];  // FALSE will return relative path.
    // Note. If an alias is not set, you get /node/1234
    $url = Url::fromRoute('entity.node.canonical', ['node' => $nid], $options);
    $url_string = $url->toString(); // make a string
    // Returns "/test-node".
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath($url_string);

    // Given "/test-node, returns "/node/32".
    $alias = "/test-node";
    $path = \Drupal::service('path_alias.manager')->getPathByAlias($alias);

    // Returns "/general/example".
    $current_path = \Drupal::service('path.current')->getPath();

    $str = "Alias = $alias";
    $str .= "<br/> Current path = $current_path";
    $str .= "<br/> path = $path";

    $build['content'] = [
      '#type' => 'item',
//      '#markup' => $this->t('It works!'),
      '#markup' => $str,
    ];

//    drush_print("URL Alias set to:". $alias);

    return $build;
  }

}

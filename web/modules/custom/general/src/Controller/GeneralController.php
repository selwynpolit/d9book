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

    // Add ?abc=blah to the url
    $abc_val = $_GET['abc'];
    $abc_val = \Drupal::request()->query->get('abc');

    $current_route_name = \Drupal::routeMatch()->getRouteName();

    // Get URL alias – note. If a pathauto url alias is not set, you get '/node/32'
    $options = ['absolute' => TRUE];  // False will return relative path.
    $options = ['absolute' => FALSE];  // False will return relative path.
    $url = Url::fromRoute('entity.node.canonical', ['node' => 32], $options);
    $url_string = $url->toString();

    $node_path = '/node/32';
    $node32_alias = \Drupal::service('path_alias.manager')->getAliasByPath($node_path);

    $term_path = '/term/5';
    $term5_alias = \Drupal::service('path_alias.manager')->getAliasByPath($term_path);
    $term5_url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => 5], $options);
    $term5_alias = $term5_url->toString();

    //Taxonomy term
    $term_path_with_tid = \Drupal::service('path_alias.manager')->getPathByAlias('/hunger-strike');

    //User
    $user_path_with_uid = \Drupal::service('path_alias.manager')->getPathByAlias('/selwyn-the-chap');



    $str = "Alias = $alias";
    $str .= "<br/> Current path = $current_path";
    $str .= "<br/> path = $path";
    $str .= "<br/> abc = $abc_val";
    $str .= "<br/> current route name = $current_route_name";
    $str .= "<br/> url_string for node 32 = $url_string";
    $str .= "<br/> node32_alias = $node32_alias";
    $str .= "<br/> term5_alias = $term5_alias";
    $str .= "<br/> term_path_with_tid = $term_path_with_tid";
    $str .= "<br/> user_path_with_uid = $user_path_with_uid";

    $build['content'] = [
      '#type' => 'item',
//      '#markup' => $this->t('It works!'),
      '#markup' => $str,
    ];

//    drush_print("URL Alias set to:". $alias);

    return $build;
  }

  public function queryBuild() {
    $database = \Drupal::database();
    $query = $database->query("SELECT id, name, amount  FROM {donors}");

    $results = [];
    //    $results = $query->fetchAll();

    $result_count = count($results);
    $str = "Results from db query";
    $str .= "<br/> Result count = $result_count";


    while ($row = $query->fetchAssoc()) {
      $name =  $row['name'];
      $str .= "<br/> Name: $name";
    }
//    foreach ($results as $result) {
//      $name = $result['name'];
//      $str .= "<br/> Name: $name";
//    }



    $query = $database->query("SELECT sum(amount) as total_donations FROM {donors}");
    $results = $query->fetchAll();
    if (!empty($results)) {
      $total = $results[0]->total_donations;
    }

    $str .= "<br/>Total = $total";



    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;
  }

}

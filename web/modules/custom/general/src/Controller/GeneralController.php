<?php

namespace Drupal\general\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

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

  public function multiTest() {

    $str = '<h2>Results</h2>';

    $node = Node::load(35);

    // Write to index 0, 1, 2.
    self::smartMultiValueFieldSetter($node, 'field_condiment', 'ketchup', 0);
    self::smartMultiValueFieldSetter($node, 'field_condiment', 'mayo', 1);
    self::smartMultiValueFieldSetter($node, 'field_condiment', 'mustard', 2);
    //$node->save();

    $field_name = 'field_condiment';
    $field_type = $node->get($field_name)->getFieldDefinition()->getType();

    $contents = $node->get($field_name)->getValue();
    $str .= "<br/><strong>Field:</strong> " . $field_name;
    $str .= ",  type: " . $field_type;
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['value'] . ', ';
    }

    self::smartMultiValueFieldSetter($node, 'field_condiment', 'mustard', 2, 'dummy', TRUE);
    $contents = $node->get($field_name)->getValue();
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['value'] . ', ';
    }

    self::smartMultiValueFieldSetter($node, 'field_condiment', 'ketchup', 1, 'dummy', TRUE);
    $contents = $node->get($field_name)->getValue();
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['value'] . ', ';
    }


    $field_name = 'field_event';
    $field_type = $node->get($field_name)->getFieldDefinition()->getType();
    $contents = $node->get($field_name)->getValue();
    //kint($contents);
    $str .= "<br/><strong>Field:</strong> " . $field_name;
    $str .= ", type: " . $field_type;
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['target_id'] . ', ';
    }

    // 17, 18
    //14, 18, 19
    self::smartMultiValueFieldSetter($node, $field_name, 14, 0);
    self::smartMultiValueFieldSetter($node, $field_name, 18, 1);
    self::smartMultiValueFieldSetter($node, $field_name, 19, 2);
    $contents = $node->get($field_name)->getValue();
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['target_id'] . ', ';
    }


    $field_name = 'field_category';
    $field_type = $node->get($field_name)->getFieldDefinition()->getType();
    $contents = $node->get($field_name)->getValue();
    //kint($contents);
    $str .= "<br/><strong>Field:</strong> " . $field_name;
    $str .= ", type: " . $field_type;
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['target_id'] . ', ';
    }

    // 3, 2, 1
    // 1, 2, 3.
    self::smartMultiValueFieldSetter($node, $field_name, 1, 0);
    self::smartMultiValueFieldSetter($node, $field_name, 2, 1);
    self::smartMultiValueFieldSetter($node, $field_name, 3, 2);
    $contents = $node->get($field_name)->getValue();
    //kint($contents);
    $str .= "<br/><strong>Values: </strong>";
    foreach ($contents as $item) {
      $str .= $item['target_id'] . ', ';
    }


    // Multi-value Text Field.
    $field_name = 'field_condiment';
    // Returns FieldItemList.
    $data = $node->get($field_name);
    $data = $node->field_condiment;
    // Returns simple array.
    $data = $node->get($field_name)->getValue();
    $data = $node->field_condiment->getValue();


    // Multi-value entity reference field.
    $field_name = 'field_event';
    $data = $node->get($field_name)->getValue();
    $data = $node->field_event;

    // Multi-value taxonomy entity reference field.
    $field_name = 'field_category';
    $data = $node->get($field_name)->getValue();
    // Yes, you can use a variable for a magic field getter!
    $data = $node->$field_name;


    // Loop thru results.
    $items = $node->field_condiment;
    foreach ($items as $item) {
      $x = $item->value;
    }

    // get array of results.
    $condiments = $node->get('field_condiment');
    $vote_number = 1;
    if (isset($condiments[$vote_number])) {
      $result = $condiments[$vote_number]->value;
    }

    $result = $node->get('field_condiment')[0]->value;
    //$result = $node->get('field_condiment')[5]->value;
    if (isset($node->get('field_condiment')[2]->value)) {
      $result = $node->get('field_condiment')[2]->value;
    }



    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;

  }

  /**
   * Smart multi value field setter.
   *
   * Example calls:
   *
   * Set the index 2 to incomplete, keep old values:
   *  smartMultiValueFieldSetter($node, 'field_srp_voting_status', 'incomplete', 2);
   *
   *  Set the index 1 to incomplete, overwrite the old values to 'placeholder'
   *   smartMultiValueFieldSetter($node, 'field_srp_voting_status', 'incomplete', 1, 'placeholder', TRUE);
   *
   *
   * @param \Drupal\node\Entity\Node $node
   *   Node.
   * @param string $field_name
   *   Field name.
   * @param string $value
   *   Value to be put in $node->field[$index]->value.
   * @param int $index
   *   The delta i.e. $node->field[$index]
   * @param string $default_value
   *   The default values that will be written into the previous indexes.
   * @param bool $overwrite_old_values
   *   TRUE to ignore previous index values and overwrite them with $default_value.
   */
  public static function smartMultiValueFieldSetter(Node $node, string $field_name, string $value, int $index, string $default_value="", bool $overwrite_old_values=FALSE) {
    $old_values = $node->get($field_name)->getValue();

    // Grab old values and put them into $new_values array.

    $field_type = $node->get($field_name)->getFieldDefinition()->getType();
    if ($field_type == 'entity_reference') {
      foreach ($old_values as $key=>$old_value) {
        $new_values[$key] = $old_values[$key];
      }
    }
    else {
      $new_values = [];
      foreach ($old_values as $old_value) {
        $new_values[]["value"] = $old_value["value"];
      }
    }

    // Ignore what was in the old values and put my new default value in.
    if ($overwrite_old_values) {
      for ($i = 0; $i < $index; $i++) {
        $new_values[$i] = $default_value;
      }
    }

    // Pad missing items.
    for ($i = 0;$i<$index; $i++) {
      if (!isset($new_values[$i])) {
        if ($field_type == 'entity_reference') {
          $new_values[$i] = $default_value;
        }
        else {
          $new_values[$i]['value'] = $default_value;
        }
      }
    }

    if ($field_type == 'entity_reference') {
      $new_values[$index]['target_id'] = $value;
    }
    else {
      $new_values[$index]["value"] = $value;
    }

    // Trim off extras from testing.
    // TODO: this isn't trimming correctly for entity ref fields.
    if (count($new_values)>($index+1)) {
      $chunk = array_chunk($new_values, $index+1);
      $new_values = $chunk[0];
    }

    $node->set($field_name, $new_values);
  }

}

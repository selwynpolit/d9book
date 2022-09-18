<?php

namespace Drupal\queries\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\core\Database\Database;

/**
 * Returns responses for Queries routes.
 */
class QueriesController extends ControllerBase {

  public function buildQuery1() {
    $database = \Drupal::database();
    $query = $database->query("SELECT id, name, amount  FROM {donors}");
    $results = $query->fetchAll();

    $result_count = count($results);

    $str = "Results from db query";
    $str .= "<br/> Result count = $result_count";

    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;
  }

  public function buildQuery2() {

    $database = \Drupal::database();
    $query = $database->query("SELECT id, name, amount  FROM {donors}");
    $str = "Results from buildQuery2";
    while ($row = $query->fetchAssoc()) {
      $name =  $row['name'];
      $str .= "<br/> Name: $name";
    }

    $query = $database->query("SELECT sum(amount) as total_donations FROM {donors}");
    $results = $query->fetchAll();
    if ($results) {
      $total = $results[0]->total_donations;
    }

    $str .= "<br/><br/>Total = $total";

    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;
  }


  public function deleteQuery1() {
    $results = \Drupal::entityQuery('node')
      ->condition('type', 'event')
      ->range(0, 10)
      ->execute();

    if ($results) {
      foreach ($results as $result) {
        $node = Node::load($result);
        $node->delete();
      }
    }
    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => t("10 nodes deleted."),
    ];

    return $render_array;
  }

  //use Drupal\Core\Database\Database;

  public function deleteQuery2() {

    $database = \Drupal::database();
    $query_string = "Delete FROM {donors} where id>10 ";
    //$affectedRows = $database->query($query_string,[],['return' => Database::RETURN_AFFECTED]);
    $affectedRows = $database->query($query_string,[],['return' => Database::RETURN_AFFECTED]);

    $str = "Affected rows = $affectedRows";
    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;
  }

  //use Drupal\Core\Database\Database;

  public function updateQuery1() {
    $database = \Drupal::database();
    $query_string = "Update {donors} set amount=amount+1 where id<=10 ";
    $affectedRows = $database->query($query_string,[],
      ['return' => Database::RETURN_AFFECTED]);
    $str = "Affected rows = $affectedRows";
    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;

  }

  public function entityExists() {

    $name = 'hello';
    // See if the article named hello exists.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->condition('title', $name)
      ->count();

    $count_nodes = $query->execute();

    if ($count_nodes == 0) {
      $str = "Found no articles";
    }
    elseif ($count_nodes > 0) {
      $str = "Found $count_nodes articles";
    }
    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];
    return $render_array;
    }

  public function highestId() {
    $database = \Drupal::database();
    $connection = Database::getConnection();

    $query = $connection->select('donors', 'n');
    $query->addExpression('MAX(id)', 'id');
    $result = $query->execute();
    $highest_id = intval($result->fetchField());


    $str = "Highest id = $highest_id";
    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;
  }

  /**
   * @throws \Exception
   */
  public function insert() {
    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = Database::getConnection();

    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = \Drupal::service('database');

    //    $query = $connection->insert('donors', $options);

    // single insert.
    $result = $connection->insert('donors')
      ->fields([
        'name' => 'Singleton',
        'amount' => 1,
      ])
      ->execute();
    // Note. there is an auto-increment field so insert() returns  the value
    // for the new row in $result.
    $str = "Single insert returned auto-increment value of $result";


    // Multi-insert1.
    $result = $connection->insert('donors')
      ->fields(['name', 'amount',])
      ->values(['name' => 'Multiton1', 'amount' => 11,])
      ->values(['name' => 'Multiton1', 'amount' => 22,])
      ->execute();
    $str .= "<br/>Multi-insert1 added 2 rows";

    // Multi-insert2.
    $values = [
      ['name' => 'Multiton2', 'amount' => 111,],
      ['name' => 'Multiton2', 'amount' => 222,],
      ['name' => 'Multiton2', 'amount' => 333,],
      ['name' => 'Multiton2', 'amount' => 444,],
      ['name' => 'Multiton2', 'amount' => 555,],
    ];
    $query = $connection->insert('donors')
      ->fields(['name', 'amount',]);
    foreach ($values as $record) {
      $query->values($record);
    }
    $result = $query->execute();
    $str .= "<br/>Multi-insert2 added 5 rows";

    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $render_array;
  }

}

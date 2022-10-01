<?php

namespace Drupal\taxonomy_examples\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Returns responses for Taxonomy routes.
 */
class TaxonomyExamplesController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  public function loadByUUID() {
    $uuid = 'd4a7bbc5-3b1b-46a4-bea4-01255365999f';

    //$term_loaded_by_uuid = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['uuid' => $uuid]);
    $storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $term_loaded_by_uuid = $storage->loadByProperties(['uuid' => $uuid]);
    $term = reset($term_loaded_by_uuid);
    $str = "Term not found";
    if (!empty($term)) {
      $term_name = $term->getName();
      $term_id = $term->id();
      $str = "Term uuid = $uuid<br>";
      $str .= "Term name = $term_name<br>";
      $str .= "Term id = $term_id<br>";
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $build;
  }

  public function loadTermRef() {
    $nid = 24;
    $event_node = Node::load($nid);
    if ($event_node) {
      $categories = $event_node->get('field_event_category')->referencedEntities();
    }
    $str = "Terms:<br>";

    /** @var \Drupal\Core\Entity\EntityInterface $category */
    foreach ($categories as $category) {
      $term_id = $category->id();
      $term_name = $category->getName();
      $uuid = $category->uuid();
      $str .= "Term name = $term_name<br>";
      $str .= "Term id = $term_id<br>";
      $str .= "Term uuid = $uuid<br>";
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $build;
  }

  public function getTaxonomyAlias() {
    $tid = 3;
    //return taxonomy alias
    $options = ['absolute' => true];  //false will return relative path.

    // Build a URL.
    /** @var \Drupal\Core\Url $url */
    $url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $tid], $options);
    $path_string = $url->toString();
    $str = "URL Alias for tid $tid = $path_string";

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $build;
  }

  public function getMatchingNodes() {
    $term_id = 3;

    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'field_event_category' => $term_id,
    ]);

    $length = count($nodes);
    $str = "Found $length nodes matching term_id $term_id<br>";

    /** @var Node $node */
    foreach ($nodes as $node) {
      $str .= "Node ". $node->id() . ": Title: " . $node->getTitle() . "<br>";
    }
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    return $build;
  }

  public function loadTerms() {
    $vocabulary_id = 'event_category';

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vocabulary_id);

    /** @var \Drupal\taxonomy\Entity\Term $term */
    foreach ($terms as $term) {
      $terms_array[] = [
        'id' => $term->tid,
        'name' => $term->name,
        'weight' => $term->weight,
      ];
    }
    $length = count($terms_array);
    $str = "Found $length terms in vocabulary $vocabulary_id<br>";

    foreach ($terms as $term) {
      $str .= 'Term: ' . $term->name . ' term_id: ' . $term->tid . '<br>';
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    foreach ($terms_array as $item) {
      $term = Term::load($item['id']);
      $term_id = $term->id();
      $term_name = $term->label();
      $build[$term_name . $term_id] = [
        '#type' => 'item',
        '#markup' => $this->t('Term name: @term_name', ['@term_name' => $term_name]),
      ];
    }

    return $build;
  }

  public function loadTermByName() {


    $vocabulary_id = 'event_category';
    $term_name = 'protest';

    // Deprecated version.
    $terms = taxonomy_term_load_multiple_by_name($term_name, $vocabulary_id);

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => $term_name]);


    if (empty($terms)) {
      $build['result'] = [
        '#type' => 'item',
        '#markup' => $this->t('No terms found'),
      ];
    }
    else {
      /** @var Term $term */
      foreach ($terms as $term) {
        $term_name = $term->getName();
        $build[$term_name] = [
          '#type' => 'item',
          '#markup' => $this->t('Term name: @term_name. Term id: @term_id', [
            '@term_name' => $term_name,
            '@term_id' => $term->id(),
          ]),
        ];
      }
    }

    return $build;

  }

}

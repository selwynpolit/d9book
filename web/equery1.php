<?php

$query = \Drupal::entityQuery('node');
$query->condition('status', 1);
$query->condition('type', 'vote');
$query->sort('title', 'ASC');
$ids = $query->execute();

if (empty($ids)) {
  $this->output()->writeln("No votes found");
}
else {
  $this->output()->writeln("Found " . count($ids) . " articles");
}

foreach ($ids as $id) {
  $node = \Drupal\node\Entity\Node::load($id);
  $title = $node->getTitle();
  $this->output()->writeln("Nid: " . $id . " title: " . $title);
}

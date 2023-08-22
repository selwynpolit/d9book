<?php

//
// This example demonstrates how to write a drush
// script. These scripts are run with the php:script command.
//
use Drush\Drush;

$this->output()->writeln("Hello world!");
// $extra is an array if you issue arguments e.g.
// drush scr example4.drush abc def
$this->output()->writeln("The extra options/arguments to this command were:");
if (isset($extra)) {
  $this->output()->writeln(print_r($extra, true));
}

$database = \Drupal::database();
$query = $database->query("SELECT nid, vid, type  FROM {node} n");
$results = $query->fetchAll();

$result_count = count($results);
$this->output()->writeln("Result count = " . $result_count);
print "Result count = $result_count\n";

echo "Hi\n";

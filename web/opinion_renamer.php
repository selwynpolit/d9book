<?php

// arguments
// print_r($extra);


print ("usage: drush scr test1 <acg term id> <R>\n");
print ("e.g. 976 is the termid for John Smith.\n");


$acg = $extra[0] ?? '';
if (empty($acg)) {
  die("no acg termid");
}
$undo = FALSE;
if (isset($extra[1])) {
  $revert = $extra[1];
  if (strtoupper($revert) == "R") {
    $undo = TRUE;
    print("reverting titles for acg $acg\n");
  }
}

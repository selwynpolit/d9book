---
title: PHP tips
---

# PHP
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=php.md)

## Overview

## Foreach with ampersand (\&)
When iterating through an array of items, changes you make to the `as` value are not reflected in the original array.  e.g. if you iterate through your array as in `foreach ($cars as $car) {` any changes to `$car` will not be reflected in `$cars`

The following code shows the problem if you don\'t use the ampersand(\&).

```php

$correlations = [
  [
    'completion_status' => 'complete',
  ],
  [
    'completion_status' => 'incomplete',
  ],
  [
    'completion_status' => 'incomplete',
  ],
];

foreach ($correlations as $correlation) {
  $correlation['action_status'] = ($correlation['completion_status'] == 'complete') ? 'no-action-required' : 'action-required';
}
var_dump($correlations);

// Notice the use of the ampersand below.
foreach ($correlations as &$correlation) {
  $correlation['action_status'] = $correlation['completion_status'] == 'complete' ? 'no-action-required' : 'action-required';
}
var_dump($correlations);
```

And here is the output.  Notice that the first var_dump shows no sign of the `action_status` items.  With the ampersand(\&) the expected variable appears.
```
array(3) {
  [0]=>
  array(1) {
    ["completion_status"]=>
    string(8) "complete"
  }
  [1]=>
  array(1) {
    ["completion_status"]=>
    string(10) "incomplete"
  }
  [2]=>
  array(1) {
    ["completion_status"]=>
    string(10) "incomplete"
  }
}
array(3) {
  [0]=>
  array(2) {
    ["completion_status"]=>
    string(8) "complete"
    ["action_status"]=>
    string(18) "no-action-required"
  }
  [1]=>
  array(2) {
    ["completion_status"]=>
    string(10) "incomplete"
    ["action_status"]=>
    string(15) "action-required"
  }
  [2]=>
  &array(2) {
    ["completion_status"]=>
    string(10) "incomplete"
    ["action_status"]=>
    string(15) "action-required"
  }
}

```

Here is Copilot's take on the problem:

The code you provided is iterating over the `$correlations` array and updating each `$correlation` item's `action_status` based on its `completion_status`. However, this code doesn't actually update the original `$correlations` array because `$correlation` is a copy of the original item in the array, not a reference.

To refactor this code and make sure the original `$correlations` array is updated, you should use the `&` symbol to pass `$correlation` by reference:

```php
foreach ($correlations as &$correlation) {
    $correlation['action_status'] = $correlation['completion_status'] == 'complete' ? 'no-action-required' : 'action-required';
}
unset($correlation); // Unset reference to avoid unexpected behavior
```

This way, any changes made to `$correlation` inside the loop will be reflected in the original `$correlations` array. The `unset($correlation)` line is added after the loop to break the reference with the last element, as recommended in the PHP documentation to avoid unexpected behavior.




## Reference

* [PHP Docs](https://www.php.net/docs.php)
* [Change Record - Actions are now plugins, configured actions are configuration entities](https://www.drupal.org/node/2020549)
* [ECA: Event-Condition-Action - no-code solution to orchestrate your Drupal site](https://www.drupal.org/project/eca)

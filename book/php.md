---
title: PHP tips
---

# PHP
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=php.md)


## Foreach with ampersand (\&)
When iterating through an array of items, changes you make to the `as` value are not reflected in the original array.  e.g. if you iterate through your array as in `foreach ($cars as $car)` any changes to `$car` will not be reflected in `$cars`.

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


## Deep merge arrays with numeric keys

While searching for a way to merge two arrays with numeric keys, I found this [information on PHP.net](https://www.php.net/manual/en/function.array-merge-recursive.php).  Unfortunately `array_merge_recursive()` does not work with numeric keys.  The following code (from 12 years ago) is a solution to the problem.  I did have to get ChatGPT to modernize the code a bit so it would work with PHP 8.


```php
function array_merge_recursive_new(array $base, array $array1, array ...$arrays): array {
  array_unshift($arrays, $array1);

  foreach ($arrays as $array) {
    foreach ($array as $key => $value) {
      if (is_array($value) && isset($base[$key]) && is_array($base[$key])) {
        $base[$key] = array_merge_recursive_new($base[$key], $value);
      } else {
        $base[$key] = $value;
      }
    }
  }
  return $base;
}

// Define some test arrays
$array1 = ['a' => 1, 'b' => 2, 'c' => ['d' => 3]];
$array2 = ['a' => 2, 'c' => ['e' => 4]];
$array3 = ['f' => 5, 'c' => ['g' => 6]];

// Merge the arrays
$result = array_merge_recursive_new($array1, $array2, $array3);

// Print the result
print_r($result);

```

Here is the output:

```
Array
(
    [a] => 2
    [b] => 2
    [c] => Array
        (
            [d] => 3
            [e] => 4
            [g] => 6
        )
    [f] => 5
)
```


## Reference

* [PHP Docs](https://www.php.net/docs.php)
* [Change Record - Actions are now plugins, configured actions are configuration entities](https://www.drupal.org/node/2020549)
* [ECA: Event-Condition-Action - no-code solution to orchestrate your Drupal site](https://www.drupal.org/project/eca)

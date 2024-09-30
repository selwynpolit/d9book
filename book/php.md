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

While searching for a way to merge two arrays with numeric keys, I found this [information on PHP.net](https://www.php.net/manual/en/function.array-merge-recursive.php).  Unfortunately `array_merge_recursive()` does not work with numeric keys.  The following code (from 12 years ago) is a solution to the problem.  I did use ChatGPT to modernize the code for PHP 8.


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

## Match

PHP 8.0 introduced the `match` expression, which is similar to a `switch` statement but with a more concise syntax. Here's an example of how you can use `match`:

```php
  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
   return match ($this->get('value')->getValue()) {
     NULL, '' => TRUE,
     default => FALSE,
   };
  }
```

It allows you to match a value against multiple conditions and return a result based on the matched condition. In this example, the `isEmpty` method checks if the value of the field is `NULL` or an empty string and returns `TRUE` if it matches, otherwise it returns `FALSE`.

More at [php.net](https://www.php.net/manual/en/control-structures.match.php).


## Field mapping

Here is the most convoluted way (Don\'t ask.) I could figure out to insert some additional instructions on a form upload field in a media entity. It showed some interesting ways to get deep into the render array. This line: `$field_reference = &$form;` establishes a starting point for navigating the form's structure. By using a reference (`&`), any changes made to `$field_reference` will directly affect the `$form` array, ensuring that the alterations are applied to the actual form being processed.

The loop below navigates through the form array to the specific field that needs modification. It does so by updating $field_reference at each step to point deeper into the form structure, following the path defined in the mapping. This dynamic navigation allows the code to reach any field within the form, regardless of its depth or location.

```php
foreach ($mapping['field_path'] as $path) {
  $field_reference = &$field_reference[$path];
}
```


Here is the whole function:

```php
/**
 * Implements hook_form_alter().
 */
function abc_form_alter(&$form, FormStateInterface $form_state, $form_id) {
    // Mapping of form IDs to their respective fields and new descriptions.
    $message = t('Keep titles clear and useful. e.g., "ski_trip_2023" instead of "img_pxl_443445"<br>');
  $form_mappings = [
    'media_library_add_form_upload' => [
      'field_path' => ['container', 'upload'],
      'description' => $message,
    ],
    'media_image_add_form' => [
      'field_path' => ['field_media_image', 'widget', 0],
      'description' => $message,
    ],
    'media_image_edit_form' => [
      'field_path' => ["replace_file", "replacement_file"],
      $message,
    ],
  ];
  // Check if the current form ID is in the mappings.
  if (array_key_exists($form_id, $form_mappings)) {
    // Retrieve the mapping for the current form.
    $mapping = $form_mappings[$form_id];
    // Build the reference to the form field.
    $field_reference = &$form;
    foreach ($mapping['field_path'] as $path) {
      $field_reference = &$field_reference[$path];
    }
    // Insert the new description before the existing one.
    $field_reference['#description'] = $mapping['description'] . '<br>' . $field_reference['#description'];
  }
}
```

For those of you following along at home, you may realize that this could easily be performed with a simple `hook_form_FORM_ID_alter()` implementation for each form. Some developers think this is a better way to do things. You decide.


## The difference between require and include

The difference between include and require is subtle. 

Require, as the function name suggests *requires* that the included file exist to continue the script. So, if require fails, the script stops. 

Using include *will allow* the script to continue. Most of the time, using require makes more sense because it's likely that the file we want to include includes some important information that is required for your application to run properly.

e.g. 
```php
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}

// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}
```


## nullsafe operator

The nullsafe operator `?->` is a new feature in PHP 8 that allows you to safely access properties and methods of an object without having to check if the object is null. This means you only need 1 null check as the operator will return null if any of the calls in the chain return null.

```php
 $job = $this->apiHandler?->getJobs()?->getJobById($job_id);
 // Any of the calls can return NULL
 if ($job === NULL) {
  continue;
 }
```

::: tip Note
The `?->` signs after each call. If any of the calls in the chain return null, the entire chain will return null.
:::

[PHP Nullsafe Operator](https://wiki.php.net/rfc/nullsafe_operator)

## Regex examples


```php
// Remove Lab and a space from the beginning of a string.
$bundle_label = preg_replace('#^(Lab )#i', '', $bundle_label);
```

```php
// Remove Team Content regardless of case from the beginning of a string.
// #i is the case-insensitive flag.
$bundle_label = preg_replace('#^(Team Content)#i', 'Team', $bundle_label);
```

```php
// Create human friendly ids for the top level paragraphs for the tabs
// Match strings that start with "lab_", "office_", or "staff_profile_"
$start_pattern = '/^(lab|office|staff_profile)_/';
if (preg_match($start_pattern, $entity->bundle())) {
  // Remove the matched prefix.
  $id = preg_replace($start_pattern, '', $id);
}
```

Return early pattern

I am a fan of the [return early pattern](https://medium.com/swlh/return-early-pattern-3d18a41bba8).  Return early is the way of writing functions or methods so that the expected positive result is returned at the end of the function and the rest of the code terminates early by returning or throwing an exception if there are any errors.  I've also seen this called the "happy path" pattern.


Doesn't this code make you feel good?  It is from the medium article linked above.  

```php
public String returnStuff(SomeObject argument1, SomeObject argument2){
      if (!argument1.isValid()) {
            throw new Exception();
      }

      if (!argument2.isValid()) {
            throw new Exception();
      }

      SomeObject otherVal1 = doSomeStuff(argument1, argument2);

      if (!otherVal1.isValid()) {
            throw new Exception();
      }

      SomeObject otherVal2 = doAnotherStuff(otherVal1);

      if (!otherVal2.isValid()) {
            throw new Exception();
      }

      return "Stuff";
}
```

While you are contemplating this, consider these anti-patterns: [Else is considered smelly](https://wiki.c2.com/?ElseConsideredSmelly=) and [Arrow Anti-Pattern](https://wiki.c2.com/?ArrowAntiPattern) where code becomes shaped like an arrow because of nested conditions and loops like:

```
 if
   if
     if
       if
         do something
       endif
     endif
   endif
 endif
```

## Reference

* [PHP Docs](https://www.php.net/docs.php)
* [PHP: The Right Way - easy-to-read, quick reference for PHP popular coding standards, links to authoritative tutorials, and what the contributors consider to be best practices at present](https://phptherightway.com/)
* [Serializing and unserializing data in PHP Apr 19, 2024 by Jeroen Deviaene Apr 2024](https://www.deviaene.eu/articles/2024/php-serialize-unserialize/?utm_source=mglaman&utm_medium=email&utm_campaign=mglamandev-may-17th-2024)
* [Change Record - Actions are now plugins, configured actions are configuration entities](https://www.drupal.org/node/2020549)
* [ECA: Event-Condition-Action - no-code solution to orchestrate your Drupal site](https://www.drupal.org/project/eca)
* [Learn Object Oriented PHP by Gary Clarke](https://www.garyclarke.tech/p/learn-object-oriented-php)
* [PHP 8.1: What's New?](https://www.php.net/releases/8.1/en.php)
* [PHP 8.2: What's New?](https://www.php.net/releases/8.2/en.php)
* [PHP 8.3: What's New?](https://www.php.net/releases/8.3/en.php)
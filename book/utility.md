---
title: Utility
---

# Drupal Utility Functions
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=utility.md)

## Overview

Drupal provides developers with a variety of utility functions that make it easier and more efficient to perform tasks that are either really common, tedious, or difficult. Utility functions help to reduce code duplication and should be used in place of one-off code whenever possible.

## Random Class

The Random utility in Drupal is a pivotal tool for generating diverse test data, essential for robust automated testing. It aids developers by providing placeholder content for UI design and development, ensuring modules and themes can handle varied user inputs. This utility not only streamlines the development process but also plays a crucial role in enhancing the security and performance of Drupal projects.

### Generating a Random ASCII String
The string method generates a random string of ASCII characters. It's great for when you need a random string without any specific requirements.

```php
// Generate a random 10-character string.
$random = new Drupal\Component\Utility\Random();
$randomString = $random->string(10);
```

### Creating a Random Alphanumeric String
The name method is perfect for generating machine-readable strings like usernames or file names, as it includes letters and numbers and starts with a letter.

```php
// Generate a random 8-character alphanumeric string.
$randomName = $random->name(8);
```

### Crafting a Random Word
The word method creates a string that alternates between consonants and vowels, resembling a word. It's useful for generating more readable test data.

```php
// Generate a random 6-letter word.
$randomWord = $random->word(6);
```

### Generating a Random Object
With the object method, you can create a PHP object with a specified number of random properties. Each property has a random string value.

```php
// Generate a PHP object with 3 random properties.
$randomObject = $random->object(3);
```

### Generating Random Sentences
The sentences method produces a string of random Latin-esque words, forming sentences. This can be used for generating placeholder text.

```php
// Generate a string of random sentences totaling at least 100 words.
$randomSentences = $random->sentences(100);
```

### Creating Random Paragraphs
The paragraphs method generates multiple paragraphs of random Latin-esque sentences, separated by double new lines.

```php
// Generate 5 random paragraphs.
$randomParagraphs = $random->paragraphs(5);
```

### Generating a Placeholder Image
Finally, the image method creates a placeholder image file with random colored sections and a circle. Specify the destination and resolution range.

```php
// Generate a random placeholder image between 400x300 and 800x600 pixels.
$destination = "/path/to/your/image.jpg"; // Ensure this is a valid writable path.
$randomImage = $random->image($destination, '400x300', '800x600');
```

Each of these methods offers a different kind of random data generation, suitable for various testing and placeholder needs in Drupal development. Whether you need random strings for user inputs, objects with random properties for testing object interactions, or even placeholder images for theming, this utility class has you covered.

## Nested Array Utility

There is a useful utility class in Drupal called `NestedArray` that provides a set of static methods for manipulating nested arrays. This class is particularly helpful when working with complex nested arrays, such as form elements or configuration data. Check [it out at api.drupal.org](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21NestedArray.php/class/NestedArray/10)

Here is a really convoluted way to insert some additional instructions on a form upload field when adding or editing media entities. If you really want to impress others, you can use the `NestedArray` utility class like this:

```php

use Drupal\Component\Utility\NestedArray;

/**
 * Implements hook_form_alter().
 */
function abc_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  // Mapping of form IDs to their respective fields and new descriptions.
  $form_mappings = [
    'media_library_add_form_upload' => ['container', 'upload'],
    'media_image_add_form' => ['field_media_image', 'widget', 0],
    'media_image_edit_form' => ['replace_file', 'replacement_file'],
  ];

  // Check if the current form ID is in the mappings.
  if (array_key_exists($form_id, $form_mappings)) {
    $message = t('Keep titles clear and useful. e.g., "ski_trip_2023" instead of "img_pxl_443445"<br>');
    $parents = $form_mappings[$form_id];
    $el = &NestedArray::getValue($form, $parents);
    if ($el !== NULL) {
      $el['#description'] = $message . $el['#description'];
    }
  }
}
```
More on the `NestedArray` class can be found [at api.drupal.org](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21NestedArray.php/class/NestedArray/10).

Just for giggles, this is the way I think it should be done:

```php
/**
 * Implements hook_form_FORM_ID_alter().
 */
function abc_media_form_media_library_add_form_upload_alter(&$form, FormStateInterface $form_state, $form_id) {
  $form['container']['upload']['#description'] = t('Keep titles clear and useful. e.g., "ski_trip_2023" instead of "img_pxl_443445"<br>') . '<br>' . $form['container']['upload']['#description'];
}
/**
 * Implements hook_form_FORM_ID_alter().
 */
function abc_media_form_media_image_add_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $form['field_media_image']['widget'][0]['#description'] = t('Keep titles clear and useful. e.g., "ski_trip_2023" instead of "img_pxl_443445"<br>') . '<br>' . $form['field_media_image']['widget'][0]['#description'];
}
/**
 * Implements hook_form_FORM_ID_alter().
 */
function abc_media_form_media_image_edit_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $form["replace_file"]["replacement_file"]["#description"] = t('Keep titles clear and useful. e.g., "ski_trip_2023" instead of "img_pxl_443445"<br>') . '<br>' . $form["replace_file"]["replacement_file"]["#description"];
}
```

## getClass() Utility
This function prepares a string for use as a valid class name by replacing the following characters that are not alphanumeric or an underscore. It is useful when you need to generate a class name based on a string that may contain special characters.  It can also be used to clean up taxonomy term names as in the example below:

This code ensures that the term name comparison accounts for variations in character casing and formatting, for matching user-provided arguments to taxonomy terms.

```php
foreach ($all_terms as $term) {
  // Replace certain characters like spaces and special characters with hyphens.
  $term = \Drupal\Component\Utility\Html::getClass($term->getName());
  // Replace hyphens with spaces.
  $term = str_replace('-', ' ', $term);
  // Check if the term matches the argument.
  if (strcasecmp($term, $argument) === 0) {
    $terms[$term->id()] = $term;
  }
}
```



The `getClass()` function calls the function below which may help understand exactly what it does:

```php
  /**
   * Prepares a string for use as a CSS identifier (element, class, or ID name).
   *
   * Link below shows the syntax for valid CSS identifiers (including element
   * names, classes, and IDs in selectors).
   *
   * @see http://www.w3.org/TR/CSS21/syndata.html#characters
   *
   * @param string $identifier
   *   The identifier to clean.
   * @param array $filter
   *   An array of string replacements to use on the identifier.
   *
   * @return string
   *   The cleaned identifier.
   */
  public static function cleanCssIdentifier(
    $identifier,
    array $filter = [
      ' ' => '-',
      '_' => '-',
      '/' => '-',
      '[' => '-',
      ']' => '',
    ],
  ) {
    // We could also use strtr() here but its much slower than str_replace(). In
    // order to keep '__' to stay '__' we first replace it with a different
    // placeholder after checking that it is not defined as a filter.
    $double_underscore_replacements = 0;
    if (!isset($filter['__'])) {
      $identifier = str_replace('__', '##', $identifier, $double_underscore_replacements);
    }
    $identifier = str_replace(array_keys($filter), array_values($filter), $identifier);
    // Replace temporary placeholder '##' with '__' only if the original
    // $identifier contained '__'.
    if ($double_underscore_replacements > 0) {
      $identifier = str_replace('##', '__', $identifier);
    }

    // Valid characters in a CSS identifier are:
    // - the hyphen (U+002D)
    // - a-z (U+0030 - U+0039)
    // - the colon (U+003A)
    // - A-Z (U+0041 - U+005A)
    // - the underscore (U+005F)
    // - 0-9 (U+0061 - U+007A)
    // - ISO 10646 characters U+00A1 and higher
    // We strip out any character not in the above list.
    $identifier = preg_replace('/[^\x{002D}\x{0030}-\x{0039}\x{003A}\x{0041}-\x{005A}\x{005F}\x{0061}-\x{007A}\x{00A1}-\x{FFFF}]/u', '', $identifier);
    // Identifiers cannot start with a digit, two hyphens, or a hyphen followed by a digit.
    $identifier = preg_replace([
      '/^[0-9]/',
      '/^(-[0-9])|^(--)/',
    ], ['_', '__'], $identifier);
    return $identifier;
  }
```

## Reference

- [Utility classes and functions Drupal API](https://api.drupal.org/api/drupal/core%21core.api.php/group/utility/10)

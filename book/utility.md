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

Here is a really convoluted way to insert some additional instructions on a form upload field in a media entity. If you really want to show the world how clever you are and that you can use the `NestedArray` utility class, you can use do it like this:

```php
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


## Reference

- [Utility classes and functions Drupal API](https://api.drupal.org/api/drupal/core%21core.api.php/group/utility/10)

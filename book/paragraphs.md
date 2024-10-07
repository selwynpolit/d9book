---
title: Paragraphs
permalink: /paragraphs
---

# Paragraphs
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=paragraphs.md)


## Introduction

Paragraphs are those special things that allow you to blend fields together e.g. count and unit of measure so you can store values
like 5 kilograms or 7 years etc. Often they are used like nodes where you define the fields and fill them with data that get displayed on the screen for things like carousels or events.

## Load a node and find the terms referenced in a paragraph in a term reference field

Here we loop thru all the instances of my paragraph reference and grab the term in the paragraph.

```php
use Drupal\taxonomy\Entity\Term;

foreach ($node->get('field_my_para')->referencedEntities() as $ent){
  $term = Term::load($ent->$field_in_paragraph->target_id);
  $name = $term->getName();
  print_r($name);
}
```

## Find all the long text fields that have a certain string

In this code snippet from an [issue in the entity embed module](https://www.drupal.org/project/entity_embed/issues/3077225#comment-14806085) the author loads all the fields of type string_long, text_long, and text_with_summary and then queries for nodes with the value `data-entity-embed-display-settings=""`.  The code then removes the string and saves the node. It is a useful example because it shows how to find all the fields of a certain type.

Please note that I have not tested this code so it may not be perfect.

```php
<?php

/**
 * Example batch update hook.
 */
function just_an_example(&$sandbox) {
  // Get the entity type manager and node storage.
  $entity_type_manager = \Drupal::entityTypeManager();
  $node_storage = $entity_type_manager->getStorage('node');
  $limit = 100;

  // Initialize the sandbox if it's the first run.
  if (!isset($sandbox['total'])) {
    // Load all fields of type string_long, text_long, and text_with_summary.
    $string_long_fields = array_map(fn ($field) => $field->getName(),
      $entity_type_manager
        ->getStorage('field_storage_config')
        ->loadByProperties([
          'entity_type' => 'node',
          'type' => 'string_long',
        ])
    );
    $text_long_fields = array_map(fn ($field) => $field->getName(),
      $entity_type_manager
        ->getStorage('field_storage_config')
        ->loadByProperties([
          'entity_type' => 'node',
          'type' => 'text_long',
        ])
    );
    $text_with_summary_fields = array_map(fn ($field) => $field->getName(),
      $entity_type_manager
        ->getStorage('field_storage_config')
        ->loadByProperties([
          'entity_type' => 'node',
          'type' => 'text_with_summary',
        ])
    );

    // Merge all field names into a single array.
    $sandbox['fields'] = array_merge($string_long_fields, $text_long_fields, $text_with_summary_fields);

    // Initialize bundles array.
    $sandbox['bundles'] = [];
    foreach ($sandbox['fields'] as $field_name) {
      // Load field storage config and get bundles for each field.
      $field_storage = $entity_type_manager->getStorage('field_storage_config')->load($field_name);
      if ($field_storage) {
        $sandbox['bundles'] = array_merge($sandbox['bundles'], $field_storage->getBundles());
      }
    }

    // Initialize counters.
    $sandbox['current'] = $sandbox['count'] = $sandbox['changed'] = 0;

    // Initialize node IDs array.
    $sandbox['nids'] = [];
    foreach ($sandbox['fields'] as $field) {
      // Query for nodes with specific field values and add to node IDs array.
      $query = \Drupal::database()->select("node__{$field}", 'f')
        ->fields('f', ['entity_id'])
        ->condition("f.{$field}_value", 'data-entity-embed-display-settings=""', 'REGEXP BINARY')
        ->condition('f.bundle', $sandbox['bundles'], 'IN');
      $sandbox['nids'] = array_merge($sandbox['nids'], $query->execute()->fetchCol());
    }

    // Remove duplicate node IDs and sort them.
    $sandbox['nids'] = array_unique($sandbox['nids']);
    sort($sandbox['nids']);
    $sandbox['total'] = count($sandbox['nids']);
  }

  // Query for nodes to process in the current batch.
  $nids = $node_storage->getQuery()
    ->accessCheck(FALSE)
    ->range(0, $limit)
    ->condition('nid', $sandbox['nids'], 'IN')
    ->condition('nid', $sandbox['current'], '>=')
    ->sort('nid', 'ASC');
  $nodes = $nids->execute();
  $nodes = $node_storage->loadMultiple($nodes);

  // Process each node.
  foreach ($nodes as $node) {
    $languages = $node->getTranslationLanguages();

    // Process each language translation of the node.
    foreach ($languages as $langcode => $lang_obj) {
      $cs_node = $node->getTranslation($langcode);
      $changed_node = FALSE;

      // Process each field in the node.
      foreach ($sandbox['fields'] as $field_name) {
        if ($cs_node->hasField($field_name)) {
          $value = $cs_node->get($field_name)->getValue();
          $changed_field = FALSE;

          // Check and update field values.
          foreach ($value as &$content) {
            if (preg_match_all('/data-entity-embed-display-settings=""/', $content['value'], $matches)) {
              foreach ($matches[0] as $replacement) {
                // Remove the match and flag for saving.
                $content['value'] = str_replace($replacement, '', $content['value']);
                $changed_field = $changed_node = TRUE;
              }
            }
          }

          // Save the updated field value.
          if ($changed_field) {
            $cs_node->set($field_name, $value);
          }
        }
      }

      // Save the node if any field was changed.
      if ($changed_node) {
        $sandbox['changed']++;
        $cs_node->save();
      }
    }

    // Update the current node ID and increment the count.
    $sandbox['current'] = $node->id();
    $sandbox['count']++;
  }

  // Update the progress and display status messages.
  $sandbox['#finished'] = empty($sandbox['total']) ? 1 : ($sandbox['count'] / $sandbox['total']);
  \Drupal::messenger()->addStatus($sandbox['count'] . ' nodes processed out of ' . $sandbox['total']);
  \Drupal::messenger()->addStatus($sandbox['changed'] . ' nodes altered');
}
```


## Load a node and retrieve a paragraph field

Because paragraphs and nodes are both entities, the pattern is the same. You load the entity (node or paragraph) and then simply reference the field name e.g. `myentity->field_blah`

From `/Users/selwyn/Sites/inside-mathematics/themes/custom/danaprime/danaprime.theme`

These are a little different from regular fields. Generally you want to get their `target_id` which will tell you the `pid` or paragraph id. Here are two different ways to load a `video_collection_node` and go to retrieve a field `field_related_lessons` which holds paragraphs of type `related_lessons`:

```php
$video_collection_node = Node::load($video_collection_nid);

//This gives you a bunch of \Drupal\entity_reference_revisions\EntityReferenceRevisionsFieldItemList items
$lessons = $video_collection_node->field_related_lessons;
//or
$lessons = $video_collection_node->get('field_related_lessons');

foreach ($lessons as $lesson) {
  $paragraph_revision_ids[] = $lesson->target_revision_id;
}
```

Paragraphs use the contributed [Entity Reference Revisions module](https://www.drupal.org/project/entity_reference_revisions) to reference paragraphs and it is very important to use the `target_revision_id` property when referencing paragraphs. Alternatively, the `entity` computed property can be used to retrieve the paragraph entity itself.

OR

```php
//This gives you an array of arrays [['target_id' => '348','target_revision_id' => '348'],['target_id' => '349','target_revision_id' => '349'] ]
$lessons = $video_collection_node->get('field_related_lessons')->getValue();
foreach ($lessons as $lesson) {
  $paragraph_revision_ids[] = $lesson['target_revision_id'];
}
```

Collecting them like this is only an example, while the `loadMultiple` method exists on entity storage objects, there is no `loadMultipleRevisions` method.

```php
// This gives you null! - don't do this.
$lessons = $video_collection_node->get('field_related_lessons')->value;
```

:::tip Note
Note. `getValue()` here will get you the nid buried in a result array of arrays like `result[0]['target_revision_id']` - quicker to just grab `->target_revision_id`
:::

## Load a node and grab a paragraph field to find the nid in an entity reference field

From
`/Users/selwyn/Sites/inside-mathematics/themes/custom/danaprime/danaprime.theme` - Continuing from above, I load a node, grab it's field `field_related_lessons` which holds paragraphs of type `related_lessons` and grab it's field `field_lesson.` That field has a target_id which is the nid for the entity reference field. Phew!:

```php
//Grab the related lessons from the collection.
$video_collection_node = Node::load($video_collection_nid);
$lessons = $video_collection_node->field_related_lessons;
$storage = \Drupal::entityTypeManager()->getStorage('paragraph');
foreach ($lessons as $lesson) {
  //Load each paragraph and get the nids from them.
  $paragraph = $lesson->entity;
  $related_lessons_nid = $paragraph->field_lesson->target_id;
  $related_lessons_nids[] = $related_lessons_nid;
}

//This should have an array of nids for video_details.
$variables['related_lessons_pids'] = $pids;
$variables['related_lessons_nids'] = $related_lessons_nids;
```


## Loop through the paragraphs in a node
This code is from a `hook_preprocess_node` function in a `.theme` file. It loads a node and then loops through the paragraphs in a field `field_related_lessons` and then grabs the title of the paragraph.

```php
<?php

use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_node().
 */
function abc_preprocess_node(&$variables) {

  /** @var \Drupal\node\Entity\Node $node */
  $node = $variables['node'];
  $bundle = $node->bundle();
  $view_mode = $variables['view_mode'];

  // Check the there is a field_content field.
  if (isset($variables['content']['field_content'])) {
    $content_tabs = [];
    // Loop through each instance of the field_content field.
    foreach (Element::children($variables['content']['field_content']) as $idx) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      // Grab each paragraph.
      $entity = $variables['content']['field_content'][$idx]['#paragraph'];

      // Get the paragraph's human readable type (not machine name).
      $bundle_label = \Drupal::entityTypeManager()
        ->getStorage($entity->getEntityType()->getBundleEntityType())
        ->load($entity->bundle())
        ->label();

      // For staff_profile content, strip off the "Staff Profile" prefix.
      if ($bundle === 'staff_profile') {
        // This makes "Staff Profile Staff Resources" into "Resources".
        $bundle_label = preg_replace('#^(Staff Profile )#i', '', $bundle_label);

        $variables['offset'] = 0;
        // Check if there is a title term on the node.
        if (($title_term = $node->get('field_staff_profile_title')->entity) !== NULL) {
          // If the title term has a field_global field and it is set to 1, offset the first tab.
          if ($title_term->hasField('field_global') && $title_term->get('field_global')->getString() == '1') {
            $variables['offset'] = 1;
          }
        }
      }
      ...

```

## Add validation for paragraph fields

Here we want to perform validation when a node is added or edited so we use `hook_form_alter` to add a validation function to the form. In this case, we are adding a validation function to a node form with the id `node_staff_profile_form` and `node_staff_profile_edit_form`. We add a class to the form so we can target it with CSS and then add a validation function `_ccr_admin_enhancements_area_of_expertise_validate` to the form.



In `abc_admin_enhancements.module`:
```php

/**
 * Implements hook_form_alter().
 */
function ccr_admin_enhancements_form_node_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  if ($form_id === 'node_staff_profile_form' || $form_id === 'node_staff_profile_edit_form') {
    $form['#attributes']['class'][] = 'staff-profile-form';
    $form['#validate'][] = '_abc_admin_enhancements_area_of_expertise_validate';
  }
}

/**
 * Validate the area of expertise field.
 */
function _abc_admin_enhancements_area_of_expertise_validate(&$form, FormStateInterface &$form_state) {
  // Don't perform validations if the field doesn't exist.
  if (!($form_state->hasValue('field_areas_of_expertise'))) {
    return;
  }
  if ($form_id === 'node_staff_profile_form' || $form_id === 'node_staff_profile_edit_form') {
    $form['#attributes']['class'][] = 'staff-profile-form';
    $form['#validate'][] = '_ccr_admin_enhancements_area_of_expertise_validate';

    /*
    * Special primary titles: Assistant Research Physician, Associate Research
    *  Physician, Senior Research Physician, OR Staff Clinician (Contr).
    */
    $special_primary_titles = [2477, 2478, 2558, 2651];

    $profile_type = $form_state->getValue('field_staff_profile_type')[0]['target_id'] ?? NULL;
    $ccr_primary_title = $form_state->getValue('field_staff_profile_title')[0]['target_id'] ?? NULL;
    // Some validations will not apply to Other Staff profiles.
    $is_other_staff = !empty($profile_type) && $profile_type == 2575;

    $button = $form_state->getTriggeringElement();
    // Check what paragraph button was pressed: add, remove or closed.
    if (!empty($button)) {
      if ( isset($button['#paragraphs_mode']) && $button['#paragraphs_mode'] === 'remove') {
        // We don't need to validate on paragraph removal.
        return;
      }
      else if ($button['#name'] === 'field_areas_of_expertise_area_of_expertise_add_more') {
        // We don't need to validate on paragraph addition.
        return;
      }
    }

    $values = $form_state->getValue('field_areas_of_expertise');

    // Paragraphs come to us indexed by a number, not necessarily zero-based.
    $first = TRUE;
    // Loop through each area of expertise paragraph.
    foreach ($values as $i => $value) {
      
      // Disease focus is required for first area of expertise for certain profiles.
      if ($first) {
        if (empty($disease_focus) && $is_other_staff && in_array($ccr_primary_title, $special_primary_titles)) {
          $form_state->setErrorByName('field_areas_of_expertise][' . $i . '][subform][field_disease_focus', 'Area of Expertise: Disease Focus is required for this profile type.');
          continue;
        }
      }

      // At least one of these fields must be filled in.
      if (empty($custom_text) && empty($research_area) && empty($disease_focus)) {
        $form_state->setErrorByName('field_areas_of_expertise][' . $i . '][subform', 'Area of Expertise: One of these options must be selected.');
        continue;
      }


    }
  }

```

## Load a node and find the terms referenced in a paragraph in a term reference field

Here we loop thru all the instances of my paragraph reference and grab
the term in the paragraph.

```php
foreach ($node->get('field_my_para')->referencedEntities() as $ent){
  $term = $ent->$field_in_paragraph->entity;
  $name = $term->getName();
  print_r($name);
}
```



## Reference

### Great Cheat sheets
- [Various ways of updating field values in Drupal 8 and 9 - Nov 2020](https://gorannikolovski.com/blog/various-ways-updating-field-values-drupal-8-and-9)
- [Entity query cheat sheet from Metal Toad - Nov 2017](https://www.metaltoad.com/blog/drupal-8-entity-api-cheat-sheet)
- [Drupalsun: Drupal entity API cheat sheet - Jul 2018](https://drupalsun.com/zhilevan/2018/07/21/drupal-entity-api-cheat-sheet)

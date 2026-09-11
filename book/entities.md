---
title: Entities
---

# Entities
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=entities.md)

## Overview

In Drupal, **entities** are the fundamental data structures used to represent content and complex configuration. They provide both a flexible system for handling different types of content e.g. _nodes_, _users_, _taxonomy terms_, and _files_, as well as different types of configuration e.g. _views_, _image styles_ and _user roles_.

::: info
See [Introduction to Entity API in Drupal - Updated March 2024](https://www.drupal.org/docs/drupal-apis/entity-api/introduction-to-entity-api-in-drupal-8) in the Drupal.org documentation that has an entire section dedicated to the Entity API.
:::


## Content entity types

Content entities are the storage mechanism for data in a Drupal site that can be managed by editors via the admin interface. Content entities are composed of **fields** each which store a specific type of data such as text, dates, or references to other entities.

The two types of fields on content entities are:
*   **Base fields**: Defined programmatically in the entity class, data is stored in a single table per entity type and they exist for all bundles (e.g., `title`, `created`).
*   **Configurable fields**: Created via the admin interface or config. They are attached to specific bundles, but can be re-used across multiple bundles, and data is stored in their own database table.

::: info
Read more about fields from [Defining and using Content Entity Field definitions on Drupal.org](https://www.drupal.org/docs/drupal-apis/entity-api/defining-and-using-content-entity-field-definitions)
:::

### Bundles

Content entity bundles are variants of an entity type that allow for different field configurations. For example, the `Node` entity type in Drupal core comes with the bundles _Article_ and _Page_. Bundles are not exposed on all content entity types (e.g., `User`).


## Config entity types

They store configuration information e.g: views, imagestyles, roles, NodeType (which defines node bundles).

-   They are not revisionable
-   They don't have fields/are not fieldable.
-   They don't support entity translation interface(TranslatableInterface), but can still be translated using config's translation API.


## Creating custom entity types

Defining your own content entity types can be useful when content you want to model within Drupal doesn't fit with an out of the box entity. One good use case is storing quiz/test results for a user; it gives a lot of control over how the data is stored and presented without polluting an existing core content entity. Other examples in contributed modules include: [Product](https://git.drupalcode.org/project/commerce/-/blob/3.x/modules/product/src/Entity/Product.php) in [Drupal Commerce](https://www.drupal.org/project/commerce), and [Redirect entity](https://git.drupalcode.org/project/redirect/-/blob/8.x-1.x/src/Entity/Redirect.php) in [Redirect](https://www.drupal.org/project/redirect).

Defining a custom content entity type from scratch is a long winded and error prone task, thus it is far easier to start by using a tool such as [Drush](/drush) to kick off your new entity type.

To generate a content entity the following command can be used:

`drush generate entity:content`

This will prompt you with several questions so the entity type can be tailored to your requirements. Similarly for config entities:

`drush generate entity:config`


## Query an entity by title and type

This example counts the number of entities of type `article` with the name `$name`. Note that access checking must be [explicitly specified on content entity queries](https://www.drupal.org/node/3201242).

```php
$query = $this->nodeStorage->getQuery()
  ->accessCheck(FALSE)
  ->condition('type', 'article')
  ->condition('title', $name)
  ->count();

$count_nodes = $query->execute();

if ($count_nodes == 0) {
```

## Create an entity

To create a new entity instance, you can use the _Entity Type Manager_ (`entity_type.manager`) service. **NOTE** that this only creates an entity object and does not persist it. Use `->save()` to persist it.

```php
$node = \Drupal::entityTypeManager()->getStorage('node')->create([
  'title' => 'New Article',
  'body' => 'Article body',
  'type' => 'article',
));
```

Alternatively, use the static `create` method on the entity class directly.

```php
$node = Node::create(array(
  'title' => 'New Article',
  'body' => 'Article body',
  'type' => 'article',
));
```

## Save an entity

To save an entity, use the instance's `save` method.

```php
$node->save();
```

## Create article node entity with attached image

```php
use Drupal\node\Entity\Node;

  $data = file_get_contents('https://www.drupal.org/files/druplicon-small.png');
  $file = file_save_data($data, 'public://druplicon.png', FILE_EXISTS_RENAME);

  $node = Node::create([
    'type'        => 'article',
    'title'       => 'A new article',
    'field_image' => [
      'target_id' => $file->id(),
      'alt' => 'Drupal',
      'title' => 'Drupal logo'
    ],
  ]);
  assert($node->isNew(), TRUE);
  $node->save();
  assert($node->isNew(), FALSE);
```

## Update a node entity and add some terms

```php
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

$node = Node::load(4);
$term1 = Term::load(1);
$term2 = Term::load(2);
$node->field_tags->setValue([$term1, $term2]);
$node->save();
```


## Identify if an entity is a node

```php
    $entity = $this->routerMatch->getParameter('node');
    // Is this a node?
    if (!$entity instanceof NodeInterface) {
      return NULL;
    }
```


## Get the entity type and content type (or bundle type)

```php
use Drupal\Core\Entity\EntityInterface;

function hook_entity_presave(EntityInterface $entity) {
  // getEntityTypeId() returns 'node'.
  // $entity->bundle() returns 'article'.
  if($entity->getEntityTypeId() == 'node' && $entity->bundle() == 'article') {
    // Do your stuff here
  }
}
```

## Identify entities

from https://www.drupal.org/docs/drupal-apis/entity-api/working-with-the-entity-api

```php
// Make sure that an object is an entity.
if ($entity instanceof \Drupal\Core\Entity\EntityInterface) {
}

// Make sure it's a content entity.
if ($entity instanceof \Drupal\Core\Entity\ContentEntityInterface) {
}
// or:
if ($entity->getEntityType()->getGroup() == 'content') {
}

// Get the entity type or the entity type ID.
$entity->getEntityType();
$entity->getEntityTypeId();

// Make sure it's a node.
if ($entity instanceof \Drupal\node\NodeInterface) {
}

// Using entityType() works better when the needed entity type is dynamic.
$needed_type = 'node';
if ($entity->getEntityTypeId() == $needed_type) {
}
```


## Create a file entity

Note, Drupal is ok with files existing (for example, in sites/default/files)  without file entities but it probably makes more sense to have file entities connected with those files. When you create file entities, Drupal tracks the files in the tables: file_managed and file_usage. You can also create media entities where bundle='file'.

Once the file already exists, I write the file entity. I do need a mime type

```php

// Initialize $mimetype to a default value or a safe fallback
$mimetype = 'application/octet-stream'; // a general binary mimetype as a fallback

switch (strtoupper($type)){
  case 'PDF':
    $mimetype = 'application/pdf';
    break;
  case 'JPG':
    $mimetype = 'image/jpeg';
    break;
  case 'POW':
    $mimetype = 'application/vnd.ms-powerpoint';
    break;
}


// Create file entity.
$file = File::create();
$file->setFileUri($destination);
$file->setOwnerId(1);
$file->setMimeType($mimetype);
$file->setFileName($this->fileSystem->basename($final_destination));
$file->setPermanent();
$file->save();

$fid = $file->id();
return $fid;
```

## Entity Validation

This looks quite interesting, although I haven't had an opportunity to try it yet.

From [Entity Validation API overview](https://www.drupal.org/node/2015613)


The [entity validation
API](https://www.drupal.org/docs/drupal-apis/entity-api/entity-validation-api)
should be used to validate values on a node, like any other content entity. While that was already possible to define validation constraints for base fields and for a field type, there was no API to add constraints to a specific field. This has been addressed.

```php
/**
 * Implements hook_entity_bundle_field_info_alter().
 */
function mymodule_entity_bundle_field_info_alter(&$fields, \Drupal\Core\Entity\EntityTypeInterface $entity_type, $bundle) {
  if ($entity_type->id() == 'node' && !empty($fields['myfield'])) {
    $fields['myfield']->setPropertyConstraints('value', [
      'Range' => [
        'min' => 0,
        'max' => 32,
      ],
    ]);
  }
}
```

Custom validation constraints can also be defined.  These links may be useful:
* [ForumLeafConstraint](https://api.drupal.org/api/drupal/core%21modules%21forum%21src%21Plugin%21Validation%21Constraint%21ForumLeafConstraint.php/class/ForumLeafConstraint/8)
* [ForumLeafConstraintValidator](https://api.drupal.org/api/drupal/core%21modules%21forum%21src%21Plugin%21Validation%21Constraint%21ForumLeafConstraintValidator.php/class/ForumLeafConstraintValidator/8)

Also, from https://drupalize.me/tutorial/entity-validation-api?p=2792 (you need a paid membership to read the whole tutorial):
Drupal includes the [Symfony Validator
component](https://symfony.com/doc/2.8/components/validator.html), and
provides an Entity Validation API to assist in validating the values of fields in an entity. By using the Entity Validation API you can ensure that your validation logic is applied to Entity CRUD operations regardless of how they are triggered. Whether editing an Entity via a Form API form, or creating a new Entity via the REST API, the same validation code will be used.


## Link to a media entity

This code gives you a link on a node edit form (for `community_photo_edit` content type) which allows you to directly edit the referenced media entity (e.g., crop the image, add alt text, set the focal point).  Normally you would have to go search in the media library to find the media entity.

```php
/**
 * Implements hook_form_FORM_ID_alter() for node_community_photo_edit_form.
 *
 * @see _apc_calendar_community_photo_form_alter()
 */
function apc_calendar_form_node_community_photo_edit_form_alter(array &$form, FormStateInterface $form_state): void {

  // Link straight to the referenced media item's own edit form (crop, alt
  // text, focal point) -- editing a community_photo node otherwise gives no
  // way to fix those without leaving the form and hunting for the media
  // item in the library. Only shown when a photo is already selected and the
  // current user can actually edit it.
  /** @var \Drupal\node\NodeInterface $node */
  $node = $form_state->getFormObject()->getEntity();
  if ($node->hasField('field_photo_image') && !$node->get('field_photo_image')->isEmpty()) {
    $media = $node->get('field_photo_image')->entity;
    if ($media !== NULL && $media->access('update') && isset($form['field_photo_image'])) {
      $form['field_photo_image']['edit_media_link'] = [
        '#type' => 'link',
        '#title' => t('Edit this photo (crop, alt text, focal point)'),
        '#url' => $media->toUrl('edit-form', [
          'query' => ['destination' => Url::fromRoute('<current>')->toString()],
        ]),
        '#weight' => 100,
        '#attributes' => ['class' => ['button', 'button--small', 'apc-edit-media-link']],
      ];
    }
  }
}
```

## Delete attached media on a node when the node is deleted
In this instance, the node of type `community_photo` has a media entity attached via the `field_photo_image` field. When the node is deleted, we want to also delete the attached media entity and its file, ensuring that no orphaned media or files remain.
Drupal never does this by default since in general the referenced entity could be shared
 elsewhere, but in this instance, we know there is only 1 media entity attached via the `field_photo_image` field.  If we don't do this, deleting the node orphans the media and its file.



```php
/**
 * Implements hook_ENTITY_TYPE_delete() for node entities.
 */
function apc_calendar_node_delete(NodeInterface $node): void {
  if ($node->bundle() !== 'community_photo') {
    return;
  }
  if (!$node->hasField('field_photo_image') || $node->get('field_photo_image')->isEmpty()) {
    return;
  }

  $media = $node->get('field_photo_image')->entity;
  if (!$media) {
    return;
  }

  // Is some OTHER node still pointing at this same media entity?
  $other_usage = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
    ->accessCheck(FALSE)
    ->condition('type', 'community_photo')
    ->condition('field_photo_image', $media->id())
    ->condition('nid', $node->id(), '<>')
    ->range(0, 1)
    ->execute();
  if ($other_usage) {
    return;
  }

  // Capture the file entity before the media entity is deleted --
  // deleting the media entity decrements the file's usage count.
  $file = $media->hasField('field_media_image') ? $media->get('field_media_image')->entity : NULL;
  $media->delete();

  // If the list isn't empty now, some other media entity has its own separate usage row for
  // this same file (e.g. two Image media entities that both ended up
  // pointing at the same underlying file), and it isn't this hook's file to
  // delete.
  if ($file && empty(\Drupal::service('file.usage')->listUsage($file))) {
    $file->delete();
  }
}
```


## Resources

- [Entity API on Drupal.org updated Jan 2021](https://www.drupal.org/docs/drupal-apis/entity-api)
- [Introduction to Drupal Entity API from Drupal.org updated Sep 2022](https://www.drupal.org/docs/drupal-apis/entity-api/introduction-to-entity-api-in-drupal-8)
- [Drupal entity API cheat sheet Updated July 2018](https://drupalsun.com/zhilevan/2018/07/21/drupal-entity-api-cheat-sheet)
- [Drupal 8 Entity query API cheat sheet by Keith Dechant , Software Architect - updated February 2021](https://www.metaltoad.com/blog/drupal-8-entity-api-cheat-sheet)
- [Entity Validation API tutorial from <https://drupalize.me> (requires paid membership to read the whole tutorial) updated February 2022 at](https://drupalize.me/tutorial/entity-validation-api?p=2792)
- [Dynamic Entity Reference Module which allows you to create a single field to hold references to Users and Nodes, Or Terms and Nodes, or all three.](https://www.drupal.org/project/dynamic_entity_reference)

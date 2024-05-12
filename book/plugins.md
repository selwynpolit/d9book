---
title: Plugins
---

# Plugins and the Plugin API
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=plugins.md)


## Custom field type

A custom field type is a new type of field that can be added to an entity. For example, a field that stores both first and last name.  

### Which to use: custom field type or custom entity?

So which should you use? A custom field type or a custom entity?  If you were considering storing recipes where each ingredient has a name, quantity, and unit (of measurement i.e. ounce, cup, pinch etc.), you could use a custom field type to store the ingredient data. 

If the ingredient data is always going to be a simple part of a recipe and won't be used outside that context, a custom field might be simpler and more efficient.

A custom entity type (or maybe even a node) makes sense if you want to reference the ingredients from multiple places, have a lot of associated data or behaviors.



### Source code examples you can peruse
Examples of custom fields are visible below. 

- [From the Drupal Examples module field_example](https://git.drupalcode.org/project/examples/-/tree/4.0.x/modules/field_example?ref_type=heads)


Please consider supporting these authors by purchasing their excellent books.  Links to [these books are available here](learn.md/#books).:

- [From the book: Drupal 10 Development Cookbook - recipe3](https://github.com/PacktPublishing/Drupal-10-Development-Cookbook/tree/main/chp08/recipe4/mymodule)
- [From the book: Drupal 9 Module Development - license plate example](https://github.com/PacktPublishing/Drupal-9-Module-Development-Third-Edition/tree/master/packt/chapter9/license_plate)
- [From the book: Drupal 10 Module Development - license plate example](https://github.com/PacktPublishing/Drupal-10-Module-Development-Fourth-Edition/tree/main/chapter09/license_plate)


Custom field types e.g. `~/Sites/ddev102/web/modules/custom/test/src/Plugin/Field/FieldType/Realname.php` require both a widget (for entering data into the custom field) and a formatter plugin for displaying the data in your custom field. If your custom field type requires configuration by the site builder, you will also need to a schema yml file e.g. `~/Sites/field_example/config/schema/field_example.schema.yml` to tell Drupal how to store that configuration.

:::tip Note.
For information on [configuration schema/metadata on drupal.org](https://www.drupal.org/node/1905070)
and for a [cheat sheet on configuration schema/metadata on drupal.org](https://www.drupal.org/files/ConfigSchemaCheatSheet2.0.pdf)
:::


There can be multiple field widgets and formatters for a field type. Check out all the field types, widgets, formatters in the Smart Date module:

![Smart date files](/images/smart-date-files.png)


### Scaffolding code with Drush

Use `drush generate plugin:field_type` to generate a new field type plugin in the `test` module like this:

```bash
drush generate plugin:field:type

 Welcome to field-type generator!
––––––––––––––––––––––––––––––––––

 Module machine name:
 ➤ test

 Plugin label:
 ➤ RealName

 Plugin ID [test_realname]:
 ➤

 Plugin class [RealnameItem]:
 ➤ Realname

 Make the field storage configurable? [No]:
 ➤

 Make the field instance configurable? [No]:
 ➤

 The following directories and files have been created or updated:
–––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/config/schema/test.schema.yml
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/Plugin/Field/FieldType/Realname.php
```

#### Annotation

In the `~/Sites/ddev102/web/modules/custom/test/src/Plugin/Field/FieldType/Realname.php` the required annotation can look like this:

```php
/**
 * Defines the 'realname' field type.
 *
 * @FieldType(
 *   id = "realname",
 *   label = @Translation("RealName"),
 *   description = @Translation("Real name - includes first and last."),
 *   category = @Translation("General"),
 *   default_widget = "default_realname",
 *   default_formatter = "default_realname_formatter",
 * )
 */
```
or another example from the [examples module](https://www.drupal.org/project/examples): 

```php
/**
 * Plugin implementation of the 'field_example_rgb' field type.
 *
 * @FieldType(
 *   id = "field_example_rgb",
 *   label = @Translation("Example Color RGB"),
 *   module = "field_example",
 *   description = @Translation("Demonstrates a field composed of an RGB color."),
 *   default_widget = "field_example_text",
 *   default_formatter = "field_example_simple_text"
 * )
 */
```

#### Base class

The class should extend `FieldItemBase` (which implements the `FieldItemInterface` interface.). 

#### Complete example

Here is `web/modules/contrib/examples/modules/field_example/RgbItem.php` file from the [examples module](https://www.drupal.org/project/examples). It defines a new field type called 'Example Color RGB' and provides a widget and formatter for that field type.

```php
<?php

declare(strict_types=1);

namespace Drupal\field_example\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'field_example_rgb' field type.
 *
 * @FieldType(
 *   id = "field_example_rgb",
 *   label = @Translation("Example Color RGB"),
 *   module = "field_example",
 *   description = @Translation("Demonstrates a field composed of an RGB color."),
 *   default_widget = "field_example_text",
 *   default_formatter = "field_example_simple_text"
 * )
 */
class RgbItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'text',
          'size' => 'tiny',
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Hex value'));

    return $properties;
  }

}
```

#### Required methods

The class should implement the following methods:
- `schema()` - Defines the database API schema so Drupal knows how to store the field type in the database. You can define indexes here as well.
  
- `propertyDefinitions()` - Returns the data definition of the field type. This method should return an array of properties that the field type has. Each property should be an instance of `DataDefinition`. The key of the array should be the name of the property and the value should be the `DataDefinition` object.

- `mainPropertyName()` - Returns the name of the main property of the field type. So if your field type has a property called `value`, this method should return `value`. If there are multiple properties, you can return the name of the first property.

- `isEmpty()` - Checks if any of the fields are empty which stops the value from being saved to the database if the required info isn't entered.

- `getConstraints()` - Allows you to define constraints for the field type. This is optional.

- `generateSampleValue()` - This is optional, but can be useful for testing. It generates a random value for the field type.

Check out a complete field type plugin at [Drupal 10 development cookbook repo](https://github.com/PacktPublishing/Drupal-10-Development-Cookbook/blob/main/chp08/recipe4/mymodule/src/Plugin/Field/FieldType/RealName.php). Consider buying [Matt Glaman and Kevin Quillen's Drupal 10 Development Cookbook. Published in Feb 2023](https://amzn.to/3SuU18j) to support the authors.

I implemented the isEmpty() method in the Realname.php file. Here is the code:
```php
  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $first_name = $this->get('first_name')->getValue();
    $last_name = $this->get('last_name')->getValue();
    if (empty($first_name) && empty($last_name)) {
      return TRUE;
    }
    return FALSE;
  }
```

I also implemented the `getConstraints()` method:

```php
  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    $constraint_manager = $this->getTypedDataManager()->getValidationConstraintManager();

    // @DCG Suppose our value must not be longer than 10 characters.
    $options['first_name']['Length']['max'] = 10;

    // @DCG
    // See /core/lib/Drupal/Core/Validation/Plugin/Validation/Constraint
    // directory for available constraints.
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    return $constraints;
  }
```

Copilot suggested this version, but I haven't tried it yet:

```php
  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', [
      'first_name' => [
        'Length' => [
          'max' => 255,
          'maxMessage' => t('The first name may not be longer than @max characters.', ['@max' => 255]),
        ],
      ],
      'last_name' => [
        'Length' => [
          'max' => 255,
          'maxMessage' => t('The last name may not be longer than @max characters.', ['@max' => 255]),
        ],
      ],
    ]);
    return $constraints;
  }
```



:::tip Note
For more info on what goes in the `schema()` method, check out [Schema API on drupal.org](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Database%21database.api.php/group/schemaapi).
:::

 
### Custom field widget
The widget is the form element that is used to edit the field. The widget is responsible for converting the field value to a form element and back again. The widget is defined in a plugin class that extends `WidgetBase` and is annotated with `@FieldWidget`.


### Scaffolding code with Drush

```bash
drush generate plugin:field:widget

 Welcome to field-widget generator!
––––––––––––––––––––––––––––––––––––

 Module machine name:
 ➤ test

 Plugin label:
 ➤ RealName

 Plugin ID [test_realname]:
 ➤ realname

 Plugin class [RealnameWidget]:
 ➤

 Make the widget configurable? [No]:
 ➤

 Would you like to inject dependencies? [No]:
 ➤

 The following directories and files have been created or updated:
–––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/Plugin/Field/FieldWidget/RealnameWidget.php
```

Check out a complete field widget plugin at [Drupal 10 development cookbook repo](https://github.com/PacktPublishing/Drupal-10-Development-Cookbook/blob/main/chp08/recipe3/mymodule/src/Plugin/Field/FieldWidget/RealNameDefaultWidget.php)

Note the widget in that example has one small problem.  After you enter the first and last name and save the node, when you go to edit, it doesn't load it back up.  This is a small problem with the formElement function in `RealnameWidget.php`.  The problem is the default value is set to `''` which means instead of loading the data from the custom field, it will always show blank.

Here is a fixed version of that function:

```php
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element['first_name'] = [
      '#type' => 'textfield',
      '#title' => t('First name'),
      '#default_value' => $items[$delta]->first_name ?? '',
      '#size' => 25,
      '#required' => $element['#required'],
    ];
    $element['last_name'] = [
      '#type' => 'textfield',
      '#title' => t('Last name'),
      '#default_value' => $items[$delta]->last_name ?? '',
      '#size' => 25,
      '#required' => $element['#required'],
    ];
    return $element;
  }
  ```
  


### Custom Field Formatter
Field formatters are used to display your custom fields in display modes or in views. 


### Scaffolding code with Drush

```bash
drush generate plugin:field:formatter

 Welcome to field-formatter generator!
–––––––––––––––––––––––––––––––––––––––

 Module machine name:
 ➤ test

 Plugin label:
 ➤ Real Name Formatter

 Plugin ID [test_real_name_formatter]:
 ➤ realname

 Plugin class [RealnameFormatter]:
 ➤

 Make the formatter configurable? [No]:
 ➤

 The following directories and files have been created or updated:
–––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/Plugin/Field/FieldFormatter/RealnameFormatter.php
 ```






### Field Formatter example

Field formatters are used to display your custom fields in display modes or in views. 

Some basic info is available at [Create a custom field formatter on drupal.org updated Aug 2023](https://www.drupal.org/docs/8/creating-custom-modules/create-a-custom-field-formatter)

This example is a custom formatter that takes a value from a field (in
this case a uuid) and builds a url which essentially retrieves an image
(via an API call.) It looks for some config info (in the node display
mode for the node, or in the views setup for the usage in a view.).

For the node called `infofeed`, the config data is stored in an entity
called `core.entity_view_display.node.infofeed.default`

For a view called `infofeeds`, the config data is stored in a config
entity called `views.view.infofeeds`.

(You can find them by browsing thru the `config` table and looking for
your info in the data field i.e. in Sequel Ace, look for data like
`%image_width%` )

It is pretty reasonable that the custom field formatter will require some configuration, so this means we will need a `module/config/schema/module.schema.yml` file

So at
`~/Sites/ncs/docroot/modules/custom/ncs_infoconnect/config/schema/ncs_infoconnect.schema.yml`
we have the following file which defines a `config_entity` called NCS
Thumbnail settings and specifically two integer values for image_width
and image height. I use these to specify the size of the thumbnail I
generate:

```yml
# Schema for configuring NCS thumbnail formatter.

field.formatter.settings.ncs_thumbnail:
  type: config_entity
  label: 'NCS thumbnail settings'
  mapping:
    image_width:
      label: 'Image width'
      type: integer
    image_height:
      label: 'Image Height'
      type: integer
```

I create the `fieldformatter` as a fairly unexciting plugin at
`~/Sites/ncs/docroot/modules/custom/ncs_infoconnect/src/Plugin/Field/FieldFormatter/NcsThumbnailFormatter.php`

The annotation shows what will be seen in Drupal when configuring the
formatter.

```php
<?php

namespace Drupal\ncs_infoconnect\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;

/**
 * Plugin implementation of the 'ncs_thumbnail' formatter.
 *
 * @FieldFormatter(
 *   id = "ncs_thumbnail",
 *   label = @Translation("NCS Thumbnail"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class NcsThumbnailFormatter extends FormatterBase {
```

I override the `settingsSummary()` which is mostly informative, and `viewElements()` which has the meat of the plugin. In `viewElements()`, we loop thru the items (i.e. the values coming in from the field) and build an `image_uri`, jam each one into a render element and return the bunch.

```php
  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Specify size of the thumbnail to display.');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $markup = "";
    $width = $this->getSetting('image_width');
    $height = $this->getSetting('image_height');
    $ncs_auth_settings = Settings::get('ncs_api_auth', []);
    $base_url = $ncs_auth_settings['default']['imageserver'];

    foreach ($items as $delta => $item) {
      $image_uri = $base_url . "/?uuid=" . $item->value . "&function=original&type=thumbnail";
      $markup = '<img src="' . $image_uri . '" width="' . $width . '" height="' . $height . '">';

      // Render each element as markup.
      $elements[$delta] = [
        '#markup' => $markup,
      ];
    }

    return $elements;
  }
```

:::tip Note. 
Retrieving the config settings for a particular situation happens with a call to `getSetting()` as in:

```php
$width = $this->getSetting('image_width');
$height = $this->getSetting('image_height');
```
:::

To use this we need to edit the display for the `infofeed` content type, make sure we have the `image_uuid` field displayed (i.e. not disabled) for Format, select NCS Thumbnail, click the gear to the right to specify the thumbnail size and save. Displaying nodes will then include the thumbnails.

You can do the same with a view: Add the field, specify the formatter (and dimensions) and the thumbnail will appear.


## List site plugins with drush

This lists all the `plugin manager services` with the word `plugin` in the name so it may be slightly incomplete.

```bash
drush ev 'foreach (\Drupal::getContainer()->getServiceIds() as $id) { $plugin_manager_service[$id] = is_object(\Drupal::service($id)) ? get_class(\Drupal::service($id)) : ""; } dump($plugin_manager_service);' | grep plugin
```

This outputs something like:
```
  "plugin.manager.link_relation_type" => "Drupal\Core\Http\LinkRelationTypeManager"
  "plugin_form.factory" => "Drupal\Core\Plugin\PluginFormFactory"
  "plugin.manager.entity_reference_selection" => "Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManager"
  "plugin.manager.block" => "Drupal\Core\Block\BlockManager"
  "plugin.manager.field.field_type" => "Drupal\Core\Field\FieldTypePluginManager"
  "plugin.manager.field.field_type_category" => "Drupal\Core\Field\FieldTypeCategoryManager"
  "plugin.manager.field.widget" => "Drupal\Core\Field\WidgetPluginManager"
  "plugin.manager.field.formatter" => "Drupal\Core\Field\FormatterPluginManager"
  "plugin.manager.archiver" => "Drupal\Core\Archiver\ArchiverManager"
  "plugin.manager.action" => "Drupal\Core\Action\ActionManager"
  "plugin.manager.menu.link" => "Drupal\Core\Menu\MenuLinkManager"
  "plugin.manager.menu.local_action" => "Drupal\Core\Menu\LocalActionManager"
  "plugin.manager.menu.local_task" => "Drupal\Core\Menu\LocalTaskManager"
  "plugin.manager.menu.contextual_link" => "Drupal\Core\Menu\ContextualLinkManager"
  "plugin.manager.display_variant" => "Drupal\Core\Display\VariantManager"
  "plugin.manager.queue_worker" => "Drupal\Core\Queue\QueueWorkerManager"
```

You can use the `plugin service manager` to find out more about the plugins it manages. For example, to find the plugin types managed by the block plugin manager (as well as their class names) you can use the following drush command:

```bash
drush ev 'dump(\Drupal::service("plugin.manager.block")->getDefinitions());'
```
This reports that there are 357 block plugins on the site. The output is too long to show here, but here is a snippet:

```bash
^ array:357 [
  "announce_block" => array:6 [
    "admin_label" => Drupal\Core\StringTranslation\TranslatableMarkup^ {#1917 …5}
    "category" => "Announcements"
    "context_definitions" => []
    "id" => "announce_block"
    "class" => "Drupal\announcements_feed\Plugin\Block\AnnounceBlock"
    "provider" => "announcements_feed"
  ]
  "block_content:594d4405-36de-4883-a90d-0fdf8694ec24" => array:9 [
    "class" => "Drupal\block_content\Plugin\Block\BlockContentBlock"
    "provider" => "block_content"
    "id" => "block_content"
    "deriver" => "Drupal\block_content\Plugin\Derivative\BlockContent"
    "admin_label" => "AI hand"
    "category" => Drupal\Core\StringTranslation\TranslatableMarkup^ {#2166 …5}
    "context_definitions" => []
    "forms" => []
    "config_dependencies" => array:1 [ …1]
  ]
  "block_play_test_block1" => array:6 [
    "admin_label" => Drupal\Core\StringTranslation\TranslatableMarkup^ {#2044 …5}
    "category" => Drupal\Core\StringTranslation\TranslatableMarkup^ {#2076 …5}
    "context_definitions" => []
    "id" => "block_play_test_block1"
    "class" => "Drupal\block_play\Plugin\Block\TestBlock1Block"
    "provider" => "block_play"
  ]
  "devel_switch_user" => array:6 [
    "admin_label" => Drupal\Core\StringTranslation\TranslatableMarkup^ {#2132 …5}
    "category" => "Devel"
    "context_definitions" => []
    "id" => "devel_switch_user"
    "class" => "Drupal\devel\Plugin\Block\SwitchUserBlock"
    "provider" => "devel"
  ]
  ```


I haven't tried this, but Gemini AI suggested using this code to list all the plugin types on the site:

```php
<?php

use Drupal\Component\Plugin\Discovery\CachedDiscoveryInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

$plugin_types = [];

$discovery = \Drupal::service('plugin.discovery');
$managers = $discovery->getDefinitions();

foreach ($managers as $plugin_type => $definition) {
  if (is_subclass_of($definition['class'], PluginManagerInterface::class)) {
    $plugin_types[] = $plugin_type;
  }
}

print_r($plugin_types);
```

I'm curious to see if it works.


## Figuring out the annotation for a plugin

Annotations are documented in Drupal by providing an empty class in the `Drupal\{module}\Annotation` namespace which implements `\Drupal\Component\Annotation\AnnotationInterface`, and adding a `@docblock` that contains the `@Annotation` annotation. 

To find the annotation for a plugin, simply use `command` + `shift` + `O` in PhpStorm to open a file (make sure `files` is selected) and then type `FieldFormatter` or whatever the plugin type is. Select the file that is in the `Annotation` namespace. i.e. in a directory like `web/core/lib/Drupal/Core/Field/Annotation/...`.

![search for annotation](/images/find-annotation.png)

you can search for the class name in the module's codebase. e.g. in `Usage in comments` in PhpStorm, I found `web/core/lib/Drupal/Core/Field/Annotation/FieldFormatter.php`. That class name also corresponds to the annotation itself. In the case of `@FieldFormatter`, search for `FieldFormatter in the \Annotation namespace. 


From there you'll find the following documentation that lets you know the field_types keys in the annotation is an array of field types. And field types are the ids of the @FieldType plugins.

Here is that file: 
  
```php
  <?php

namespace Drupal\Core\Field\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a FieldFormatter annotation object.
 *
 * Formatters handle the display of field values. They are typically
 * instantiated and invoked by an EntityDisplay object.
 *
 * Additional annotation keys for formatters can be defined in
 * hook_field_formatter_info_alter().
 *
 * @Annotation
 *
 * @see \Drupal\Core\Field\FormatterPluginManager
 * @see \Drupal\Core\Field\FormatterInterface
 *
 * @ingroup field_formatter
 */
class FieldFormatter extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the formatter type.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * A short description of the formatter type.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * The name of the field formatter class.
   *
   * This is not provided manually, it will be added by the discovery mechanism.
   *
   * @var string
   */
  public $class;

  /**
   * An array of field types the formatter supports.
   *
   * @var array
   */
  public $field_types = [];

  /**
   * An integer to determine the weight of this formatter.
   *
   * Weight is relative to other formatter in the Field UI when selecting a
   * formatter for a given field instance.
   *
   * This property is optional and it does not need to be declared.
   *
   * @var int
   */
  public $weight = NULL;

}
```

So the annotation for a block plugin might look something like this:

```php
/**
 * Provides a test block1 block.
 *
 * @Block(
 *   id = "block_play_test_block1",
 *   admin_label = @Translation("test block1"),
 *   category = @Translation("Custom"),
 * )
 */
```

Each of the fields e.g. `id`, `admin_label`, and `category` are defined in the annotation class. The `@Block` annotation is defined in `web/core/lib/Drupal/Core/Block/Annotation/Block.php` which is shown below.  Notice how each public variable in the annotation class corresponds to a key in the annotation.

```php
<?php

namespace Drupal\Core\Block\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Block annotation object.
 *
 * @ingroup block_api
 *
 * @Annotation
 */
class Block extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The administrative label of the block.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $admin_label = '';

  /**
   * The category in the admin UI where the block will be listed.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $category = '';

  /**
   * An array of context definitions describing the context used by the plugin.
   *
   * The array is keyed by context names.
   *
   * @var \Drupal\Core\Annotation\ContextDefinition[]
   */
  public $context_definitions = [];

}
```
More at [Annotation based plugins on drupal.org updated May 2023](https://www.drupal.org/docs/drupal-apis/plugin-api/annotations-based-plugins)



## Generate plugs with drush
Each of the following can be used to generate a plugin of the specified type. e.g. `drush generate plugin:block`

- [plugin:action - Generates action plugin](https://www.drush.org/latest/generators/plugin_action/)
- [plugin:block - Generates block plugin](https://www.drush.org/latest/generators/plugin_block/)
- [plugin:ckeditor - Generates CKEditor plugin](https://www.drush.org/latest/generators/plugin_ckeditor/)
- [plugin:condition - Generates condition plugin](https://www.drush.org/latest/generators/plugin_condition/)
- [plugin:constraint - Generates constraint plugin](https://www.drush.org/latest/generators/plugin_constraint/)
- [plugin:entity-reference-selection - Generates entity reference selection plugin](https://www.drush.org/latest/generators/plugin_entity_reference_selection/)
- [plugin:field-formatter - Generates field formatter plugin](https://www.drush.org/latest/generators/plugin_field_formatter/)
- [plugin:field-type - Generates field type plugin](https://www.drush.org/latest/generators/plugin_field_type/)
- [plugin:field-widget - Generates field widget plugin](https://www.drush.org/latest/generators/plugin_field_widget/)
- [plugin:filter - Generates filter plugin](https://www.drush.org/latest/generators/plugin_filter/)
- [plugin:menu-link - Generates menu link plugin](https://www.drush.org/latest/generators/plugin_menu_link/)
- [plugin:migrate-destination - Generates migrate destination plugin](https://www.drush.org/latest/generators/plugin_migrate_destination/)
- [plugin:migrate-process - Generates migrate process plugin](https://www.drush.org/latest/generators/plugin_migrate_process/)
- [plugin:migrate-source - Generates migrate source plugin](https://www.drush.org/latest/generators/plugin_migrate_source/)
- [plugin:queue-worker - Generates queue worker plugin](https://www.drush.org/latest/generators/plugin_queue_worker/)
- [plugin:rest-resource - Generates REST resource plugin](https://www.drush.org/latest/generators/plugin_rest_resource/)
- [plugin:views:argument-default - Generates views default argument plugin](https://www.drush.org/latest/generators/plugin_views_argument_default/)
- [plugin:views:field - Generates views field plugin](https://www.drush.org/latest/generators/plugin_views_field/)
- [plugin:views:style - Generates views style plugin](https://www.drush.org/latest/generators/plugin_views_style/)



## The Basics

Plugins are small swappable pieces of functionality. Plugins of the same plugin type, perform similar functionality.

Drupal contains many different plugins, of different types. For example, `Field widget` is a plugin type. You can select from the list of field widget plugins to set the widget that a field uses.

The Drupal plugin system provides a set of guidelines and reusable code components to allow developers to expose pluggable components within their code and support managing these components through the user interface (as needed).

Plugins are defined in modules: a module may provide plugins of different types, and different modules may provide their own plugins of a particular type.

-  [Plugin API overview on drupal.org updated Mar 2021](https://www.drupal.org/docs/drupal-apis/plugin-api/plugin-api-overview)
- [Drupal 8 Plugins Explained by Joe Schindlar of Drupalize.me - Jul 2014](https://drupalize.me/blog/drupal-8-plugins-explained)

Some of the plugin types provided by Core are:

- Blocks (see */src/Plugin/Block/* for many examples)
- Field formatters, Field widgets (see */src/Plugin/Field/* for many examples)
- All Views plugins (see */src/Plugin/views/* for many examples)
- Conditions (used for Block visibility in the core)
- Migrate source, process & destination plugins



### Additional plugin types
- Render element type
- Field Types
- Custom Content Entities
- Configuration Entities



### Creating new Plugin types

You should define new plugin types if you need multiple configurable features and you expect others to provide new functionality without changing your module. A plugin manager is the centralized controlling class that defines how the plugins of each type will be discovered and instantiated. This class is called directly in any module wishing to invoke a plugin type. When you create a new plugin manager, you also create a new plugin type.

Use `drush generate plugin:plugin_manager` to generate a new plugin type.

```bash
drush generate plugin:manager

 Welcome to plugin-manager generator!
––––––––––––––––––––––––––––––––––––––

 Module machine name:
 ➤ test

 Module name [Test]:
 ➤

 Plugin type [test]:
 ➤ sandwich

 Discovery type [Annotation]:
  [1] Annotation
  [2] Attribute
  [3] YAML
  [4] Hook
 ➤ 1

 The following directories and files have been created or updated:
–––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/test.services.yml
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/SandwichInterface.php
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/SandwichPluginBase.php
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/SandwichPluginManager.php
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/Annotation/Sandwich.php
 • /Users/selwyn/Sites/ddev102/web/modules/custom/test/src/Plugin/Sandwich/Foo.php
 ```

For an example of a plugin type, look in the [examples module](https://www.drupal.org/project/examples) at the `web/modules/contrib/examples/modules/plugin_type_example` directory. This module defines a new plugin type called 'Sandwich' and provides 2 example plugins of that type: `ExampleHamSandwich.php` and `ExampleMeatballSandwich.php`.

These consist of:
- An interface for the plugin type e.g. `web/modules/contrib/examples/modules/plugin_type_example/src/SandwichInterface.php`
- A service definition for the plugin manager in the module's `services.yml` file e.g. `web/modules/contrib/examples/modules/plugin_type_example/plugin_type_example.services.yml`
- A [plugin manager](https://api.drupal.org/api/drupal/core%21modules%21system%21tests%21modules%21lazy_route_provider_install_test%21src%21PluginManager.php/class/PluginManager/11.x) file e.g. `web/modules/contrib/examples/modules/plugin_type_example/src/SandwichPluginManager.php`
- The annotation class for the plugin e.g. `web/modules/contrib/examples/modules/plugin_type_example/src/Annotation/Sandwich.php`
- One or more example plugins to show how to use the plugin type e.g. `web/modules/contrib/examples/modules/plugin_type_example/src/Plugin/Sandwich/ExampleHamSandwich.php`
- a base class (so others can extend it) e.g. `web/modules/contrib/examples/modules/plugin_type_example/src/SandwichBase.php`


- The Base Class is the class that all plugins of a particular type extend. Usually `PluginBase` or a subclass of `PluginBase`.
- The Plugin Manager is responsible for discovering, instantiating, and managing plugins of a particular type.
- The Services Definition identifies the plugin id (name) and the class for the plugin manager service.


#### Common plugin types include:
- Block: Provides a block that can be placed in a region.
- Field Formatter: Formats the display of a field.
- Field Type: Defines a new field type e.g. a field that stores both first and last name.
- Field Widget: Provides a form element for editing a field.
- Filter: Filters text input.
- Menu Link: Provides a menu link.
- Menu local task: Provides a tab (also called a local task) on a page.
- Views field: Provides a field for a Views.
- Views filter: Provides a filter option for a Views.



## Resources
- [Annotation based plugins on drupal.org updated May 2023](https://www.drupal.org/docs/drupal-apis/plugin-api/annotations-based-plugins)
- [Drupal blocks in the user interface on Drupal.org updated Feb 2023](https://www.drupal.org/docs/user_guide/en/block-concept.html)
- [Block API overview on Drupal.org updated April 2023](https://www.drupal.org/docs/drupal-apis/block-api/block-api-overview)
- [Plugin API overview on Drupal.org updated Mar 2021](https://www.drupal.org/docs/drupal-apis/plugin-api/plugin-api-overview)
- [Drupal Blocks API](https://api.drupal.org/api/drupal/core%21modules%21block%21block.api.php/group/block_api/10)
- [Programatically creating a block in Drupal 9 - Dec 2021](https://www.specbee.com/blogs/programmatically-creating-block-in-drupal-9)
- [How to Create a Custom Block in Drupal 8/9/10 Oct 2022](https://www.agiledrop.com/blog/how-create-custom-block-drupal-8-9-10)
- [Drupal 8 Plugins Explained by Joe Schindlar of Drupalize.me - Jul 2014](https://drupalize.me/blog/drupal-8-plugins-explained)

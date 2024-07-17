---
title: Views
---

# Views
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=views.md)

## Change the view title

To change the title of the view, in a `.module` file, you can use this code:

```php
function views_play_views_pre_view(\Drupal\views\ViewExecutable $view, $display_id, array $args) {
  // Check if the view is the one we want to alter.
  if ($view->id() === 'blurbs' && $display_id === 'page_1') {
    $user = \Drupal::currentUser();
    if ($user->hasRole('administrator')) {
      // Returns a Drupal\views\Plugin\views\display\Page object for a page view.
      $display = $view->getDisplay();
      $display->setOption('title', 'Hello, Administrator!');
    }
  }
}
```

## Change contextual filters value

Assuming the URL `https://ddev102.ddev.site/blurbs/green` where green is the value you are passing to the contextual filter, you could change it to rather use `red` with the following code:

```php
/**
 * Implements hook_views_pre_view().
 *
 */
function views_play_views_pre_view(\Drupal\views\ViewExecutable $view, $display_id, array $args) {
  // Check if the view is the one we want to alter.
  if ($view->id() === 'blurbs' && $display_id === 'page_1') {
      $args[0] = 'red';
      $view->setArguments($args);
  }
}
```

## Disable an exposed filter
Assuming you have an exposed filter on a reference field called `field_section` you want to disable it, you can use `hook_views_pre_view()` to do the job:

```php
/**
 * Implements hook_views_pre_view().
 *
 */
function views_play_views_pre_view(\Drupal\views\ViewExecutable $view, $display_id, array $args) {
  // Check if the view is the one we want to alter.
  if ($view->id() === 'blurbs' && $display_id === 'page_1') {
      $filters = $view->getDisplay()->getOption('filters');
      // Disable exposed filter.
      $filters['field_section_target_id']['exposed'] = FALSE;
      $display->setOption('filters', $filters);
  }
}
```



## Template Preprocess views view
Using template_preprocess_views_view you customize the view by adding or modifying variables.  This is useful if you want to add a form to a view or add some other variable to the view.  See more at the [Drupal API link to function template_preprocess_views_view](https://api.drupal.org/api/drupal/core%21modules%21views%21views.theme.inc/function/template_preprocess_views_view/9.3.x)


### Example 1

This is used in a custom module at `/Users/selwyn/Sites/dir/web/modules/custom/dir/dir.module`. It adds a form `EventsByMonthForm` which can be displayed on the view using the TWIG template. In the code, the view and display are identified and the `$variables['events_by_month']` is added.

```php
/**
 * Implements template_preprocess_views_view().
 */
function dirt_preprocess_views_view(&$variables) {
  $view = $variables['view'];
  $view_name = $view->storage->id();
  $display_id = $view->current_display;
  if ($view_name == 'events_for_events_landing_page' && $display_id == 'upcoming') {
    $variables['events_by_month'] = \Drupal::formBuilder()->getForm('Drupal\dir\Form\EventsByMonthForm');
  }
}
```

### Example 2
This is a barebones example of how to modify a view using the `template_preprocess_views_view()` function.  This would be stored in a custom module or `.theme` file.  It modifies the view `events` by looping through the results and modifying each row as needed.

```php
function dirt_preprocess_views_view(array &$variables) {
  $view = $variables['view'];

  // Check if this is a specific view.
  if ($view->name == 'events') {
    foreach ($view->result as $r => $result) {
      // Modify each "row" as needed.
      // For example, you can access fields like $result->field_name.
      // Add your custom logic here.
    }
  }
  // You can handle other views here if needed.
}
```

### Example 3
This version looks up the venue reference field and replace the node ID with the venue reference field's title.

```php
function dirt_preprocess_views_view(array &$variables) {
  $view = $variables['view'];
  if ($view->name == 'events_landing_page') {
    foreach ($view->result as $r => $result) {
      $node = $result->_entity;
      $venue = $node->get('field_venue')->entity;

      // Replace the node ID with the venue reference field's title.
      $variables['rows'][$r]['#row']->nid = $venue->getTitle();
    }
  }
  // You can handle other views here if needed.
}
```


## Template Preprocess Views View Field
This is used to preprocess the output of a field in a view.  It is a little more complex than the view preprocess function, but not much. 

### Example 1

This function from  `/Users/selwyn/Sites/txglobal/web/themes/custom/txglobal/txglobal.theme` modifies the value of the `nid` field in the `news_events_search` view.  It does some magic based on the `nid` field which is in the view and builds some stuff


```php
/**
 * Implements template_preprocess_views_view_field().
 */
function txglobal_preprocess_views_view_field(&$variables) {

  $view = $variables["view"];
  $viewname = $view->id();
  $display = $variables["view"]->current_display;
  if ($viewname == 'news_events_search' && $display == 'page_events') {
    $field = $variables['field']->field;
    if ($field == 'nid') {
      $nid = $variables['output'];
      if ($nid) {
        $nid = $nid->__toString(); // Uh, it’s a Drupal render markup object- need string.
        $node = Node::load($nid);
        $node_type = $node->getType();
        $aofs = _txglobal_multival_ref_data($node->field_ref_aof, 'aof', 'target_id');
        $units = _txglobal_multival_ref_data($node->field_ref_unit, 'unit', 'target_id');
  ..

        $related_items = array_merge($topics, $aofs, $units,$countries);
        // New variable that will be readable in TWIG file
        $variables['related_items'] = $related_items;

        // Build a UL with a bunch of LI links in it
        $links = '<ul>';
        foreach ($related_items as $item) {
          $links = $links . '<li><a href="/events/search?' . $item['param_name'] . '=' . $item['id'] . '">' . $item['title'] . '</a></li>';
        }
        $links .= '</ul>';

        // Modify the actual output

        // This outputs only text, not HTML so...
        $variables['output'] = $links;
        // Render it so you get legit HTML
        $variables['output'] = \Drupal\Core\Render\Markup::create($links);

        // OR add new variable that will be readable in TWIG file
        $variables['related_items'] = \Drupal\Core\Render\Markup::create($links);

      }
    }

  }
}
```
Also of note here: To output the new variable `{{related_items}}` put that in the twig file e.g. `views-view-field--news-events-search--page-nid.html.twig` (as in `/Users/selwyn/Sites/txglobal/web/themes/custom/txglobal/templates/views/views-view--news-events-search--page-events.html.twig`). The variable is output like this:

```twig
{{ related_items }}
```

For completeness, here is the function that retrieves the values from the node and builds the array of related items.

```php
/**
 * Returns array of data for multivalue node reference fields.
 *
 * @param \Drupal\Core\Field\FieldItemListInterface $ref_field
 *   The entity reference field which we are building the links from.
 * @param string $param_name
 *   Parameter name to be passed as get value.
 * @param string $value_type
 *   Indicates which field to retrieve from database.
 * @param string $field_ref_type
 *   Variable to determine type of reference field.
 *
 * @return array
 *   Array of data.
 *
 * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
 * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
 */
function _txglobal_multival_ref_data(FieldItemListInterface $ref_field, $param_name, $value_type, $field_ref_type = 'node') {
  $values = [];
  $title = '';
  if ($ref_field) {
    foreach ($ref_field as $ref) {
      if ($field_ref_type == 'taxonomy') {
        $term = Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->load($ref->$value_type);
        if ($term) {
          $title = $term->getName();
        }
      }
      else {
        if ($value_type == 'value') {
          $title = $ref->$value_type;
        }
        else {
          if (isset($ref->entity->title->value)) {
            $title = $ref->entity->title->value;
          }
        }
      }
      $id = $ref->$value_type;
      $values[] = [
        'title' => $title,
        'id' => str_replace(' ', '+', $id),
        'param_name' => $param_name,
      ];
    }
  }
  return $values;
}
```

### Example 2

Another version of `~/Sites/txglobal/web/themes/custom/txglobal/txglobal.theme` which builds a `UL` of links based on the nid field in the view.  It also uses a helper function to build the links.  The helper function is in the same file and is called `build_related_items_links()`.

```php
/**
 * Implements template_preprocess_views_view_field().
 *
 * This function replaces the nid field in the view with
 * a UL of links.
 */
function txglobal_preprocess_views_view_field(&$variables) {
  $view = $variables["view"];
  $viewname = $view->id();
  $display = $variables["view"]->current_display;
  $displays = ['page_events', 'block_1', 'audience', 'program', 'unit'];
  if ($viewname == 'news_events_search' || $viewname == 'events_for_this_anything') {
    if (in_array($display, $displays)) {
      $field = $variables['field']->field;
      if ($field == 'nid') {
        $nid = $variables['output'];
        if ($nid) {
          $nid = $nid->__toString();
          $nid = intval($nid);
          $node = Node::load($nid);
          $related_items = build_related_items_links($node);

          // Build the UL of related links.
          $links = '<ul class="categories-block no-bullet">';
          foreach ($related_items as $item) {
            $links = $links . '<li><a href="/events/search?' . $item['param_name'] . '=' . $item['id'] . '">' . $item['title'] . '</a></li>';
          }
          $links .= '</ul>';
          // Render it so you get legit HTML.
          $variables['output'] = Markup::create($links);
        }
      }
    }
  }
}
```

Here is the helper function that builds the links:

```php
function build_related_items_links(Node $node) {
  $node_type = $node->getType();
  $collections = [];
  $programs = [];
  $topics = [];
  $aofs = [];
  $units = [];
  $audiences = [];
  $continents = [];
  $countries = [];
  if ($node->hasField('field_ref_aof')) {
    $aofs = $node->field_ref_aof->target_id ? _txglobal_multival_ref_data($node->field_ref_aof, 'aof', 'target_id') : [];
  }
  if ($node->hasField('field_ref_unit')) {
    $units = NULL !== $node->field_ref_unit->target_id ? _txglobal_multival_ref_data($node->field_ref_unit, 'unit', 'target_id') : [];
  }
  if ($node->hasField('field_ref_audience')) {
    $audiences = NULL !== $node->field_ref_audience->target_id ? _txglobal_multival_ref_data($node->field_ref_audience, 'audience', 'target_id') : [];
  }
  if ($node->hasField('field_continent')) {
    $continents = NULL !== $node->field_continent->value ? _txglobal_multival_ref_data($node->field_continent, 'continent', 'value', 'list') : [];
  }
  if ($node->hasField('field_ref_country')) {
    $countries = NULL !== $node->field_ref_country->target_id ? _txglobal_multival_ref_data($node->field_ref_country, 'country', 'target_id') : [];
  }
  if ($node->hasField('field_ref_programs')) {
    $programs = NULL !== $node->field_ref_programs->target_id ? _txglobal_multival_ref_data($node->field_ref_programs, 'program', 'target_id') : [];
  }
  if ($node->hasField('field_ref_program_collection')) {
    $collections = NULL !== $node->field_ref_program_collection->target_id ? _txglobal_multival_ref_data($node->field_ref_program_collection, 'collection', 'target_id') : [];
  }
  if ($node->hasField('field_ref_topic')) {
    $topics = NULL !== $node->field_ref_topic->target_id ? _txglobal_multival_ref_data($node->field_ref_topic, 'topic', 'target_id', 'taxonomy') : [];
  }
  $related_items = array_merge($topics, $aofs, $units, $audiences, $collections, $programs, $continents, $countries);
  return $related_items;
}
```
And the template

```twig
{%
  set classes = [
  dom_id ? 'js-view-dom-id-' ~ dom_id,
]
%}
<section class="content-section filter-search-form-section">
  {{ title_prefix }}
  {{ title }}
  {{ title_suffix }}
  {% set searchType = 'Events' %}
  {% include '@txglobal/partials/searchfilterform.html.twig' %}

  {% if header %}
    <header>
      {{ header }}
    </header>
  {% endif %}

  {{ exposed }}
  {{ attachment_before }}
  <div class="grid-container">
    <div class="grid-x grid-margin-x">
      {% if rows -%}
        {{ rows }}
      {% elseif empty -%}
        <div class="cell">
          {{ empty }}
        </div>
  
      {% endif %}
      {% if pager %}
        <div class="cell small-12">
          {{ pager }}
        </div>
      {% endif %}
  
    </div>
  </div>

  {{ attachment_after }}
  {{ more }}

  {% if footer %}
    <footer>
      {{ footer }}
    </footer>
  {% endif %}

  {{ feed_icons }}
</section>
```

### Example 3

This example has a view called `selwyntest3` and a display called `page_1`.  It modifies the `nid` field to output a specific value.  In this case, there were two nid fields. Views refers to them as  `nid` and `nid_1`.  This shows how to find the correct field and modify the output. It is from `~/Sites/tea/docroot/themes/custom/tea/tea.theme`.

```php{6-17}
function tea_preprocess_views_view_field(&$variables) {
  $view = $variables['view'];
  $view_name = $view->id();
  $display_name = $view->current_display;
  if ($view_name == 'selwyntest3' && $display_name == 'page_1') {
    $field_name = $variables['field']->field;
    if ($field_name == 'nid') {
      /** @var Drupal\views\Plugin\views\field\EntityField $entity_field */
      $entity_field = $variables['field'];
      /** @var Drupal\Component\Plugin\Definition\PluginDefinition $plugin */
      //$plugin = $entity_field->getPluginDefinition();
      $options = $entity_field->options;
      $view_field_machine_name = $options['id'];
      if ($views_field_machine_name == 'nid_1') {
        $program_nid = '825601';
        $variables['output'] = $program_nid;
      }
    }
  }
}
```

## Either Or in views

To show one field if it exists otherwise show another field is remarkably easy.

1. Add a field for `field1` and exclude it from display.
1. Add a field for `field2` and exclude it from display. 
1. Add a third field for `field2` again
1. In the rewrite results put the token for `field2`.  Eg. `[colorbox]`
1. In the `No results behavior`, put the token for `field1` e.g. `[field1]`.




## Views Query options

When views give you unexpected results that seem permissions related, you can check the query options.  This is a screenshot of the query options for a view.  You can see the query options by clicking on the `Advanced` link in the view and then in the `other` section, click on `Query settings`.  Sometimes it is useful to **disable SQL rewriting** as this bypasses security and returns the same data that you get as when you are logged in as user 1.

![Views query options](/images/views-query-options.png)

::: tip Note
The dialog will display this WARNING: Disabling SQL rewriting means that node access security is disabled. This may allow users to see data they should not be able to see if your view is misconfigured. Use this option only if you understand and accept this security risk.
:::

## Jump Menu
Using the [Views Jump Menu module](https://www.drupal.org/project/views_jump_menu) you can easily create a node driven no-code drop-down select box.  The instructions are a bit confusing.

After installing (`composer require 'drupal/views_jump_menu:^1.0@RC'`) and enabling the module, create a content type (e.g. `jump_items`) with a plain text field for a url.  Don't use a link field.  Create some `jump_items` nodes. e.g. 
1. Apple https://www.apple.com
2. Microsoft https://www.microsoft.com
3. Google https://www.google.com
4. Node 10 /node/10

::: tip Note
You can use external or internal URL's as in the example above where we used a relative url `/node/10`
:::

Create a view `jump1`. Add a block with a format of `Jump Menu`.  

![block format selection](/images/jump-menu1.png)

Add the fields that will be used for the jump menu.  I used `title` field and `link2` field.  
![jump menu view fields](/images/jump-menu3.png)

For the block format jump menu settings:
![jump menu settings](/images/jump-menu4.png)


specify the label field and url field.  Use the `title` and `link2` fields respectively.
![jump menu settings](/images/jump-menu2.png)

Now set the block to display where you want it and voila!  The drop-down select field will appear in it's block and you can select it and it will immediately load the associated page.

![jump menu open](/images/jump-menu6.png)


## Search multiple fields with a single search box

This core functionality allows a view to have an exposed filter that searches multiple fields for a given search term. It essentially allows you to combine multiple exposed search filters into a single search box.

To set it up include all the fields that you want to search in the Fields section, marking them with Exclude from display as necessary. Then, add and expose a `Combine fields` filter to the view, and configure it to use all the fields you want searchable in the Choose fields to combine for filtering section of the filter's configuration

Thanks to [Mike Anello's blog post outlining this useful feature.](https://www.drupaleasy.com/quicktips/reintroducing-drupal-cores-views-combine-fields-filter)


## See Views Query

To see the query that views generates, use the following code:

```php
use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_post_execute().
 */
function MY_MODULE_views_post_execute(ViewExecutable $view) {
  $channel = 'view_query:' . $view->id();
  $message = $view->query->query();
  \Drupal::logger($channel)->debug($message);
}
```

From Goran Nikolovski blog post [How to see your Drupal Views query - Mar 2024](https://gorannikolovski.com/snippet/how-to-see-your-drupal-views-query)

Alternatively, you could add this code to your theme's `THEME.theme` file.  This will output the query to the screen.  

```php
function THEME_views_pre_execute(\Drupal\views\ViewExecutable $view) {
  $query = $view->query;
  $query_string = (string) $query;
  \Drupal::logger('THEME')->notice($query_string);
}
```

## Set the title for a view programatically

Here is some code that gets the views arguments and sets the title based on the argument.  This is from a custom module.  The view is called `loaders` and the display is `loaders_list_block`.  The title is set to the state abbreviation.  If the argument is the same as the exception value, the title is not set.


```php

/**
 * Implements hook_views_pre_render().
 */
function my_module_views_pre_render(ViewExecutable $view) {
  $view_id = $view->id();
  switch ($view_id) {
    ///...
    case 'loaders':
      // Check the display.
      if ($view->current_display !== 'loaders_list_block') {
        break;
      }
      $title = $view->getTitle();
      $view_args = $view->args;
      if (is_array($view_args)) {
        $state_arg = $view_args[0];
        if ($state_arg == $view->argument['field_acronym_value_1']->options['exception']['value']) {
          break;
        }

        $view->setTitle($title . ' for ' . _get_taxonomy_term_by_abbreviation($state_arg));
      }
      break;
  }


// For completeness, here is the helper function that retrieves the taxonomy term by abbreviation.
function _get_taxonomy_term_by_abbreviation(?string $abbreviation): string {
  if (is_null($abbreviation) || empty($abbreviation) {
    return '';
  }
  $entity_query = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->getQuery()->accessCheck(FALSE);
  $entity_result = $entity_query->condition('vid', 'states_and_territories')
    ->condition('field_acronym', $abbreviation)
    ->execute();

  if (empty($entity_result)) {
    return '';
  }
  $term_entity = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load(array_pop($entity_result));
  return $term_entity?->label() ?? '';
}

```

## Update filters for a view programatically


The code below updates the content filters for a views display.

It begins with implementation of `hook_views_pre_view()` in the `.module` file.  

```php

/**
 * Implements hook_views_pre_view().
 */
function dod_views_views_pre_view(ViewExecutable $view, $display_id, array &$args) {

  // In order to update filters relevant to the referencing entity, we have to
  // figure out the referencing entity where the filter options are stored.
  // There is not enough information to update the view otherwise.
  $ref_data = $view->element['#viewsreference'] ?? $view->getRequest()->get('viewsreference');
  if (!empty($ref_data['parent_entity_id']) && !empty($ref_data['parent_entity_type'])) {
    $parent =  \Drupal::entityTypeManager()
      ->getStorage($ref_data['parent_entity_type'])
      ->load($ref_data['parent_entity_id']);
  }

  // We inject filter definitions in the list of existing filters. 
  // Note that the code below overrides any previously
  // defined filters for the given view.
  $filters = $view->getDisplay()->getOption('filters');

  switch ($view->id()) {
    case 'list_page__feeds':
      _dod_views_update_content_filters($filters, $parent);
      _dod_views_update_date_threshold_filter($filters, $parent);
  }
  ...
```


Below in the function `_dod_views_update_content_filters()` function, the value in the field `field_list_content_types` is added to the view\'s filter using `array_intersect`. The exposed filter is set to true if there is more than one content type (in that multivalue field) and the entity is a node.

The code after the comment `// Add/update taxonomy filters.` gets the term ids from the field `field_list_filter` and adds them to the views filter.

```php
/**
 * Updates content filters (taxonomy) for a view display.
 *
 * @param array $filters
 *   Array of views filters.
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   Entity (node) from which to pull term filters.
 */
function _dod_views_update_content_filters(array &$filters, EntityInterface $node):void {
  // Update type filter based on parent entity's selection.
  if (isset($filters['type']) && $node->hasField('field_list_content_types')) {
    $value = $node->get('field_list_content_types')->getValue();
    // Hide exposed filter when only a single content type is involved.
    if (!empty($value)) {
      $filters['type']['value'] = array_intersect(
        $filters['type']['value'],
        array_map(static function ($value) {
          return $value['value'];
        }, $value)
      );
    }
    // Show exposed only on the node level (e.g. not in paragraphs).
    $filters['type']['exposed'] = 1 !== count($value) && ($node instanceof Node);
  }

  // Add/update taxonomy filters.
  if ($entity->hasField('field_list_filter')) {
    $filter_tids = array_map(static function ($value) {
      return $value['target_id'];
    }, $entity->get('field_list_filter')->getValue());

    if (!empty($filter_tids)) {
      $filters['tid'] = dod_views_generate_view_filter('tid', $filter_tids);
    }
  }
```

Here is the dod_views_generate_view_filter function:

```php
/**
 * Generates a filter config for a given filter and value.
 *
 * Generated filter configurations for the views filters.
 * Views API doesn't seem to have a clear way to add filters.
 *
 * @param string $filter
 *   Type of filter to generate.
 * @param array $value
 *   Array representing the value of the filter.
 * @param string $op
 *   Optional operator type, defaults to 'or'.
 *
 * @return array
 *   The filter config.
 */
function dod_views_generate_view_filter($filter, $value, $op = 'or') {
  $filters = [
    'tid' => [
      'id' => 'tid',
      'table' => 'taxonomy_index',
      'field' => 'tid',
      'plugin_id' => 'taxonomy_index_tid',
      'reduce_duplicates' => TRUE,
      'hierarchy' => FALSE,
      'limit' => TRUE,
    ],
    'published_at' => [
      'id' => 'published_at',
      'table' => 'node_field_data',
      'field' => 'published_at',
      'plugin_id' => 'date',
      'entity_type' => 'node',
      'entity_field' => 'published_at',
    ],
  ];
  $shared_props = [
    'relationship' => 'none',
    'group_type' => 'group',
    'value' => $value,
    'operator' => $op,
    'group' => 1,
    'exposed' => FALSE,
    'is_grouped' => FALSE,
  ];

  return $shared_props + $filters[$filter];
}

```





## Reference

### Views API
Look in `core/modules/views/views.api.php` for all the hooks available for views along with examples of their usage. The file is also available online at [Drupal API Reference: views.api.php](https://git.drupalcode.org/project/drupal/-/blob/11.x/core/modules/views/views.api.php?ref_type=heads)

### hook_views_pre_view()
In [Drupal API Reference: hooks_views_pre_view](https://api.drupal.org/api/drupal/core%21modules%21views%21views.api.php/function/hook_views_pre_view/10) you can make significant changes to a view before it appears on a page. These include:
- Alter a view at the very beginning of Views processing.
- Add an attached view to the view by setting `$view->attachment_before` and `$view->attachment_after`.

::: tip Note
To make the changes appear on the page, you will notify the `$view` object or the `display` object using calls like: `$view->setArguments($args)` or `$view->setOption('title', $new_title)`. The `$view` object is passed as an argument, but you will have to retrieve the display object using `$display = $view->getDisplay()`.
:::


For example to add an argument (contextual filter value) use this code in a `.module` file:

```php
function hook_views_pre_view(ViewExecutable $view, $display_id, array &$args) {
    // Modify contextual filters for my_special_view if user has 'my special permission'.
    $account = \Drupal::currentUser();
    if ($view->id() == 'my_special_view' && $account->hasPermission('my special permission') && $display_id == 'public_display') {
        $args[0] = 'custom value';
        $view->setArguments($args);
    }
}
```

To change the title of the view, in a '.module' file, you can use this code:
```php
function views_play_views_pre_view(\Drupal\views\ViewExecutable $view, $display_id, array $args) {
  // Check if the view is the one we want to alter.
  if ($view->id() === 'blurbs' && $display_id === 'page_1') {
    $user = \Drupal::currentUser();
    if ($user->hasRole('administrator')) {
      // Returns a Drupal\views\Plugin\views\display\Page object for a page view.
      $display = $view->getDisplay();
      $display->setOption('title', 'Hello, Administrator!');
    }
  }
}
```

To make changes to filters in a view, retrieve the filters with `$filters = $view->getDisplay()->getOption('filters')`. This returns an array with an element corresponding to each filter defined in the view.
So the view with these filters defined:

![view filters](/images/view-filters.png)

Will return an array like this:

![filters array](/images/view-filters-in-debugger.png)

You can modify the filters array and set that back into the view for processing with `$display->setOption('filters', $filters)`.


### Links
- [Drupal API Reference: hook_views_pre_view](https://api.drupal.org/api/drupal/core%21modules%21views%21views.api.php/function/hook_views_pre_view/10)
- [Drupal API Reference: Template Preprocess views view](https://api.drupal.org/api/drupal/core%21modules%21views%21views.theme.inc/function/template_preprocess_views_view/9.3.x)
- [Drupal API Reference: Template Preprocess Views View Field](https://api.drupal.org/api/drupal/core%21modules%21views%21views.theme.inc/function/template_preprocess_views_view_field/9.3.x )
- [Building a Views display style plugin for Drupal - updated Nov 2023](https://www.drupal.org/docs/develop/creating-modules/building-a-views-display-style-plugin-for-drupal)
- [How to customize results of views using view templates in Drupal 8 and 9 - Updated Aug 2022](https://www.digitalnadeem.com/drupal/how-to-customize-results-of-views-using-view-templates-in-drupal-8-and-9/)
 

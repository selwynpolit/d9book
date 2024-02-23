---
title: Views
---

# Views
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=views.md)

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
This is a barbones example of how to modify a view using the template_preprocess_views_view() function.  This would be stored in a custom module or `.theme` file.  It modifies the view `events` by looping through the results and modifying each row as needed.

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
Also of note here is to output the new variable {{related_items}} I can jam it in a `views-view-field--news-events-search--page-nid.html.twig` (as in `/Users/selwyn/Sites/txglobal/web/themes/custom/txglobal/templates/views/views-view--news-events-search--page-events.html.twig`). Here is the partial code:

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

Another version of `/Users/spolit/Sites/txglobal/web/themes/custom/txglobal/txglobal.theme` which builds a UL of links based on the nid field in the view.  It also uses a helper function to build the links.  The helper function is in the same file and is called `build_related_items_links()`.

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

This example has a view called `selwyntest3` and a display called `page_1`.  It modifies the `nid` field to output a specific value.  In this case, there were two nid fields and views calls the first one `nid` and the second one `nid_1`.  This shows how to find the correct field and modify the output. It is from `/Users/spolit/Sites/tea/docroot/themes/custom/tea/tea.theme`.

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

## Views Query options

When views give you unexpected results that seem permissions related, you can check the query options.  This is a screenshot of the query options for a view.  You can see the query options by clicking on the `Advanced` link in the view and then in the `other` section, click on `Query settings`.  

![Views query options](/images/views-query-options.png)

::: tip Note
The dialog will display this WARNING: Disabling SQL rewriting means that node access security is disabled. This may allow users to see data they should not be able to see if your view is misconfigured. Use this option only if you understand and accept this security risk.
:::



## Reference
- [Drupal API Reference: Template Preprocess views view](https://api.drupal.org/api/drupal/core%21modules%21views%21views.theme.inc/function/template_preprocess_views_view/9.3.x)
- [Drupal API Reference: Template Preprocess Views View Field](https://api.drupal.org/api/drupal/core%21modules%21views%21views.theme.inc/function/template_preprocess_views_view_field/9.3.x )
 

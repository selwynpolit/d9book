---
layout: default
title: Twig
permalink: /twig
last_modified_date: '2023-04-14'
---

# TWIG
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![visitors](https://page-views.glitch.me/badge?page_id=selwynpolit.d9book-gh-pages-twig)

---

Drupal 10 uses **Twig 3**. Drupal 9 uses Twig 2. Drupal 8 used Twig 1.

## Overview

### Theme System Overview

Drupal\'s theme system allows a theme to have nearly complete control
over the appearance of the site, which includes both the markup and the
CSS used to style the markup. For this system to work, instead of
writing HTML markup directly, modules return \"render arrays\", which
are structured hierarchical arrays that include the data to be rendered
into HTML, and options that affect the markup. Render arrays are
ultimately rendered into HTML or other output formats by recursive calls
to [\\Drupal\\Core\\Render\\RendererInterface::render](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21RendererInterface.php/function/RendererInterface%3A%3Arender/10)(),
traversing the depth of the render array hierarchy. At each level, the
theme system is invoked to do the actual rendering. See the
documentation
of [\\Drupal\\Core\\Render\\RendererInterface::render](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21RendererInterface.php/function/RendererInterface%3A%3Arender/10)()
and the [Theme system and Render API
topic](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21theme.api.php/group/theme_render/10) for
more information about render arrays and rendering.

### Twig Templating Engine

Drupal uses the templating engine Twig. Twig offers developers a fast,
secure, and flexible method for building templates for Drupal 8 sites.
Twig does not require front-end developers to know PHP to build and
manipulate Drupal themes.

For more on theming in Drupal
see <https://www.drupal.org/docs/theming-drupal> .

For further Twig documentation
see [https://twig.symfony.com/doc/2.x](https://twig.symfony.com/doc/2.x%20/)
and <https://twig.symfony.com/doc/3.x>

Note. Drupal 10 uses **Twig 3**, Drupal 9 uses Twig 2 and Drupal 8 used
Twig 1.

## Displaying Data

### Fields or Logic

Twig can do things that PHP can't such as whitespacing control, sandboxing, automatic HTML escaping, manual contextual output escaping, inclusion of custom functions and filters that only affect templates.

Double curly braces are used to output a variable. E.g.

```twig
{% raw %}{{ content.title }}{% endraw %}
```

Brace and percent are used to put logic into Twig templates e.g. if, then, else or for loops. E.g.

```twig
{% raw %}{%if content.price is defined %}
  <h2>Price: {{ content.price }} </h2>
{% endif %}{% endraw %}
```

Use brace and pound symbol (hash) for comments e.g.

```twig
{# this section displays the voting details #}
```

Here are some of the Twig functions that you can use in twig templates: <https://www.drupal.org/docs/8/theming/twig/functions-in-twig-templates> There are lots of them e.g.


- `file_url($uri)`
- `link($text, $uri, $attributes)`
- `path($name, $parameters, $options)`
- `url($name, $parameters, $options)`

And even more Twig fun at <https://twig.symfony.com/doc/3.x/functions/index.html>


### Which template, which variables?

There is usually one `page.tpl.php` and *multiple* node templates. One node template per content type. Eg. `node-news-story.html.twig`, `node-event.html.twig`. There can also be field specific templates e.g. `web/themes/custom/txg/templates/field/field--field-3-column-links.html.twig`

In the `page.html.twig`, you can refer to variables as `page.content` or `node.label`

whereas node templates expect `content.field_image` or `node.field_myfield`

Note. If you don't see a field output for a node, try specifying the preface `node.` instead of `content.`.

Field specific template are usually very simple and refer to 
```twig
{% raw %}{{ items }}{% endraw %}
```
 and 
 ```twig
 {% raw %}{{ item.content }} {% endraw %}
 ```

e.g. from txg/web/themes/contrib/zurb_foundation/templates/page.html.twig

```twig
{% raw %}<section>
  {{ page.content }}
</section>{% endraw %}
```

And from `txg/web/themes/custom/txg/templates/content/page--node--event.html.twig` I accidentally started implementing this in the page template. See below
for the node template.

```twig
{% raw %}{{ drupal_field('field_image', 'node') }}

<h1>{{ node.label }}</h1>
<div>For: {{ node.field_for.0.value }}</div>
<div>DATE: {{ node.field_event_date.0.value|date('n/j/Y') }}</div>
<div>Time: {{ node.field_event_date.0.value|date('h:ia') }} - {{ node.field_event_date.0.end_value|date('h:ia') }}</div>

<div>
  Location:
    {% if node.field_event_location_link.0.url %}
    <a href="{{ node.field_event_location_link.0.url }}">{{ node.field_event_location.0.value }}</a>
    {% else %}
    {{ node.field_event_location.0.value }}
    {% endif %}
</div>

{% if node.field_event_cta_link.0.url %}
  CTA:<div class="button"><a href="{{ node.field_event_cta_link.0.url }}">{{ node.field_event_cta_link.0.title }}</a></div>
{% endif %}{% endraw %}
```

Here is the same basic stuff (as above) but implemented in the node template at `txg/web/themes/custom/txg/templates/content/node--event.html.twig`:

>Note. That `node.label` becomes `label` and `node.field_for` becomes `content.field_for`.

```twig
{% raw %}<h1>{{ label }}</h1>
{{ content.field_image }}
<div>Node: {{ node.id }}</div>
<div>For: {{ content.field_for }}</div>
<div>DATE: {{ node.field_event_date.0.value|date('n/j/Y') }}</div>
<div>Time: {{ node.field_event_date.0.value|date('h:ia') }} - {{ node.field_event_date.0.end_value|date('h:ia') }}</div>
<div>
  Location:
  {% if node.field_event_location_link.0.url %}
    <a href="{{ node.field_event_location_link.0.url }}">{{ node.field_event_location.0.value }}</a>
  {% else %}
    {{ node.field_event_location.0.value }}
  {% endif %}
</div>

{% if node.field_event_cta_link.0.url %}
  CTA:<div class="button"> <a href="{{ node.field_event_cta_link.0.url }}">{{ node.field_event_cta_link.0.title }}</a></div>
{% endif %}{% endraw %}
```

### Display fields or variables

Using `node.field_myfield` will bypass the rendering and display any markup in the field. Using `content.field_myfield` uses the rendering system and is the preferred way to display your content.

This will display all the content rendered

```twig
{% raw %}{{ content }}{% endraw %}
```

### Node Title with and without a link

Render node title (or label) (with markup -- so it may include \<span\> tags)

```twig
{% raw %}{{ label }}{% endraw %}
```

Render node label (without markup -- no html in this version)

```twig
{% raw %}{{ node.label }}{% endraw %}
```

Render link to node

```twig
{% raw %}<a href="{{ url }}">{{ label }}</a>{% endraw %}
```

// Or a little more complex..
```twig
{% raw %}<div class="title"><a href="{{ url }}">{{ label }}</a> | <span>{{ content.field_vendor_ref }}</span></div>{% endraw %}
```

### Fields

There are many ways to limit things and only show some of the content. Mostly often you will need to show specific fields. Note. This will include rendered info such as labels etc.

```twig
{% raw %}{{ content.field_yomama }}{% endraw %}
```

or

```twig
{% raw %}{{ content.field_ref_topic }}{% endraw %}
```

Any field -- just jam `content.` in front of it

```twig
{% raw %}{{ content.field_intl_students_and_scholars }}{% endraw %}
```

You can also grab node specific fields if `content.` type fields don't do the trick.

In a node template, you can display specific node fields by prefacing them with `node` e.g.:

```twig
{% raw %}{{ node.id }}
{{ node.label }}
{{ node.field_date.value }}
{{ node.field_date.end_value }}{% endraw %}
```


### Paragraph field

These still work fine: `content.field_abc` or `node.field_ref_topic` but instead of `node`, you preface fields with `paragraph` like this:

```twig
{% raw %}termid0: {{ paragraph.field_ref_tax.0.target_id }}
termid1: {{ paragraph.field_ref_tax.1.target_id }}{% endraw %}
```
and we get this result if we have selected two terms 13 and 16.

```
termid0: 13
termid1: 16
```

To dump a taxonomy reference field for debugging purposes use the code below. The pre tags format it a little nicer than if we don't have them.

```twig
{% raw %}<pre>
{{ dump(paragraph.field_ref_tax.value) }}
</pre>{% endraw %}
```

### Loop thru paragraph reference fields

Here we go looping thru all the values in a multi-value reference field.

```twig
{% raw %}{% for tax in paragraph.field_ref_tax %}
  <div>target_id: {{ tax.target_id }}</div>
{% endfor %}{% endraw %}
```

It's the same as outputting these guys:

```twig
{% raw %}termid0: {{ paragraph.field_ref_tax.0.target_id }}
termid1: {{ paragraph.field_ref_tax.1.target_id }}{% endraw %}
```
and to make this more useful, here we build a string of them to pass to a view.

From:
dirt/web/themes/custom/dirt_bootstrap/templates/paragraphs/paragraph\--news-preview.html.twig

```twig
{% raw %}
{# Figure out parameters to pass to view for news items #}
{% set params = '' %}
{% for item in paragraph.field_ref_tax_two %}
  {% set params = params ~ item.target_id %}
  {% if not loop.last %}
    {% set params = params ~ '+' %}
  {% endif %}
{% endfor %}
params: {{ params }}
{% endraw %}
```

This will output something like: 5+6+19

### Body

```twig
{% raw %}{{ content.body }}{% endraw %}
```
Or

```twig
{% raw %}{{ node.body.value }}{% endraw %}
```


And for summary

```twig
{% raw %}{{ node.body.summary | raw }}{% endraw %}
```
### Multi-value fields

Fields that you preface with `node.` can also handle an index (the 0 below) i.e. to indicate the first value in a multi-value field, 1 to indicate the second etc.

```twig
{% raw %}{{ node.field_iso_n3_country_code.0.value }}{% endraw %}
```

### Fields with HTML

If a field has html that you want rendered, use the keyword raw. Be
aware this has security considerations which you can mitigate using
[striptags](https://twig.symfony.com/doc/3.x/filters/striptags.html)
filters:

```twig
{% raw %}<div>How to order: {{ how_to_order|raw }}</div>{% endraw %}
```

And maybe you want to only allow \<b\> tags

```twig
{% raw %}{{ word|striptags('<b>')|raw }}{% endraw %}
```

Or several tags. In this case \<b\>\<a\>\<pre\>


```twig
{% raw %}{{ word|striptags('<b>,<a>,<pre>')|raw }}{% endraw %}
```

### The date/time a node is published, updated or created

Each of these calls return a datetime value in string form which can be
massaged by the twig date() function for formatting.

```twig
{% raw %}<pre>
Created:   {{ node.created.value }}
Created:   {{ node.createdtime }}
Created:   {{ node.created.value|date('Y-m-d') }}

Modified:  {{ node.changed.value }}
Modified:  {{ node.changedtime }}
Modified:  {{ node.changed.value|date('Y-m-d') }}

Published: {{ node.published_at.value }}
Published: {{ node.published_at.value|date('Y-m-d') }}
</pre>{% endraw %}
```

Here is the output you might see. Note. The first published is apparently blank because I didn't use the drupal scheduling to publish the node (maybe?) and the second one seems to have defaulted to today's date.

```
Created: 1604034000
Created: 1604034000
Created: 2020-10-30
Modified: 1604528207
Modified: 1604528207
Modified: 2020-11-04
Published:
Published: 2020-11-20
```

Updated/changed

```twig
{% raw %}{% set post_date = node.changedtime %}{% endraw %}
```

Created (same as authored on date on node edit form):

```twig
{% raw %}{{ node.createdtime }}{% raw %}
```

And pretty formatted like Sep 2, 2023

```twig
{% raw %}{{ node.createdtime\|date('M d, Y') }}{% raw %}
```

Also


```twig
{% raw %}<div class="date">Date posted: {{ node.getCreatedTime|date('m/d/Y') }}</div>
<div class="date">Date posted: {{ node.getChangedTime|date('m/d/Y') }}</div>{% endraw %}
```

Node published date:

```twig
{% raw %}Date published: {{ _context.node.published_at.value }}
Date published: {{ node.published_at.value }}{% endraw %}
```

### Format a date field

Use the field's format settings; include wrappers. This example includes wrappers.

```twig
{% raw %}{{ content.field_blog_date }}{% endraw %}
```

The examples below do not include wrappers. 

Use the field's format settings. This will use the format defined in `Content type » Manage Displays »Your View Mode`.

```twig
{% raw %}{{ content.field_blog_date.0 }}{% endraw %}
```

Using Twig date filter and a defined Drupal date format

```twig
{% raw %}{{ node.field_blog_date.value|date('U')|format_date('short_mdyyyy') }}{% endraw %}
```

Use Twig date filter

```twig
{% raw %}{{ node.field_blog_date.value|date('n/j/Y') }}{% endraw %}
```


### Smart date field formatting

When using the [smart date](https://www.drupal.org/project/smart_date) module, dates are stored as timestamps so you have to use the twig date function to format them. If you just put this in your template:

```twig
{% raw %}{{ content.field_when }}{% endraw %}
```

The output will include whichever formatting you specify in Drupal. While I assume there is a way to pass a [smart date](https://www.drupal.org/project/smart_date) formatting string to twig, I haven\'t discovered it yet. Here are ways to format a [smart date](https://www.drupal.org/project/smart_date).

Specify the index (the 0 indicating the first value, or 1 for the second) e.g. node.field.0.value and pipe the twig [date](https://twig.symfony.com/doc/3.x/filters/date.html) function  for formatting:

Date as in July 18, 2023
```twig
{% raw %}{{ node.field_when.0.value|date('F j, Y') }}{% endraw %}
```


End date
```twig
{% raw %}{{ node.field_when.0.end_value|date('F j, Y') }}{% endraw %}
```


Timezone as in America/Chicago

```twig
{% raw %}{{ node.field_when.0.value|date('e') }}{% endraw %}
```


Timezone as in CDT
```twig
{% raw %}{{ node.field_when.0.value|date('T') }}{% endraw %}
```


Day of the week
```twig
{% raw %}{{ node.field_when.0.value|date('l') }} {# day of week #}{% endraw %}
```


Hide the end date if it is the same as the start date

```twig
{% raw %}{% set start = node.field_when.0.value|date('l F j, Y') %}
{% set end = node.field_when.0.end_value|date('l F j, Y') %}
  <p class="date"> {{ start }}</p>
{% if not start is same as(end) %}
  <p class="date"> {{ end }}</p>
{% endif %}{% endraw %}
```

### Entity Reference field

If you have an entity reference field such as field_ref_topic (entity reference to topic content) you have to specify the target_id like this. If you have only 1 reference, use the .0, for the second one use .1 and so on.

```twig
{% raw %}{{ node.field_ref_topic.0.target_id }}{% endraw %}
```

Note. This will show the node id of the entity reference field. See below to see the content that the entity reference field points to.

### Entity reference destination content

If you have an entity reference and you want to display the content from the node that is referenced i.e. if you have a contract with a reference to the vendor node and you want to display information from the vendor node on the contract you can dereference fields in the entity destination:

From `dirt/web/themes/custom/dirt_bootstrap/templates/content/node--contract--vendor-list.html.twig`: 

```twig
{% raw %}{{ node.field_sf_contract_ref.entity.field_contract_overview.value }}{% endraw %}
```

Or

```twig
{% raw %}{{ content.field_sf_contract_ref.entity.field_contract_overview }}{% endraw %}
```

The field in the contract node is called `field_sf_contract_ref`. The field in the referenced entity is called field_contract_overview. Notice how with the `node.` style, you must specify `.value` at the end.

Here is an example of a taxonomy term where the title of the term will be displayed.

```twig
{% raw %}<pre>
Dump category:
{{ dump(node.field_ref_tax.entity.label) }}
</pre>{% endraw %}
```

### Taxonomy term

Here is an example of displaying a taxonomy term.

```twig
{% raw %}<pre>
Dump category: {{ dump(node.field_ref_tax.entity.label) }}
</pre>{% endraw %}
```

### Render a block

Example block with a machine name of  `block---system-powered-by-block.html.twig` from a custom theme

```twig
{% raw %}{%
  set classes = [
    'block',
    'block-' ~ configuration.provider|clean_class,
    'block-' ~ plugin_id|clean_class,
  ]
%}
<div{{ attributes.addClass(classes) }}>
  {{ title_prefix }}
  {% if label %}
    <h2{{ title_attributes }}>{{ label }}</h2>
  {% endif %}
  {{ title_suffix }}
  {% block content %}
    {{ content }}
  {% endblock %}
  also powered by <a href="http://austinprogressivecalendar.com">Austin Progressive Calendar</a>
</div>{% endraw %}
```

### Render a list created in the template_preprocess_node()

Here we create a list in the function:

```php
function burger_theme_preprocess_node(&$variables) {

  $burger_list = [
    ['name' => 'Cheesburger'],
    ['name' => 'Mushroomburger'],
    ['name' => 'Chickenburger'],
  ];
  $variables['burgers'] = $burger_list;
}
```

and render it in the `node--article--full.html.twig`

```twig
{% raw %}<ol>
  {% for burger in burgers %}
  <li>{{ burger['name'] }}</li>
  {% endfor %}
</ol>{% endraw %}
```

### Links

There are a bajillion kertrillion or more ways to render a link

Link field (URL)

This is the simplest way. Just set the display mode to link

![Suggest Button](assets/images/suggest_button.png)


And output the link without a label.

```twig
{% raw %}{{ content.field_suggest_button }}{% endraw %}
```


If you need a little more control you might use this version which allows classes etc. We are adding several classes onto the anchor to make it look like a button. In this case with an internal link, it shows up using the alias of the link i.e. it shows `/contracts` instead of `node/7` when you hover over the link.

```twig
{% raw %}<p><a class="btn secondary navy centered" href="{{ node.field_suggest_button.0.url }}">{{ node.field_suggest_button.0.title }}</a></p>{% endraw %}
```

Using `.uri` causes the link (internal only. External links are fine) to show up as `node/7` when you hover over the link.

```twig
{% raw %}
<p><a class="btn secondary navy centered" href="{{ node.field_suggest_button.uri }}">{{ node.field_suggest_button.0.title }}</a></p>
{% endraw %}
```

Don't try this as it won't work:

```twig
{% raw %}//bad
{{ node.field_suggest_button.url }}.
//bad{% endraw %}
```
Want to use the text from a different field? No problem.

```twig
{% raw %}<div class="title"><a href="{{ node.field_link.uri }}">{{ node.field_contract_number.value }}</a></div>{% endraw %}
```

### Links to other pages on site

Absolute link:

```twig
{% raw %}<a href="{{ url('entity.node.canonical', {node: 3223}) }}">Link to Weather Balloon node 3223 </a>{% endraw %}
```

Relative link

See path vs url:

```twig
{% raw %}<a href="{{ path('entity.node.canonical', {node: 3223}) }}">Link to Weather Balloon node 3223 </a>{% endraw %}
```

### Link to a user using user id

You can link to users using the following:

```twig
{% raw %}<a href="{{ url('entity.user.canonical', {user: 1}) }}">Link to user 1 </a>{% endraw %}
```

### External link in a field via an entity reference

Here we have a node with an entity reference field (`field_sf_contract_ref`) to another entity.

In a preprocess function, you can grab the link. Note, you can just grab the `first()` one. Later on you can see that in the twig template, you can specify the first one with `.0`

From dirt/web/themes/custom/dirt_bootstrap/dirt_bootstrap.theme

```php
$vendor_url = $node->field_sf_contract_ref->entity->field_vendor_url->first();
if ($vendor_url) {
  $vendor_url = $vendor_url->getUrl();
  if ($vendor_url) {
    $variables['vendor_url'] = $vendor_url->getUri();
  }
}
```


And in the template we retrieve the URI with `.uri`: 

```twig
{% raw %}
<p><a class="styled-link ext" href="{{ node.field_sf_contract_ref.entity.field_vendor_url.uri }}">Vendor Website</a></p>
{% endraw %}
```

Here we check if there is a target value and output that also. E.g.
`target="_blank"` and also display the title -- this is the anchor
title as in the words "Vendor Website" below

```html
<a href="https://www.duckduckgo.com">Vendor Website</a></p>
```

From
`inside-marthe/themes/custom/dp/templates/paragraph/paragraph--sidebar-product-card.html.twig` we wrap some stuff in a link:

```twig
{% raw %}
<a href="{{content.field_link.0['#url']}}" {% if content.field_link.0['#options']['attributes']['target'] %} target="{{content.field_link.0['#options']['attributes']['target']}}" {% endif %} class="button">{{content.field_link.0['#title']}}
  {{ content.field_image }}
  <h2 class="module-header">{{content.field_text}}</h2>
  {{content.field_text2}}
</a>
{% endraw %}
```


And from
`txg/web/themes/custom/txg/templates/content/node--event--card.html.twig` if there is a url, display the link with the url, otherwise just display the title for the link. I'm not 100% sure this is really valid. Can you put in a title and no link?

```twig
{% raw %}
{% if node.field_event_location_link.0.url %}
    <a href="{{ node.field_event_location_link.0.url }}">{{ node.field_event_location.0.value }}</a>
{% else %}
  {{ node.field_event_location.0.value }}
{% endif %}
{% endraw %}
```


### Render an internal link programatically

Here we want to render an internal link to a page on our Drupal site (as opposed to a link to another site.) We grab the link in a preprocess function. Extract out the title and the URI.

```php
$instructions_node = Node::load($order_type_instructions_nid);
if ($instructions_node) {
  $order_link = $instructions_node->field_link->first();
  if ($order_link) {
    $uri = $order_link->uri;
    $variables['order_link_title'] = $order_link->title;
    $order_url = $order_link->getUrl();
    if ($order_url) {
      $variables['order_type_link'] = $order_url;
    }
  }
}
```


We can put the pieces in the twig template like this

```twig
{% raw %}
<a href="{{ order_type_link }}">{{ order_link_title }}</a>
{% endraw %}
```
### Render an image with an image style

From `inside-marthe/themes/custom/dp/templates/paragraph/paragraph--sidebar-resource.html.twig`

Here we use sidebar_standard image style

```twig
{% raw %}
<aside class="module module--featured" data-interchange="[{{ content.field_image.0['#item'].entity.uri.value | image_style('sidebar_standard') }}, small]">
{% endraw %}
```


Or for a media field, set the image style on the display mode and use this:

```twig
{% raw %}{{ content.field_banner_image.0 }}{% endraw %}
```

### Hide if there is no content in a field or image 

From
inside-marthe/themes/custom/dp/templates/content/node\--video-detail.html.twig I check to see if there are any values in this array `related_lessons_nid`s and display the view.

```twig
{% raw %}{% if related_lessons_nids|length %}
  <div class="section section--featured">
    <div class="grid-container">
      <h2 class="section-header text-center large-text-left">Related Lessons</h2>
      <div class="grid-x grid-margin-x" data-equalizer data-equalize-on="large">
        {{ drupal_view('video', 'embed_collection_related_lessons', related_lessons_nids|join(', ')) }}
      </div>
    </div>
  </div>
{% endif %}{% endraw %}
```

Not empty:

```twig
{% raw %}{% if content.field_myfield is not empty %}
  {# Do something here #}
{% endif %}{% endraw %}
```

### Hide if there is no image present 

If there is an image (and it is renderable) display the image

```twig
{% raw %}{% if content.field_teacher_commentary_image|render %}
  <img src="{{file_url( content.field_teacher_commentary_image['#items'].entity.uri.value ) }}" width="420" height="255" alt="" class="left">
{% endif %}{% endraw %}
```

### Attributes

From <https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes>:

Elements in HTML have **attributes**; these are additional values that configure the elements or adjust their behavior in various ways to meet the criteria the users want.

Read more about using attributes in templates
[https://www.drupal.org/docs/8/theming-drupal-8/using-attributes-in-templates](https://www.drupal.org/docs/8/theming-drupal-8/using-attributes-in-templates)

To add a data attribute use:

```twig
{% raw %}{{ attributes.setAttribute('data-myname','tommy') }}{% endraw %}
```

e.g.

```twig
{% raw %}<article{{ attributes.addClass(classes).setAttribute('my-name', 'Selwyn') }}>{% endraw %}
```

Produces:

```html
<article data-history-node-id="3224" data-quickedit-entity-id="node/3224" role="article" class="contextual-region node node--type-article node--promoted node--view-mode-full" about="/burger1" typeof="schema:Article" my-name="Selwyn" data-quickedit-entity-instance-id="0">
```

More useful examples at <https://www.drupal.org/docs/8/theming-drupal-8/using-attributes-in-templates>
such as:

```twig
{% raw %}{% set classes = ['red', 'green', 'blue'] %}
{% set my_id = 'specific-id' %}
{% set image_src = 'https://www.drupal.org/files/powered-blue-135x42.png' %}

<img{{ attributes.addClass(classes).removeClass('green').setAttribute('id', my_id).setAttribute('src', image_src) }}>{% endraw %}
```

Which outputs the following:

 ```html
 <img id="specific-id" class="red blue" src="https://www.drupal.org/files/powered-blue-135x42.png">
```


Check if an attribute has a class

```twig
{% raw %}{{ attributes.hasClass($class) }}{% endraw %}
```

Remove an attribute

```twig
{% raw %}{{ attributes.removeAttribute() }}{% endraw %}
```

Convert attributes to array

```twig
{% raw %}{{ attributes.toArray () }}{% endraw %}
```


### Output the content but leave off the field_image

From
`very/web/themes/very/templates/node--teaser.html.twig`:

```twig
{% raw %}<div{{ content_attributes.addClass('content') }}>
  {{ content|without('field_image')|render|striptags }}
</div>{% endraw %}
```

###  Add a class

```twig
{% raw %}<div{{ content_attributes.addClass('node__content') }}>{% endraw %}
```

### Add a class conditionally

From `very/web/themes/very/templates/node--teaser.html.twig`

For an unpublished node, wrap this class around the word unpublished

```twig
{% raw %}{% if not node.published %}
  <p class="node--unpublished">{{ 'Unpublished'|t }}</p>
{% endif %}{% endraw %}
```

### Links to other pages on site

Absolute:

```twig
{% raw %}<a href="{{ url('entity.node.canonical', {node: 3223}) }}">Link to WEA node 3223 </a>{% endraw %}
```

Relative (see path vs url):

```twig
{% raw %}<a href="{{ path('entity.node.canonical', {node: 3223}) }}">Link to WEA node 3223 </a>{% endraw %}
```

Could also link to users using

```twig
{% raw %}<a href="{{ url('entity.user.canonical', {user: 1}) }}">Link to user 1 </a>{% endraw %}
```


### Loop.index in a paragraph twig template

From:
`web/themes/custom/dprime/templates/field/field--paragraph--field-links--sidebar-cta.html.twig`

Notice the use of `loop.index` to only output this for the first item

```twig
{% raw %}{% for item in items %}
  {% if loop.index == 1 %}
    <div class="cell medium-6">
      <a href="{{item.content['#url']}}" class="button {% if loop.index == 2 %}hollow {% endif %}button--light m-b-0"{% if item.content['#options']['attributes']['target'] %} target="{{item.content['#options']['attributes']['target']}}" {% endif %}>{{item.content['#title']}}</a>
    </div>
  {% endif %}
{% endfor %}{% endraw %}
```

### Loop thru an array of items with a separator

This loads all the authors and adds `and` between them except for the last one:

```twig
{% raw %}<div>
  {%- if content.author -%}
      by
    {%- for author in content.author -%}
      {% if loop.last %}
        {% set separator = '' %}
      {% else %}
        {% set separator = ' and ' %}
      {% endif %}
      {{ author }} {{ separator }}
    {%- endfor -%}
  {%- endif -%}
</div>{% endraw %}
```







---

<script src="https://giscus.app/client.js"
        data-repo="selwynpolit/d9book"
        data-repo-id="MDEwOlJlcG9zaXRvcnkzMjUxNTQ1Nzg="
        data-category="Q&A"
        data-category-id="MDE4OkRpc2N1c3Npb25DYXRlZ29yeTMyMjY2NDE4"
        data-mapping="title"
        data-strict="0"
        data-reactions-enabled="1"
        data-emit-metadata="0"
        data-input-position="bottom"
        data-theme="preferred_color_scheme"
        data-lang="en"
        crossorigin="anonymous"
        async>
</script>

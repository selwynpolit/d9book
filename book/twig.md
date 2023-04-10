# TWIG

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

- [TWIG](#twig)
  - [Overview](#overview)
    - [Theme System Overview](#theme-system-overview)
    - [Twig Templating Engine](#twig-templating-engine)
  - [Displaying Data](#displaying-data)
    - [Fields or Logic](#fields-or-logic)
    - [Which template, which variables?](#which-template-which-variables)
    - [Display fields or variables](#display-fields-or-variables)
    - [Node Title with and without a link](#node-title-with-and-without-a-link)
    - [Fields](#fields)
    - [Paragraph field](#paragraph-field)
    - [Loop thru paragraph reference fields](#loop-thru-paragraph-reference-fields)
    - [Body](#body)
    - [Multi-value fields](#multi-value-fields)
    - [Fields with HTML](#fields-with-html)
    - [The date/time a node is published, updated or created](#the-datetime-a-node-is-published-updated-or-created)
    - [Format a date field](#format-a-date-field)
    - [Smart date field formatting](#smart-date-field-formatting)
    - [Entity Reference field](#entity-reference-field)
    - [Entity reference destination content](#entity-reference-destination-content)
    - [Taxonomy term](#taxonomy-term)
    - [Render a block](#render-a-block)
    - [Render a list created in the template\_preprocess\_node()](#render-a-list-created-in-the-template_preprocess_node)
    - [Links](#links)
    - [Links to other pages on site](#links-to-other-pages-on-site)
    - [Link to a user using user id](#link-to-a-user-using-user-id)
    - [External link in a field via an entity reference](#external-link-in-a-field-via-an-entity-reference)
    - [Render an internal link programatically](#render-an-internal-link-programatically)
    - [Render an image with an image style](#render-an-image-with-an-image-style)
    - [Hide if there is no content in a field or image](#hide-if-there-is-no-content-in-a-field-or-image)
    - [Hide if there is no image present](#hide-if-there-is-no-image-present)
    - [Attributes](#attributes)
    - [Output the content but leave off the field\_image](#output-the-content-but-leave-off-the-field_image)
    - [Add a class](#add-a-class)
    - [Add a class conditionally](#add-a-class-conditionally)
    - [Links to other pages on site](#links-to-other-pages-on-site-1)
    - [Loop.index in a paragraph twig template](#loopindex-in-a-paragraph-twig-template)
    - [Loop thru an array of items with a separator](#loop-thru-an-array-of-items-with-a-separator)
  - [Add Javascript into a twig template](#add-javascript-into-a-twig-template)
  - [Control/Logic](#controllogic)
    - [Concatenate values into a string with join](#concatenate-values-into-a-string-with-join)
    - [Include partial templates](#include-partial-templates)
    - [Loop through entity reference items](#loop-through-entity-reference-items)
    - [IF OR](#if-or)
    - [Test if a formatted text field is empty](#test-if-a-formatted-text-field-is-empty)
    - [Test empty variable](#test-empty-variable)
    - [Conditionals (empty, defined, even)](#conditionals-empty-defined-even)
    - [Test if a paragraph is empty using striptags](#test-if-a-paragraph-is-empty-using-striptags)
    - [Comparing strings](#comparing-strings)
    - [Include other templates as partials](#include-other-templates-as-partials)
    - [Check if an attribute has a class](#check-if-an-attribute-has-a-class)
    - [Remove an attribute](#remove-an-attribute)
    - [Convert attributes to array](#convert-attributes-to-array)
  - [Views](#views)
    - [Render a view with contextual filter](#render-a-view-with-contextual-filter)
    - [Count how many rows returned from a view](#count-how-many-rows-returned-from-a-view)
    - [If view results empty, show a different view](#if-view-results-empty-show-a-different-view)
    - [Selectively pass 1 termid or 2 to a view as the contextual filter](#selectively-pass-1-termid-or-2-to-a-view-as-the-contextual-filter)
    - [Views templates](#views-templates)
    - [Inject variables](#inject-variables)
    - [Concatenate values into a string with join](#concatenate-values-into-a-string-with-join-1)
    - [Loop through entity reference items](#loop-through-entity-reference-items-1)
  - [Twig filters and functions](#twig-filters-and-functions)
  - [Twig Tweak](#twig-tweak)
    - [Display a block with twig\_tweak](#display-a-block-with-twig_tweak)
    - [Display filter form block](#display-filter-form-block)
    - [Embed view in twig template](#embed-view-in-twig-template)
    - [Some tricky quotes magic](#some-tricky-quotes-magic)
  - [Troubleshooting](#troubleshooting)
    - [Enable Twig debugging output in source](#enable-twig-debugging-output-in-source)
    - [Debugging - Dump a variable](#debugging---dump-a-variable)
    - [Dump taxonomy reference field](#dump-taxonomy-reference-field)
    - [Using kint or dump to display variable in a template](#using-kint-or-dump-to-display-variable-in-a-template)
    - [502 bad gateway error](#502-bad-gateway-error)
    - [Views error](#views-error)
    - [Striptags (when twig debug info causes if to fail)](#striptags-when-twig-debug-info-causes-if-to-fail)
  - [Reference](#reference)


![visitors](https://page-views.glitch.me/badge?page_id=selwynpolit.d9book-gh-pages-twig)

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

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
{% raw %}
{{ content.title }}
{% endraw %}
```

Brace and percent are used to put logic into Twig templates e.g. if, then, else or for loops. E.g.

```twig
{% raw %}
{%if content.price is defined %}
  <h2>Price: {{ content.price }} </h2>
{% endif %}
{% endraw %}
```

Use brace and pound symbol (hash) for comments e.g.

```twig
{# this section displays the voting details #}
```

Here are some of the Twig functions that you can use in twig templates: <https://www.drupal.org/docs/8/theming/twig/functions-in-twig-templates> There are lots of them e.g.

```
file_url($uri)
link($text, $uri, $attributes)
path($name, $parameters, $options)
url($name, $parameters, $options)
```

And even more Twig fun at <https://twig.symfony.com/doc/3.x/functions/index.html>


### Which template, which variables?

There is usually one `page.tpl.php` and *multiple* node templates. One node template per content type. Eg. `node-news-story.html.twig`, `node-event.html.twig`. There can also be field specific templates e.g. `web/themes/custom/txg/templates/field/field--field-3-column-links.html.twig`

In the `page.html.twig`, you can refer to variables as 
```twig
{% raw %}
{{ page.content }}
{% endraw %}
``` 
or 
```twig
{% raw %}
{{ node.label }}
{% endraw %}
``` 
whereas node templates expect:
```twig
{% raw %}
{{ content.field_image }}
{% endraw %}
```
 or
 ```twig
 {% raw %}
 {{ node.field_myfield }}
 {% endraw %}
 ```

Note. If you don't see a field output for a node, try specifying `node.` instead of `content.`.

Field specific template are usually very simple and refer to 
```twig
{% raw %}
{{items}}
{% endraw %}
```
 and 
 ```twig
 {% raw %}
 {{item.content}}
 {% endraw %}
 ```

e.g. from txg/web/themes/contrib/zurb_foundation/templates/page.html.twig

```twig
{% raw %}
<section>
  {{ page.content }}
</section>
{% endraw %}
```

And from `txg/web/themes/custom/txg/templates/content/page--node--event.html.twig` I accidentally started implementing this in the page template. See below
for the node template.

```twig
{% raw %}
{{ drupal_field('field_image', 'node') }}

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
{% endif %}
{% endraw %}
```

Here is the same basic stuff (as above) but implemented in the node template at `txg/web/themes/custom/txg/templates/content/node--event.html.twig`:

>Note. That `node.label` becomes `label` and `node.field_for` becomes `content.field_for`.

```twig
{% raw %}
<h1>{{ label }}</h1>
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
{% endif %}
{% endraw %}
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
<a href="{{ url }}">{{ label }}</a>
```

// Or a little more complex..
```twig
<div class="title"><a href="{{ url }}">{{ label }}</a> | <span>{{ content.field_vendor_ref }}</span></div>
```

### Fields

There are many ways to limit things and only show some of the content. Mostly often you will need to show specific fields. Note. This will include rendered info such as labels etc.

`{{ content.field_yomama }}`

or

`{{ content.field_ref_topic }}`

Any old field -- just jam `content.` in front of it

`{{ content.field_intl_students_and_scholars }}`

You can also grab node specific fields if `content.` type fields don't do the trick.

In a node template, you can dump specific node fields by prefacing them with `node`:

```twig
{{ node.id }}
{{ node.label }}

{{ node.field_date.value }}
{{ node.field_date.end_value }}
```

### Paragraph field

These still work fine:

`{{ content.field_yomama }}`

or

`{{ content.field_ref_topic }}`

But instead of node, you use `paragraph`:

```twig
termid0: {{ paragraph.field_ref_tax.0.target_id }}
termid1: {{ paragraph.field_ref_tax.1.target_id }}
```
and we get this result if we have selected two terms 13 and 16.

termid0: 13
termid1: 16

To dump a taxonomy reference field for debugging purposes use the code below. The pre tags format it a little nicer than if we don't have them.

```twig
<pre>
{{ dump(paragraph.field_ref_tax.value) }}
</pre>
```

### Loop thru paragraph reference fields

Here we go looping thru all the values in a multi-value reference field.

```twig
{% for tax in paragraph.field_ref_tax %}
  <div>target_id: {{ tax.target_id }}</div>
{% endfor %}
```

It's the same as outputting these guys:

```twig
termid0: {{ paragraph.field_ref_tax.0.target_id }}
termid1: {{ paragraph.field_ref_tax.1.target_id }}
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

`{{ content.body }}`

Or

`{{ node.body.value }}`

And for summary

`{{ node.body.summary | raw }}`

### Multi-value fields

Fields that you preface with \"node.\" can also handle an index (the 0
below) i.e. to indicate the first value in a multi-value field, 1 to
indicate the second etc.

`{{ node.field_iso_n3_country_code.0.value }}`

### Fields with HTML

If a field has html that you want rendered, use the keyword raw. Be
aware this has security considerations which you can mitigate using
[striptags](https://twig.symfony.com/doc/3.x/filters/striptags.html)
filters:

```twig
<div>How to order: {{ how_to_order|raw }}</div>
```

And maybe you want to only allow \<b\> tags

```twig
{{ word|striptags('<b>')|raw }}
```

Or several tags. In this case \<b\>\<a\>\<pre\>


```twig
{{ word|striptags('<b>,<a>,<pre>')|raw }}
```

### The date/time a node is published, updated or created

Each of these calls return a datetime value in string form which can be
massaged by the twig date() function for formatting.

```twig
{% raw %}
<pre>
Created:   {{ node.created.value }}
Created:   {{ node.createdtime }}
Created:   {{ node.created.value|date('Y-m-d') }}

Modified:  {{ node.changed.value }}
Modified:  {{ node.changedtime }}
Modified:  {{ node.changed.value|date('Y-m-d') }}

Published: {{ node.published_at.value }}
Published: {{ node.published_at.value|date('Y-m-d') }}
</pre>
{% endraw %}
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
{% raw %}
{% set post_date = node.changedtime %}
{% endraw %}
```

Created (same as authored on date on node edit form):

`{{ node.createdtime }}`

And pretty formatted like Sep 2, 2023

`{{ node.createdtime\|date('M d, Y') }}`

Also


```twig
{% raw %}
<div class="date">Date posted: {{ node.getCreatedTime|date('m/d/Y') }}</div>
<div class="date">Date posted: {{ node.getChangedTime|date('m/d/Y') }}</div>
{% endraw %}
```
Node published date

```twig
{% raw %}
Date published: {{ _context.node.published_at.value }}
Date published: {{ node.published_at.value }}
{% endraw %}
```



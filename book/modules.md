---
title: Modules
---

# Modules for Drupal
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=modules.md)

## Overview

With over 50,000 modules, Drupal has the ability to do almost anything. This also means it can be a little tough to find what you need. Here is a list of modules that are important, useful and interesting. Search for [modules on drupal.org.](https://www.drupal.org/project/project_module)

::: tip
If you want to check the usage for a module that isn't as popular as, for example, [token](https://www.drupal.org/project/token) which clearly displays `636,845 sites report using this module` Check the url [https://www.drupal.org/project/usage/token](https://www.drupal.org/project/usage/token) Just replace `token` with the machine name for that module.
:::

## Accessibility

### All in One Accessibility

#### Quick Web Accessibility Implementation with Drupal All In One Accessibility module!

The All In One Accessibility widget is developed to improve accessibility and usability of your website. It uses the accessibility interface which handles UI and design related adjustments.

Enable a wide array of people with disabilities to use your Drupal website effectively with the All In One Accessibility widget. It will integrate basic accessibility features according to the **ADA, WCAG 2.1 & 2.2, Section 508, Australian DDA, European EAA EN 301 549, UK Equality Act (EA), Israeli Standard 5568, California Unruh, Ontario AODA, Canada ACA, German BITV, France RGAA, Brazilian Inclusion Law (LBI 13.146/2015), Spain UNE 139803:2012, JIS X 8341 (Japan), Italian Stanca Act and Switzerland DDA Standards**. These standards ensure that the site is accessible to people with hearing or vision impairments, cognitive impairments, and perception problems.

[https://www.drupal.org/project/all_in_one_accessibility](https://www.drupal.org/project/all_in_one_accessibility)

[Here's a comprehensive feature guide](https://www.dropbox.com/s/de41n4xm9zjwxix/All-in-One-Accessibility-PRO-App-Usage-and-Functionality.pdf?dl=0)

## Access/Permissions/Authentication

### Access by Reference

Lightweight module that extends read, update or delete permissions to a user in the following cases:

1. "User": The node references the user
1. "User's mail"The node references the user's e-mail
1. "Profile value": The node has a value in a specified field that is the same as one in the user's profile
1. "Inherit from parent": The node references a node that the user has certain permissions on.

In each case, the rule only applies to logged-in users with general permission to access nodes by reference, and only on the node types and field names set in the configuration page.

[https://www.drupal.org/project/access_by_ref](https://www.drupal.org/project/access_by_ref)


### Simple OAuth
Simple OAuth is an implementation of the OAuth 2.0 Authorization Framework RFC and is based on League\OAuth2. The module is extremely simple and flexible, allowing you to configure your OAuth 2.0 server to suit your needs. It supports the following grant types:

[https://www.drupal.org/project/simple_oauth](https://www.drupal.org/project/simple_oauth)


## AI (Artifical Intelligence)

### OpenAI / ChatGPT / AI Search Integration
The OpenAI module aims to provide a suite of modules and an API foundation for OpenAI integration in Drupal for generating text content, images, content analysis and more. OpenAI is the company behind artificial generational intelligence products that powers applications like ChatGPT, GPT-3, GitHub CoPilot, and more. Our goal is to find ways of augmenting and adding assistive AI tech leveraging OpenAI API services in Drupal, transforming the way you manage your content and maintenance tasks.

[https://www.drupal.org/project/openai](https://www.drupal.org/project/openai)

### ChatGPT Content Creator
This is a lightweight and simple OpenAI/ChatGPT module which can act as a content generator, act as a content translator, act as a content assistance tool like creating images from text, extracting SEO keywords from content etc. You can configure to choose either of GPT-3, GPT-3.5 or GPT-4.

[https://www.drupal.org/project/chatgpt_plugin](https://www.drupal.org/project/chatgpt_plugin)

### AWS AI Augmentor

The AWS AI Augmentor is a submodule of Augmentor. It provides an implementation of multiple Augmentor plugins to allow Augmentor to interface with AWS AI Services API.

[https://www.drupal.org/project/augmentor_aws](https://www.drupal.org/project/augmentor_aws)

### Augmentor integration
Augmentor is an AI framework for Drupal which allows for the easy integration of disparate AI systems into Drupal. It provides a plugable ecosystem for managing a variety of AI services such as GPT3, ChatGPT, NLP Cloud, Google Cloud Vision and many more. Augmentor integration module allows content augmentation e.g summarize content, generate content tags etc.

[https://www.drupal.org/project/augmentor](https://www.drupal.org/project/augmentor)

## Breadcrumbs

### Easy Breadcrumb

The Easy Breadcrumb module updates the core Breadcrumb block to include the current page title in the breadcrumb. It also comes with additional settings that are common features needed in breadcrumbs. Breadcrumbs use the current URL (path alias) and the current page title to build the crumbs. The module is designed to work out of the box with no adjustments, and it comes with settings to customize the crumbs.

Example
URL: `/gallery/videos/kittens`

Breadcrumbs: `Home >> Gallery >> Videos >> Cute Kittens`

[https://www.drupal.org/project/easy_breadcrumb](https://www.drupal.org/project/easy_breadcrumb)

### Menu Breadcrumb

This module allows you to use the menu the current page belongs to for the breadcrumb, generating breadcrumbs from the titles of parent menus.

- Select and re-order the menus on which you want the menu-based breadcrumbs
- Append the page title to the breadcrumb (either as a clickable url or not)
- Hide the breadcrumb if it only contains the link to the front page
- Generate the breadcrumb from the URL structure if content does not belong to a menu
- If the "Taxonomy Attachment" option is selected for a menu, and if the current page belongs to a taxonomy that is on that menu, it will inherit the taxonomy page's menu breadcrumbs (e.g., for blog entries that aren't on any menu).
- Other breadcrumb builders (e.g., the path & title-based Drupal 8 default breadcrumb builder) will be used if there is no applicable menu based breadcrumb.

[https://www.drupal.org/project/menu_breadcrumb](https://www.drupal.org/project/menu_breadcrumb)

## Cache Control/Performance

### Acquia Purge
The acquia_purge module invalidates cached content on Acquia Cloud and allows you to set Drupal's time to live (TTL) to a high value like a year. This makes your site more resilient, the stack do less work and improves the performance of your site dramatically!

[https://www.drupal.org/project/acquia_purge](https://www.drupal.org/project/acquia_purge)

### Pantheon Advanced Page Cache
Pantheon Advanced Page Cache module is a bridge between Drupal cache metadata and the Pantheon Global CDN.

Just by turning on this module your Drupal site will start emitting the HTTP headers necessary to make the Pantheon Global CDN aware of data underlying the response. Then, when the underlying data changes (nodes and taxonomy terms are updated, user permissions changed) this module will clear only the relevant pages from the edge cache.

This module has no configuration settings of its own, just enable it and it will pass along information already present in Drupal to the Global CDN.

[https://www.drupal.org/project/pantheon_advanced_page_cache](https://www.drupal.org/project/pantheon_advanced_page_cache)

### Warmer

Provides all the necessary infrastructure to orchestrate your cache warming processes. You can warm the cache of your critical entities (and more!) right after you deploy to production. Additionally cron will keep them warm for you.
All these operations are executed asynchronously to avoid impacting the users.
includes CDN warmer

[https://www.drupal.org/project/warmer](https://www.drupal.org/project/warmer)

## Calendars

### Add To Calendar Button

Add to Calendar Module makes use of [Add to Calendar.com's](https://addtocalendar.com/) service which provides free buttons for event pages on websites and in emails. The button supports all modern browsers and platforms. It provides enough configurations for a really flexible experience.

The module extends Datetime and Datetime Range field formatters to append the "Add to Calendar" button next to the date field. If the date field is multivalued than it is configurable to show the button beside a particular date field or for all.

It also provides a new `addtocalendar` field type which can be used to add \"Add to Calendar\" button, Also it provides the option for the end user to decide if they want to enable "Add to Calendar" button or not. This new field can then be easily used in views to show \"Add to Calendar\" button with custom listings.

When the button is clicked on, the event is exported to the corresponding website with proper information in the next tab where user can add the event to their calendar.

External CSS and JS files are provided by [Addtocalendar.com](https://addtocalendar.com/) and used by this module.

[https://www.drupal.org/project/addtocalendar](https://www.drupal.org/project/addtocalendar)

## Components

### Single Directory Components: Block
This module lets you put Single Directory Components in the page using blocks. This includes the regular block layout, layout builder, and any other tool that renders blocks in a page.

[https://www.drupal.org/project/sdc_block](https://www.drupal.org/project/sdc_block)




## Content Entry/Editing

### Automatic Entity Label
"Automatic Entity Label" is a small and efficient module that allows hiding of entity label fields. To prevent empty labels it can be configured to generate the label automatically by a given pattern.

This can be used on any entity type, including e.g. for node titles, comment subjects, taxonomy term names and profile2 labels.

Patterns for automatic labels can be constructed with tokens. Drupal core provides a basic set of tokens. For a token selection widget install the [token module.](https://www.drupal.org/project/token_php) Some entity types (e.g. profile2) provide tokens via the `entity_token` module (part of entity).

Advanced users can use PHP code for automatically generating labels. This requires the [token module.](https://www.drupal.org/project/token_php)

[https://www.drupal.org/project/auto_entitylabel](https://www.drupal.org/project/auto_entitylabel)

### Conditional Fields
Define dependencies between fields based on their states and values. It provides a user interface to the States API, plus the ability to modify fields appearance and behavior based on various conditions when viewing content.

- It allows you to manage sets of dependencies between fields. When a field is “dependent”, it will only be available for editing and displayed if the state of the “dependee” field matches the right condition.  
- When editing a node (or any other entity type that supports fields, like users and categories), the dependent fields are dynamically modified with the States API.
- A simple use case would be defining a custom “Article teaser" field that is shown only if a "Has teaser" checkbox is checked, but much more complex options are available.

[https://www.drupal.org/project/conditional_fields](https://www.drupal.org/project/conditional_fields)

### Existing Values Autocomplete Widget
Provides an autocomplete widget for text fields that suggests all existing (previously entered) values for that field. This provides more flexibility than "allowed values" for the content editor to add new values. At that same time, it is simpler in many cases than creating a taxonomy vocabulary (no hierarchies, no separate system, no permissions headaches, no rendered pages per term).
[https://www.drupal.org/project/existing_values_autocomplete_widget](https://www.drupal.org/project/existing_values_autocomplete_widget)

### Inline Entity Form

Provides a widget for inline management (creation, modification, removal) of referenced entities.The primary use case is the parent -> children one (product display -> products, order -> line items, etc.), where the child entities are never managed outside the parent form. Existing entities can also be referenced.  This module has it's origins in the [Drupal Commerce module.](https://www.drupal.org/project/commerce).


### Link Field Autocomplete Filter
Currently the autocomplete in the Link Field widget always shows content suggestions from all content (node) types. This module adds a Link Field configuration for filtering the suggested content types in the autocomplete field.

[https://www.drupal.org/project/link_field_autocomplete_filter](https://www.drupal.org/project/link_field_autocomplete_filter)


## Cleanup

### Node Revision Delete

The Node Revision Delete module lets you track and prune old revisions of content types.

You can configure how many revisions you want to keep per content type and configure how long revision should be kept. When saving the configuration you can optionally start a batch job to queue all content to delete revisions that are allowed to be deleted. Includes drush command(s).

[https://www.drupal.org/project/node_revision_delete](https://www.drupal.org/project/node_revision_delete)

## Content Management

### Allow only one

The Allow Only One module was created to prevent duplicate content save, based on a combination of field values. This module provides a new field type that stores configuration and is later used during validation. Important: This only works and has been tested on Node and Taxonomy_Term

[https://www.drupal.org/project/allow_only_one](https://www.drupal.org/project/allow_only_one)

### Content Autogrid

This module provides a table for content administrators to quickly review all content of a specific content entity type (e.g. node, taxonomy term, etc) and bundle (content type, vocabulary, etc).

Each configured field will be shown in a column, and the data for that field shown for each row. Additionally, operations links (edit, delete, etc) will be added based on the permissions for the current user.

The idea is to emulate the grid view provided in popular database administration tools, as a quick way to review available data.

[https://www.drupal.org/project/autogrid](https://www.drupal.org/project/autogrid)

### Entity Clone

Allows you to clone nodes
This module add a new entity operation which allows to clone many of the entities (config & content) provided by the Drupal core. The old Node clone module doesn’t exist for D8/9/10, rather use entity clone.  

[https://www.drupal.org/project/entity_clone](https://www.drupal.org/project/entity_clone)

See also: [Replicate (API only)](https://www.drupal.org/project/replicate)

### Entity Bulk Clone
Entity Bulk Clone provides the clone features for nodes using views bulk operations.

[https://www.drupal.org/project/entity_bulk_clone](https://www.drupal.org/project/entity_bulk_clone)

### Entity Reference Revisions
Adds an Entity Reference field type with revision support, allowing specific entity revisions to be references. This is useful for modules like Paragraphs and Inline Entity Form.

A common use case is where an entity is actually part of a parent entity (with an embedded entity form). When the parent entity is updated, the referenced entity is also updated, thus the previous revision of the parent entity should still be pointing to the previous version of the entity to fully support revision diff and rollback.

[https://www.drupal.org/project/entity_reference_revisions](https://www.drupal.org/project/entity_reference_revisions)



### File Download (includes download counter)
Provides a formatter to use that allows users to download file and image entities directly.  Also includes a separate module to count downloads and display results in a View. Modelled off the statistics module which counts content views, this counts downloads using the File Download formatter.

[https://www.drupal.org/project/file_download](https://www.drupal.org/project/file_download)

### Quick Node Clone

Adds a "Clone" tab to a node. When clicked, a new node is created and fields from the previous node are populated into the new fields.
[https://www.drupal.org/project/quick_node_clone](https://www.drupal.org/project/quick_node_clone)

## Data Import

### Feeds

Feeds is the module for importing or aggregating data into nodes, users, taxonomy terms and other content entities using a web interface without coding a migration. Data can be imported from various formats, such as CSV, JSON, XML and RSS feeds.

- [Docs for creating/using feed](https://www.drupal.org/docs/8/modules/feeds/creating-and-editing-import-feeds)
- [Import your content with Feeds with Drupal by Omar Lopesino - December 2018]
[https://www.drupal.org/project/feeds](https://www.drupal.org/project/feeds)

### Feeds Tamper
Feeds Tamper provides a small plugin architecture for Feeds to modify data before it gets saved. Several plugins are available by default and are described in the examples section below. Additional plugins can be added in separate modules or through the issue queue.

Examples:
- Replace every instance of 'dog' with 'cat'.
- Filter items based on keywords or vocabularies.
- Make every letter uppercase, lowercase, or capitalize every first letter.
- Break a comma separated list of words into Taxonomy terms or a multivalued text field.
- Combine separate 'firstname' and 'lastname' fields into one 'name' field.
- Convert urls from relative to absolute.
- Incredibly simple plugin architecture allowing you to do almost anything to Feeds' data. This comes with simple configuration and exportability(i.e. Features.)

[https://www.drupal.org/project/feeds_tamper](https://www.drupal.org/project/feeds_tamper)

## Development

### Module Builder

A module which auto-generates a skeleton or "scaffolding" for a module, along with hints on how to fill them in. Useful for newbie developers to learn how Drupal code works, and seasoned developers who are too lazy to look up what arguments a function has to take.

Module Builder is unlike any other code generator in that it analyses your site's code to detect plugin types, hooks, services, and so on. It can then generate code for any of these, whether from core, contrib, or your custom code.

But more than this, module builder can generate:

- Content and config entity types
- Plugins of just about any type, with injected services
- Forms, with injected services
- Routes and controllers, with injected services
- Permissions
- Services
- Plugin types
- PHPUnit test case classes, and test modules
- An api.php file to document the module's hooks
- An admin settings form
- A README file
- ... and more.

You can [watch a demonstration](https://www.youtube.com/watch?v=jcKZwOgbE4w) of some of the components that can be generated.

[https://www.drupal.org/project/module_builder](https://www.drupal.org/project/module_builder)

### WebProfiler

WebProfiler adds a toolbar at the bottom of every page and shows you all sorts of stats, such as the amount of database queries loaded on the page, which services are used, and much more.

Webprofiler provides detailed insights into various aspects of a Drupal request, including execution time, memory usage, database queries, forms, service calls, and more. By presenting a comprehensive breakdown of the backend performance for each page request, it enables developers to identify bottlenecks, understand system behavior, and make informed decisions to enhance site performance. This tool is invaluable for performance analysis, helping to ensure that Drupal sites run as efficiently as possible.

[https://www.drupal.org/project/webprofiler](https://www.drupal.org/project/webprofiler)

## Email

### SMTP Authentication Support

This module allows Drupal to bypass the PHP mail() function and send email directly to an SMTP server. The module supports SMTP authentication and can even connect to servers using SSL if supported by PHPMailer. It sends mail via the SMTP protocol using the PHPMailer library. While it can be a standalone module for the Drupal mailsystem, you can also use the mailsystem module to manage multiple mailer modules including SMTP.

What does SMTP NOT do? This module does not support (and will not support) sending mail via other protocols or APIs other than SMTP.  For Example: While services like sendgrid do support SMTP, you may get a better experience using the [Sendgrid Integration Module](https://www.drupal.org/project/sendgrid_integration)

### Reroute Email

This module intercepts all outgoing emails from a Drupal site and reroutes them to one or more predefined configurable email addresses.  It also logs any email attempts to watchdog.

[Reroute Email Module](https://www.drupal.org/project/reroute_email)

## Entities

###Storage Entities

A new entity type for managing data that should be stored in the database, but only displayed within or associated with other content. If you're using a solution like Rabbit Hole to prevent direct access to an entire content type, that content might be better stored as storage entities.
Lightweight by design, but fieldable just like content types. This module is similar to Basic Data but where that module requires a "data" blob in addition to a name, this module only requires a name, and any other fields can be completely custom to suit your specific needs. Also, this module is designed to be revisionable and translatable.

[Storage Entities](https://www.drupal.org/project/storage)


## Essential Utility Modules for Every Site

Well maybe not every site, but certainly for most sites, your life and your content editor's lives will go better with these modules.

### Admin Toolbar
The Admin Toolbar module intends to improve the default Toolbar (the administration menu at the top of your site) to transform it into a drop-down menu, providing a fast access to all administration pages.
The module works on the top of the default toolbar core module and is therefore a light module and keeps all the toolbar functionalities (shortcut / media responsive).
[https://www.drupal.org/project/admin_toolbar](https://www.drupal.org/project/admin_toolbar)

### Module filter

The modules list page can become quite big when dealing with a fairly large site or even just a dev site meant for testing new and various modules being considered. What this module aims to accomplish is the ability to quickly find the module you are looking for without having to rely on the browsers search feature which more times than not shows you the module name in the 'Required by' or 'Depends on' sections of the various modules or even some other location on the page like a menu item.

[https://www.drupal.org/project/module_filter](https://www.drupal.org/project/module_filter)


### Pathauto

The Pathauto module automatically generates URL/path aliases for various kinds of content (nodes, taxonomy terms, users) without requiring the user to manually specify the path alias. This allows you to have URL aliases like /category/my-node-title instead of /node/123. The aliases are based upon a "pattern" system that uses tokens which the administrator can change.

[https://www.drupal.org/project/pathauto](https://www.drupal.org/project/pathauto)


### Redirect

Provides the ability to create manual redirects and maintain a canonical URL for all content, redirecting all other requests to that path.

[Redirect](https://www.drupal.org/project/redirect)


## Fields

### Field Group
Fieldgroup groups fields together. All fieldable entities will have the possibility to add groups to wrap their fields together. Fieldgroup comes with default HTML wrappers like vertical tabs, horizontal tabs, accordions, fieldsets or div wrappers. The field group project is a follow-up on the field group module in CCK.

[https://www.drupal.org/project/field_group](https://www.drupal.org/project/field_group)

### Field tools
A collection of useful UI tools for working with fields.
- Overview of all fields, filterable, sortable, with links to edit single instances
- Overview of reference fields, filterable, sortable, with links to edit single instances
- Graph of reference fields, filterable
- Clone a single field instance to other bundles
- Clone field instances in bulk to other bundles
- Clone form and view displays to other bundles, with support for Field Groups

[https://www.drupal.org/project/field_tools](https://www.drupal.org/project/field_tools)

### Dynamic Entity Reference
This module provides a field type for entity references that can reference multiple entity types. It is based on the core Entity Reference module but allows you to reference **more than one** entity type. It also provides a widget for the entity reference field that allows you to select the entity type to reference on the fly.
[https://www.drupal.org/project/dynamic_entity_reference](https://www.drupal.org/project/dynamic_entity_reference)

### Views Reference Field
This lets you add a view (and a view's display) to a content type so it gets rendered when the node gets rendered.

[https://www.drupal.org/project/viewsreference](https://www.drupal.org/project/viewsreference)

## Forms

### Protected Forms
Successor of Protected Permissions module.  Light-weight, non-intrusive spam protection module that enables rejection of node, comment, webform, user profile, contact form, private message and revision log submissions which contain undesired language characters or preset patterns.

[https://www.drupal.org/project/protected_forms](https://www.drupal.org/project/protected_forms)

## Icons

### Font Awesome Icons
This module provides a CKEditor plugin to allow users to select Font Awesome icons directly from the editor, as well as a Font Awesome Icon Field to attach directly to entities. Additionally, it also provides a core media entity type for creating Font Awesome icons as media entities.

[https://www.drupal.org/project/fontawesome](https://www.drupal.org/project/fontawesome)

## Images

### Focal Point
Focal Point allows you to specify the portion of an image that is most important. This information can be used when the image is cropped or cropped and scaled so that you don't, for example, end up with an image that cuts off the subject's head.

[https://www.drupal.org/project/focal_point](https://www.drupal.org/project/focal_point)

### Image URL Formatter
This module add a url formatter for image field which allows you to output the image url directly.

It offers 3 options that are not in views:
- Support Image styles
- Supports multivalues, there is a "Multiple field settings" when image field is multivalue.
- Works without views, you could output image URL on a node page

[https://www.drupal.org/project/image_url_formatter](https://www.drupal.org/project/image_url_formatter)

### Svg Image
This module changes default image field widget and formatter to allow use SVG image with the standard Image field.

Using SVG Image module
you will not have to use another field type
to load SVG image. Load SVG files into the Image field, it is not needed to create file field or special "SVG" type field.

Additional features (beyond the main functionality):

- Ability to select width and height of the image in formatter settings
- Ability to render svg image as `<img>` or `<svg>` tags.
- Responsive image support. Please activate svg_image_responsive submodule to get such functionality

[https://www.drupal.org/project/svg_image](https://www.drupal.org/project/svg_image)

## Other

### Duration Field
This module creates a new duration field, that can be added to any entity. A duration field can collect any combination of year, month, day, hour, minute and second. Field settings allow for the site builder to determine what level of granularity they wish to collect from users, so if the required level of granularity is a date, the field settings would be set to collect year, month and day. The module is flexible in that users could choose to only collect year and seconds. While this doesn't really make sense for most logical data collection, the module has the flexibility that allows it to happen.

[https://www.drupal.org/project/duration_field](https://www.drupal.org/project/duration_field)

### Entity Queue

The follow-on module from the original [Nodeque](https://www.drupal.org/project/nodequeue) by [Earl Miles/merlinofchaos](https://www.drupal.org/u/merlinofchaos) which allows users to collect nodes in an arbitrarily ordered list. The order in the list can be used for a any purpose, such as: A user’s favorite music albums, a block listing teasers for the five top news stories on a site or a group of favorites from which one is randomly displayed. 

The Entityqueue module allows users to create queues of any entity type. Each queue is implemented as an Entity Reference field, that can hold a single entity type. For instance, you can create a queue of: Nodes, Users, Taxonomy Terms, etc. Entityqueue provides Views integration, by adding an Entityqueue relationship to your view, and adding a sort for Entityqueue position.

[https://www.drupal.org/project/entityqueue](https://www.drupal.org/project/entityqueue)

### Smart Trim
Smart Trim implements a new field formatter for textfields (text, text_long, and text_with_summary) that improves upon the "Summary or Trimmed" formatter built into Drupal core.

[https://www.drupal.org/project/smart_trim](https://www.drupal.org/project/smart_trim)

## Paragraphs

### Paragraphs
Paragraphs allows grouping together fields to make a cohesive unit of content.

Instead of putting all their content in one WYSIWYG body field including images and videos, end-users can now choose on-the-fly between pre-defined Paragraph Types independent from one another. Paragraph Types can be anything you want from a simple text block or image to a complex and configurable slideshow.

[https://www.drupal.org/project/paragraphs](https://www.drupal.org/project/paragraphs)

### Paragraphs Edit

This module adds contextual links to paragraphs to edit, delete and clone paragaphs.

[https://www.drupal.org/project/paragraphs_edit](https://www.drupal.org/project/paragraphs_edit)

### Paragraphs Report

The Paragraphs Report module will parse nodes of certain content types that you check on the settings page, and make a catalog of what paragraphs are used on which pages.

The use case for this report is when you want to know which pages a specific paragraph type is used.

[https://www.drupal.org/project/paragraphs_report](https://www.drupal.org/project/paragraphs_report)

### Paragraphs Sets

Paragraphs Sets allows to create different sets of paragraphs.

These sets can be automatically added to a new entity or selected while creating/editing the entity.

This allows editors to add content way faster because they do not need to add all required paragraphs manually and can focus on the content.

[https://www.drupal.org/project/paragraphs_sets](https://www.drupal.org/project/paragraphs_sets)

### Layout Paragraphs

Layout Paragraphs provides an intuitive drag-and-drop experience for building flexible layouts with paragraphs. The module was designed from the ground up with paragraphs in mind, and works seamlessly with existing paragraph reference fields.

**Key Features**
- Intuitive drag-and-drop interface.
- Works with existing paragraph reference fields.
- Flexible configuration – site admins choose which paragraphs to use as `layout sections`, and which layouts should be available for each.


## Permissions

[How to restrict access to content by role in Drupal 8 - Aug 2018](https://www.optasy.com/blog/how-do-you-restrict-access-content-drupal-8-6-modules-will-do-job-you)

### Node View Permissions
Node view permissions module enables permissions \"View own content\" and \"View any content\" for each content type on permissions page as it was on Drupal 6. It's as simple as that.  It's implemented in a non-conflict way, so you can use it with any other permissions related module.

[https://www.drupal.org/project/node_view_permissions](https://www.drupal.org/project/node_view_permissions)

### Permissions by Term

Per default, Drupal allows you only to restrict access to Drupal nodes by coupling node content types to user roles. This module extends Drupal functionality for restricting view access to single nodes via taxonomy terms. If you have installed the `Permissions by Entity` submodule, any other content entity type, such as media entities, can be controlled in access restriction, too.

Please notice, that edit access is provided by hiding nodes from editors on the content admin views. There are no explicit create, edit or delete permissions provided.

Taxonomy term permissions can be coupled to specific user accounts and/or user roles. Taxonomy terms are part of the Drupal core functionality. Since Permissions by Term is using Node Access Records, every other core system will be restricted:

- search results
 - Works well with Search API modules search result lists, since PbT version 8.x-2.0
 - Drupal core search
- menu items
- views list items
- content from all content entity types (nodes, media)

[https://www.drupal.org/project/permissions_by_term](https://www.drupal.org/project/permissions_by_term)

## Problem solving 

### Easy Install
A module built to resolve and avoid the error: \"Unable to install already exists active configuration\" when reinstalling/uninstalling Drupal modules. It works even if the module's configs are not in `.yml` files or in their config folder. It has an option to purge configuration object for  uninstalled modules and helps to remove configuration objects while uninstalling a module without requiring the use of Drush or Devel.

[https://www.drupal.org/project/easy_install](https://www.drupal.org/project/easy_install)


### Module Missing message Fixer

Displays a list of missing modules and lets you fix the entries. Also deletes leftover config from missing modules. Make sure you run drush cex or export the config from the UI after using this. It is also Drush 9 compatible.


[Module Missing message Fixer](https://www.drupal.org/project/module_missing_message_fixer)



## Rest

### REST UI
This module provides a user interface to make it easier to configure the core REST modules provided by Drupal. It provides a new settings page for each REST resource where you can configure the allowed methods (GET, POST, etc), formats (json, xml, etc), authentication, serialization, and validation. It also provides a new configuration form to configure the serialization format for each entity type.  Adding the [Simple OAuth](https://www.drupal.org/project/simple_oauth) module will allow you to configure OAuth2 authentication for your REST resources.

[https://www.drupal.org/project/restui](https://www.drupal.org/project/restui)



## Security/Spam Protection

### Antibot
Antibot is an extremely lightweight module designed to eliminate robotic form submissions on your website in an innovative-fashion. The module works completely behind the scenes and doesn't require any interaction from the end-users (no annoying CAPTCHAs!). The only requirement to the end user is that they must have JavaScript enabled. If they do not, the protected forms will be hidden and a message will appear, telling the user that the form requires JavaScript be enabled in order to use it.

[https://www.drupal.org/project/antibot](https://www.drupal.org/project/antibot)


### Automated Logout

This module provides a site administrator the ability to log users out after a specified time of inactivity. It is highly customisable and includes "site policies" by role to enforce logout.

[https://www.drupal.org/project/autologout](https://www.drupal.org/project/autologout)

### Automatic IP ban (Autoban)

The "Automatic IP Ban" module enhances Drupal's site security by providing administrators with sophisticated tools to prevent unwanted access. It extends the core Ban module's functionality, allowing for more nuanced control over visitor bans based on IP addresses. Through the Autoban feature, IP banning becomes automated, leveraging rules defined against watchdog table entries. This feature necessitates the activation of the Database Logging module and can integrate with either the core Ban module or the Advanced Ban module for a comprehensive banning strategy.

[https://www.drupal.org/project/autoban](https://www.drupal.org/project/autoban)

### Drupal Perimeter Defence
Basic perimeter defence for a Drupal site. This module bans the IPs who send suspicious requests to the site. Use this if you get a lot of requests to 'wp-admin' or to .aspx urls on a Linux server, or other similar requests. The URL patterns that result in a ban can be configured in the admin settings. The module is optimized for performance and designed to be activated when a Drupal site is targeted by hackers or bots.  There is a companion module [auto_unban](https://www.drupal.org/project/auto_unban) which augments core's ban module to automatic unban IP's after a period of time. This is best used with automatic ban modules such as perimeter.

[https://www.drupal.org/project/perimeter](https://www.drupal.org/project/perimeter)


### Flood control

Flood Control provides an interface for hidden flood control variables (e.g. login attempt limiters) and makes it possible for site administrators to remove IP addresses and user ID's from the flood table.

[https://www.drupal.org/project/flood_control](https://www.drupal.org/project/flood_control)

### Honeypot
Honeypot uses both the honeypot and timestamp methods of deterring spam bots from completing forms on your site. These methods are effective against many spam bots, and are not as intrusive as CAPTCHAs or other methods which punish the user. The module currently supports enabling for all forms on the site, or particular forms like user registration or password reset forms, webforms, contact forms, node forms, and comment forms.

[https://www.drupal.org/project/honeypot](https://www.drupal.org/project/honeypot)

### Key

Key provides the ability to improve Drupal security by managing sensitive keys (such as API and encryption keys). It gives site administrators the ability to define how and where keys are stored, which allows the option of a high level of security and allows sites to meet regulatory or compliance requirements.

Examples of the types of keys that could be managed with Key are: 
- An API key for connecting to an external service, such as PayPal, MailChimp, Authorize.net, UPS
- an SMTP mail server
- Amazon Web Services
- A key used for encrypting data using the [encrypt](https://www.drupal.org/project/encrypt) module

[https://www.drupal.org/project/key](https://www.drupal.org/project/key)

### Protected Forms
Successor of Protected Permissions module.  Light-weight, non-intrusive spam protection module that enables rejection of node, comment, webform, user profile, contact form, private message and revision log submissions which contain undesired language characters or preset patterns.

[https://www.drupal.org/project/protected_forms](https://www.drupal.org/project/protected_forms)

### Security Kit

SecKit provides Drupal with various security-hardening options. This lets you mitigate the risks of exploitation of different web application vulnerabilities.

[https://www.drupal.org/project/seckit](https://www.drupal.org/project/seckit)

## SEO

### Pathauto
The Pathauto module automatically generates URL/path aliases for various kinds of content (nodes, taxonomy terms, users) without requiring the user to manually specify the path alias. This allows you to have URL aliases like /category/my-node-title instead of /node/123. The aliases are based upon a "pattern" system that uses tokens which the administrator can change.

[https://www.drupal.org/project/pathauto](https://www.drupal.org/project/pathauto)

## Site Navigation/Menus

### Menu Block

This module provides configurable blocks of menu links with additional features not available in Drupal 8+ core. Drupal core allows you to display blocks of menu links starting with any desired level of a menu and limited to any desired depth. This module provides additional configuration, so you can choose to expand all menu links with children or to root the menu tree to a specific menu item.

[https://www.drupal.org/project/menu_block](https://www.drupal.org/project/menu_block)

### Menu Breadcrumb

This module allows you to use the menu the current page belongs to for the breadcrumb, generating breadcrumbs from the titles of parent menus.

- Select and re-order the menus on which you want the menu-based breadcrumbs
- Append the page title to the breadcrumb (either as a clickable url or not)
- Hide the breadcrumb if it only contains the link to the front page
- Generate the breadcrumb from the URL structure if content does not belong to a menu
- If the "Taxonomy Attachment" option is selected for a menu, and if the current page belongs to a taxonomy that is on that menu, it will inherit the taxonomy page's menu breadcrumbs (e.g., for blog entries that aren't on any menu).
- Other breadcrumb builders (e.g., the path & title-based Drupal 8 default breadcrumb builder) will be used if there is no applicable menu based breadcrumb.

[https://www.drupal.org/project/menu_breadcrumb](https://www.drupal.org/project/menu_breadcrumb)

### Menu Trail By Path
Menu Trail By Path sets the active-trail on menu items according to the current url. For example if you are at `yoursite.com/blog/category1/article1` menu, items with these paths will get the `active-trail` class on them and expand accordingly:

- blog
- blog/category1
- blog/category1/article1

This is particularly useful if you want a lot of nodes to appear as children of certain nodes, taxonomy, term, views, referenced nodes etc. but do not want to add them all to the menu. e.g. hundreds of blog articles.

Menu Trail By Path is best used in conjunction with Pathauto.

This module is similar to Menutrails (D6) and Menu Position (D7), except no configuration is needed. It uses the path URL to determine the active-trail instead of setting rules for each node type. It also works for non-node pages such as taxonomy term and views. Just enable the module to see the results.

## Social media

### AddToAny Share Buttons
Share buttons for Drupal including AddToAny's universal sharing button, Facebook, Twitter, Pinterest, WhatsApp, Reddit, SMS, email and many more.

Vector share buttons use AddToAny SVG sharing icons. AddToAny vector icons load efficiently, are mathematically precise, scalable to any size, and stunning on High-PPI screens such as Retina and Retina HD displays.

[https://www.drupal.org/project/addtoany](https://www.drupal.org/project/addtoany)

### Social media share
The social media share module allows the user to share the current page to different social media platforms. It is rendered as a block, you can place it anywhere on your site. It also provides social media field type so that you can add it as a field in entity and take all benefits from field API.

It can share any page of the site whether it is a node, term, panels, view pages so on.

Currently, the module provides many services by default:
- Facebook share
- Facebook messenger
- Linkedin
- Twitter
- Pinterest
- Email (Client email service(:mailto) , Forward email as model dialogue, forward email as a separate page)
- Whatsapp ( Optional, needs to be enabled in the configuration )

You have full flexibility to add more services, modify the elements before render, change orders or so on. 

[https://www.drupal.org/project/social_media](https://www.drupal.org/project/social_media)

## Site Stability

### Memory limit policy
Memory limit policy allows you to override the default php memory_limit based on various constraints. e.g. you can set a different memory limit for all users depending on their roles. You can also set a different memory limit for different paths, HTTP methods, query parameters and routes.

[https://www.drupal.org/project/memory_limit_policy](https://www.drupal.org/project/memory_limit_policy)


### Node Access Rebuild Progressive
This module provides an alternative means of rebuilding the Content Access table.

It solves the problem of the default core behaviour, which delete all entries first in the `content_access` table and then rebuild the grants for all nodes.  This means the whole site is basically unusable during the whole operation. For sites with a large number of nodes and/or lots of complex hook_node_grants implementations, it can be very lenghty and results in a lot of downtime. Although this means someone could potentially still access some content they should no longer have the rights to until the new rules are in place, it does mean that the site can continue to operate normally while the access rebuild takes place in the background. It works by processing nodes in chunks, from highest `node.nid` to lowest, until all nodes have been recomputed

[https://www.drupal.org/project/node_access_rebuild_progressive](https://www.drupal.org/project/node_access_rebuild_progressive)


## Twig

### Bamboo Twig
The Bamboo Twig module provides some Twig extensions with some useful functions and filters aimed to improve the development experience.

Bamboo Twig has a lot of advantages and brings a lot of new features to the Twig landscape of Drupal.
It boosts performance by using lazy loading, improves the code quality with automated workflow. It also includes automated unit and kernel tests to ensure stability.

[https://www.drupal.org/project/bamboo_twig](https://www.drupal.org/project/bamboo_twig)

### Snippet Manager
Snippets are pieces of Twig code that can be used to build site layouts. The module provides an administrative interface to manage and render snippets on the site.

[https://www.drupal.org/project/snippet_manager](https://www.drupal.org/project/snippet_manager)

### Twig Tweak
Twig Tweak is a small module which provides a Twig extension with some useful functions and filters that can improve development experience.

[https://www.drupal.org/project/twig_tweak](https://www.drupal.org/project/twig_tweak)


## User Interface

### select2
Integrates Drupal autocomplete and select fields with the [Select2 jQuery library.](https://select2.org/) Select2 is a jQuery based replacement for select boxes. It supports searching, remote data sets, and infinite scrolling of results. The look and feel of Select2 is inspired by the excellent [Chosen library.](https://harvesthq.github.io/chosen/)
This includes views support and provides a render element (for usage in forms), and two field widgets. One for simple select fields and another for entity reference fields.

The render element supports several select2 features:
- Single and multiple selection
- Internationalization
- Integrates nicely with the seven theme
When the field widget is used in the entity reference context this module provides more features:
- Autocomplete: The select options will not be rendered in the page and instead fetched by API during typing.
- Autocreate: Like core's entity reference field this widget can create new entities on the fly.
[https://www.drupal.org/project/select2](https://www.drupal.org/project/select2)

Other JS select modules and libraries include: 
- [Selectize](https://selectize.github.io/selectize.js) with its Drupal module: [https://www.drupal.org/project/selectize](https://www.drupal.org/project/selectize)
- [Chosen](https://harvesthq.github.io/chosen/) with its Drupal module: [https://www.drupal.org/project/chosen](https://www.drupal.org/project/chosen)

## Workflow

### ECA: Event - Condition - Action

ECA is a powerful, versatile, and user-friendly rules engine for Drupal 9+. The core module is a processor that validates and executes event-condition-action plugins. Integrated with graphical user interfaces like BPMN.iO, Camunda or other possible future modellers, ECA is a robust system for building conditionally triggered action sets.

[https://www.drupal.org/project/eca](https://www.drupal.org/project/eca)

## Views

### Views Reference Field

This module provides a field that can reference a view and show the results of the view as a field in the content type. This is useful when you want to show a list of related content in a field.

In Modern Drupal, Views are entities and the core Entity Reference Module is able to reference Views, but not Views displays. This module leverages core entity reference module functionality to add the display ID so that a View can be rendered in a field formatter.

[https://www.drupal.org/project/viewsreference](https://www.drupal.org/project/viewsreference)




## Wysiwyg

### Entity Embed

Entity Embed allows any entity to be embedded within a text area using a WYSIWYG editor.

[https://www.drupal.org/project/entity_embed](https://www.drupal.org/project/entity_embed)

## Resources
- [What every Drupal website should have from the start - August 2023](https://roose.digital/en/blog/drupal/what-every-drupal-website-should-have-start)
- [50 Drupal modules every Drupal professional should know about - September 2021](https://robertroose.com/blog/drupal/50-drupal-modules-every-drupal-professional-should-know-about)
- [27 more Drupal modules every Drupal professional should know about - October 2021](https://robertroose.com/blog/drupal/27-more-drupal-modules-every-drupal-professional-should-know-about)
- [5 best modules to implement Drupal Google Maps - April 2023](https://gole.ms/blog/google-maps-drupal)
- [The best new Drupal modules I found at DrupalCon Lille Oct 2023](https://cyberschorsch.dev/drupal/best-new-drupal-modules-i-found-drupalcon-lille)

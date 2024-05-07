---
title: Routes
---

# Routes and Controllers
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=routes.md)



## Set the page title dynamically

This code shows how to set the title statically in the  `module.routing.yml` file, as well as how to call a function like `getTitle()` to return it so it can be dynamically generated:

```yaml
org_onions_summary:
  path: 'onions/{term_id}'
  defaults:
    _controller: '\Drupal\org_onions\Controller\OnionsController::buildOnionsSummary'
#    Static Title
#    _title: 'Opinions Summary'
#    Dynamic Title
    _title_callback: '\Drupal\org_onions\Controller\OnionsController::getTitle'
  requirements:
    _permission: 'access content'
```


In your controller, add the function `getTitle()`. This function can actually be called whatever you like.

```php
/**
 * Returns a page title.
 */
public function getTitle() {
  $current_path = \Drupal::service('path.current')->getPath();
  $path_args = explode('/', $current_path);
  $boss_name = $path_args[2];
  $boss_name = ucwords(str_replace("-", " ", $boss_name));

  $config = \Drupal::config('system.site');
  $site_name = $config->get('name');
  return  $boss_name . ' Onions | ' . $site_name;

  //or
  return  $boss_name . ' onions | ' . \Drupal::config('system.site')->get('name');
}
```

## ControllerBase shortcuts for your controllers

`ControllerBase.php` comes prepackaged with some useful functions to get the following services statically:

```php
protected function entityTypeManager() {
protected function entityFormBuilder() {
protected function cache($bin = 'default') {
protected function config($name) {
protected function keyValue($collection) {
protected function state() {
protected function moduleHandler() {
protected function formBuilder() {
protected function currentUser() {
protected function languageManager() {
```
This allows quick access from within your controllers to these services if you need to do things like:

```php
// Make an entityQuery
$storage = $this->entityTypeManager()->getStorage('node');
$query = $storage->getQuery();
$query
  ->accessCheck(TRUE)
  ->condition('type', 'article')
  ->condition('title', $name)
  ->count();
$count_nodes = $query->execute();

// Or.

// Get info about the current user.
$account = $this->currentUser();
$username = $account->getAccountName();
$uid = $account->id();
$message = "<br>Account info user id: " . $uid . " username: " . $username;

// Or.

// Get the site name, slogan and email from the system.site config.
$config = $this->config('system.site');
$site_name = $config->get('name');
$slogan = $config->get('slogan');
$email = $config->get('mail');
```



## Return JSON data from a route
Using this `general-routing.yml` file:

```yaml
general.json_example1:
  path: '/general/json-example1/nid/{nid}'
  defaults:
    _title: 'JSON Play1'
    _controller: '\Drupal\general\Controller\CachePlay1::jsonExample1'
#  methods: [GET]
  requirements:
    _permission: 'access content'
  options:
    parameters:
      nid:
        type: 'integer'
```

You can cause Drupal to return JSON data.  

```php
  /**
   * Example of a simple controller method that returns a JSON response.
   *
   * Note. You must enable the RESTful Web Services module to run this.
   *
   * @param int $nid
   *  The node ID.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
    public function jsonExample1(int $nid): JsonResponse {
      if ($nid == 0) {
//        $build['#cache']['tags'][] = 'node_list';
        $data = [
          'nid' => $nid,
          'name' => 'Fred Bloggs.',
          'age' => 45,
          'occupation' => 'Builder',
        ];
      }
      else {
        $data = [
          'nid' => $nid,
          'name' => 'Mary Smith',
          'age' => 35,
          'occupation' => 'Rocket Scientist',
          ];
      }

      return new JsonResponse($data, 200, [
      'Cache-Control' => 'public, max-age=3607',
    ]);
  }
```

This will return JSON data like : `{"nid":0,"name":"Fred Bloggs.","age":45,"occupation":"Builder"}` and in the response headers, you will get `Cache-Control: max-age=3607, public`. If you want to make the response cacheable, see [caching json responses](caching#caching-json-responses)


::: tip Note
You do not need JSON:API module enabled to use this code
:::


## Disable caching on a route

This will cause Drupal to rebuild the page internally on each page load but won't stop browsers or CDN's from caching. The line: `no_cache: TRUE` is all you need to disable caching for this route.

```yaml
requirements:
  _permission: 'access content'
options:
  no_cache: TRUE
```

## Generate route and controller with drush generate

Drush has the ability to generate code to start you off.  Use `drush generate module` and or `drush generate controller` to get a nice starting point for you to write your own controllers.

For more, on generating controllers see <https://www.drush.org/latest/generators/controller/>


This is what it looks like to generate a controller:

```sh
$ drush generate controller

 Welcome to controller generator!
----------------------------------

 Module machine name [web]:
 ➤ general

 Class [GeneralController]:
 ➤ ExampleController

 Would you like to inject dependencies? [No]:
 ➤

 Would you like to create a route for this controller? [Yes]:
 ➤

 Route name [general.example]:
 ➤ general.book_example

 Route path [/general/example]:
 ➤ /general/book_example

 Route title [Example]:
 ➤ Book Example

 Route permission [access content]:
 ➤

 The following directories and files have been created or updated:
-------------------------------------------------------------------
 • /Users/selwyn/Sites/d9book2/web/modules/custom/general/general.routing.yml
 • /Users/selwyn/Sites/d9book2/web/modules/custom/general/src/Controller/ExampleController.php
```

The file `general.routing.yml` will then contain:

```yaml
general.book_example:
  path: '/general/book_example'
  defaults:
    _title: 'Book Example'
    _controller: '\Drupal\general\Controller\ExampleController::build'
  requirements:
    _permission: 'access content'
```

The `ExampleController.php` file has these contents:

```php
<?php

namespace Drupal\general\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for General routes.
 */
class ExampleController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
```

This is a huge timesaver!


## Finding routes with Drush

Drush lets you figure out the controller associated with a route since version 10.5.  Here are some of the options:

```sh
$ drush route
$ drush route --path=/user/1
$ drush route --name=update.status
$ sh route --url=https://example.com/node/1
```
more at <https://www.drush.org/latest/commands/core_route/>

### All routes

Output from `drush route`. It lists the routes by name and the path they apply to.

```
'<button>': /
'<current>': /<current>
'<front>': /
'<nolink>': /
'<none>': /
admin_toolbar.run.cron: /run-cron
admin_toolbar.settings: /admin/config/user-interface/admin-toolbar
admin_toolbar_tools.cssjs: /admin/flush/cssjs
admin_toolbar_tools.flush: /admin/flush
admin_toolbar_tools.flush_menu: /admin/flush/menu
admin_toolbar_tools.flush_rendercache: /admin/flush/rendercache
admin_toolbar_tools.flush_static: /admin/flush/static-caches
admin_toolbar_tools.flush_twig: /admin/flush/twig
admin_toolbar_tools.flush_views: /admin/flush/views
admin_toolbar_tools.plugin: /admin/flush/plugin
admin_toolbar_tools.settings: /admin/config/user-interface/admin-toolbar-tools
admin_toolbar_tools.theme_rebuild: /admin/flush/theme_rebuild
batch_examples.batch: /batch-examples/batchform
...
```


### Specific path

Output when checking a specific path with `drush route --path /admin/flush`



```
name: admin_toolbar_tools.flush
path: /admin/flush
defaults:
  _controller: '\Drupal\admin_toolbar_tools\Controller\ToolbarController::flushAll'
  _title: 'Flush all caches'
requirements:
  _permission: 'administer site configuration'
  _csrf_token: 'TRUE'
options:
  _admin_route: true
  _access_checks:
    - access_check.permission
    - access_check.csrf
```

### Specific route name

```
$ drush route --name=admin_toolbar_tools.flush

name: admin_toolbar_tools.flush
path: /admin/flush
defaults:
  _controller: '\Drupal\admin_toolbar_tools\Controller\ToolbarController::flushAll'
  _title: 'Flush all caches'
requirements:
  _permission: 'administer site configuration'
  _csrf_token: 'TRUE'
options:
  _admin_route: true
  _access_checks:
    - access_check.permission
    - access_check.csrf
```


## Generate a controller with ChatGPT

At the time of this writing, [OpenAI](https://openai.com/) has a research release of its ChatGPT available.  You could always ask it to generate a controller for you as I did.  I was amazed by the result. I asked it to:

`Generate a Drupal 10 controller which displays a table of temperatures from 0 to 100 degrees in both celsius and fahrenheit
`

To which it replied:

```
Here is a Drupal 10 controller that generates a table of temperatures from 0 to 100 degrees in both Celsius and Fahrenheit:
```
```php
<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class TemperatureController extends ControllerBase {

  /**
   * Display a table of temperatures.
   */
  public function displayTable() {
    $rows = [];
    for ($i = 0; $i <= 100; $i++) {
      $rows[] = [
        'celsius' => $i,
        'fahrenheit' => $this->celsiusToFahrenheit($i),
      ];
    }
    $header = [
      ['data' => $this->t('Celsius'), 'field' => 'celsius'],
      ['data' => $this->t('Fahrenheit'), 'field' => 'fahrenheit'],
    ];
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No temperatures to display.'),
    ];
    return $build;
  }

  /**
   * Convert a temperature from Celsius to Fahrenheit.
   *
   * @param float $celsius
   *   The temperature in Celsius.
   *
   * @return float
   *   The temperature in Fahrenheit.
   */
  protected function celsiusToFahrenheit($celsius) {
    return ($celsius * 9 / 5) + 32;
  }

}

```

followed by this thoughtful addition:

```
To use this controller, you would need to define a route in your module's routing file that points to the displayTable method. For example:
```
```yml
mymodule.temperature_table:
  path: '/temperature-table'
  defaults:
    _controller: '\Drupal\mymodule\Controller\TemperatureController::displayTable'
    _title: 'Temperature Table'
  requirements:
    _permission: 'access content'
```

It concluded with:

```
This would define a route at the path /temperature-table that displays the table of temperatures when accessed.
```



## The Basics

### Route

A route connects a URL path to a controller. In `hello_world.routing.yml` ( e.g. in `modules/custom/hello_world/hello_world.routing.yml`) The path `/hello` maps to the controller `HelloWorldController` and the member function: `helloWorld()`. When a user visits `/hello`, Drupal checks to see that the user has `access content` permission and the `\Drupal\hello_world\Controller\HelloWorldController::helloWorld` function is executed.

```yaml
hello_world.hello:
   path: '/hello'
   defaults:
     _controller: '\Drupal\hello_world\Controller\HelloWorldController::helloWorld'
     _title: 'Our first route'
   requirements:
     _permission: 'access content'
```

### Controller
A controller is a PHP class that contains methods that generate a response to an `HTTP request`. In the example above, the controller is `HelloWorldController` and the method is `helloWorld()`. The controller is in a file called `HelloWorldController.php` in the `src/Controller` directory of the module.

Controllers usually return a [render array](render#overview), but can return an HTML page, an XML document, a [serialized JSON array](#return-json-data-from-a-route), an image, a redirect, a 404 error or almost anything else.

A simple render array looks like this:

```php
return [
  '#markup' => 'blah',
]
```
### Responses

HTTP is all about `requests` and `responses`. Drupal represents the `responses` it sends as `Response objects`. Drupal’s responses are [Symfony Response objects](https://symfony.com/doc/current/components/http_foundation.html#response). 

Symfony's Response objects are fully supported, but are insufficient to fully support the rich Drupal ecosystem: we need more structured metadata than the very simple Symfony Response objects can provide.

Unfortunately, Symfony Response objects do not have an interface so every specialized Response \"type\" needs to extend from Symfony's Response base class.

Drupal core defines two `response interfaces` that any response can implement to indicate it supports these particular Drupal capabilities:
1. `CacheableResponseInterface` - which can expose [cacheability metadata](https://www.drupal.org/docs/8/api/cache-api/cache-api#s-cacheability-metadata) such as cache contexts, tags and max-age. These can easily be implemented by using the corresponding [CacheableResponseTrait](https://git.drupalcode.org/project/drupal/-/blob/11.x/core/lib/Drupal/Core/Cache/CacheableResponseTrait.php?ref_type=heads).
1. `AttachmentsInterface` - which can expose #attached metadata. (Asset libraries, `<head>` elements, placeholders...)

Drupal’s additional response classes include some specialized Response subclasses that are available to developers:
1. `CacheableResponse` - A response that contains and can expose cacheability metadata. Supports Drupal's caching concepts: cache tags for invalidation and cache contexts for variations. This is simply `class CacheableResponse extends Response implements CacheableResponseInterface {}`.
1. `HtmlResponse` - This is what a controller returning a render array will result in after going through the Render API and its render pipeline. This is simply `class HtmlResponse extends Response implements CacheableResponseInterface, AttachmentsInterface {}`.
1. `CacheableJsonResponse` - A `JsonResponse` that contains and can expose cacheability metadata. This is simply `class CacheableJsonResponse extends JsonResponse implements CacheableResponseInterface {}` — i.e. it extends Symfony's [JsonResponse](https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response).
1. `CacheableRedirectResponse` - A `RedirectResponse` that contains and can expose cacheability metadata. This is simply `class CacheableRedirectResponse extends RedirectResponse implements CacheableResponseInterface {}` — i.e. it extends Symfony's [RedirectResponse](https://symfony.com/doc/current/controller.html#redirecting).
1. `LocalRedirectResponse` - A redirect response which cannot redirect to an external URL. (Extends `CacheableRedirectResponse`.)
1. TrustedRedirectResponse - A redirect response which should only redirect to a trusted (potentially external) URL. (Also extends `CacheableRedirectResponse`.)


See [Responses overview on drupal.org - updated May 2020](https://www.drupal.org/docs/drupal-apis/responses/responses-overview)

### Connecting to a twig template

Most often, you will have a twig template connected to your controller. You do this by a combination of a `#theme` element in the render array and a `hook_theme` function in a `.module` file.

In the example below, the controller returns a large render array and the theme is identified as `abc_teks_srp__correlation_voting`.

```php
return [
  '#theme' => 'abc_teks_srp__correlation_voting',
  '#content' => $content,
  '#breadcrumbs' => $breadcrumbs,
  '#management_links' => $management_links,
  '#correlation' => $correlation_info,
  '#citations' => $citations,
];
```
In a module file, there is a hook_theme function which corresponds to the `abc_teks_srp_theme` and identifies the template name as `abc-teks-srp-correlation-voting`. Here is the significant part of the `hook_theme()` function

```php
/**
 * Implements hook_theme().
 */
function abc_teks_srp_theme() {
  $variables = [
    'abc_teks_srp' => [
      'render element' => 'children',
    ],
    'abc_teks_srp__correlation_voting' => [
      'variables' => [
        'content' => NULL,
        'breadcrumbs' => NULL,
        'management_links' => NULL,
        'correlation' => NULL,
        'citations' => NULL,
      ],
      'template' => 'abc-teks-srp--correlation-voting',
    ],
```
The template will therefore be `abc-teks-srp--correlation.voting.yml`


## Simple page without arguments

This route is for a page with no arguments/parameters.

In the file page_example.routing.yml (e.g. `web/modules/contrib/examples/page_example/page_example.routing.yml` and the controller is at `web/modules/contrib/examples/page_example/src/Controller/PageExampleController.php`

```yml
# If the user accesses https://example.com/?q=examples/page-example/simple,
# or https://example.com/examples/page-example/simple,
# the routing system will look for a route with that path. 
# In this case it will find a match, and execute the _controller callback. 
# Access to this path requires "access simple page" permission.
page_example_simple:
  path: 'examples/page-example/simple'
  defaults:
    _controller: '\Drupal\page_example\Controller\PageExampleController::simple'
    _title: 'Simple - no arguments'
  requirements:
    _permission: 'access simple page'
```


## Page with arguments

From `web/modules/contrib/examples/page_example/page_example.routing.yml` `{first}` and `{second}` are the arguments.

```yml
# Since the parameters are passed to the function after the match, the
# function can do additional checking or make use of them before executing
# the callback function. The placeholder names "first" and "second" are
# arbitrary but must match the variable names in the callback method, e.g.
# "$first" and "$second".
page_example_arguments:
  path: 'examples/page-example/arguments/{first}/{second}'
  defaults:
    _controller: '\Drupal\page_example\Controller\PageExampleController::arguments'
  requirements:
    _permission: 'access arguments page'
```
## Display a form at a route

From `web/modules/custom/rsvp/rsvp.routing.yml`. This route will cause Drupal to load the form: `RSVPForm.php` so the user can fill it out.

```yml
rsvp.form:
  path: '/rsvplist'
  defaults:
    _form: 'Drupal\rsvp\Form\RSVPForm'
    _title: 'RSVP to this Event'
  requirements:
    _permission: 'view rsvplist'
```

## Admin form (or settings form)

From `web/modules/custom/rsvp/rsvp.routing.yml` this route loads the admin or settings form `RSVPConfigurationForm`.

```yml
rsvp.admin_settings:
  path: '/admin/config/content/rsvp'
  defaults:
    _form: 'Drupal\rsvp\Form\RSVPConfigurationForm'
    _title: 'RSVP Configuration Settings'
  requirements:
    _permission: 'administer rsvplist'
  options:
    _admin_route: TRUE
```


## Routing permissions

These are defined in your `module.permissions.yml` e.g. `rsvp.permissions.yml`. If you add this file to a module, a cache clear will cause the new permissions to appear on the permissions page.

This requires the user to be logged in to access this route:

```yml
requirements:
  _user_is_logged_in: 'TRUE'
```

To skip permissions, set `_access` to TRUE like this:

```yml
requirements:
  _access: 'TRUE'
```


### A specific permission

To specify a particular permission, use the following. Note. Case is critical!

```yml
requirements:
  _permission: 'administer rsvplist'
```


### Multiple permissions

Drupal allows stacking permissions with the plus(`+`) sign. Note the `+` sign means OR. e.g.

```yaml
  requirements:
    _permission: 'vote on own squishy item+manage squishy process'
```


## Resources

- [Introductory Drupal routes and controllers example from Drupal documentation updated October 2022](https://www.drupal.org/docs/drupal-apis/routing-system/introductory-drupal-routes-and-controllers-example)
- [Structure of Routes from Drupal documentation updated September 2022](https://www.drupal.org/docs/drupal-apis/routing-system/structure-of-routes)
- [Symfonycasts.com excellent course in Drupal 8](https://symfonycasts.com/screencast/drupal8-under-the-hood)
- [Controllers article from Drupalize.me updated December 2021](https://drupalize.me/topic/controllers)
- [Responses overview on drupal.org - updated May 2020](https://www.drupal.org/docs/drupal-apis/responses/responses-overview)
- [Symfony Response objects](https://symfony.com/doc/current/components/http_foundation.html#response)

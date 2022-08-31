- [Caching and cache tags](#caching-and-cache-tags)
  - [How to uncache a particular page or node](#how-to-uncache-a-particular-page-or-node)
  - [Don't cache data returned from a controller](#dont-cache-data-returned-from-a-controller)
  - [Disable caching for a content type](#disable-caching-for-a-content-type)
  - [Considering caching when retrieving query, get or post parameters](#considering-caching-when-retrieving-query-get-or-post-parameters)
  - [Debugging Cache tags](#debugging-cache-tags)
  - [Using cache tags](#using-cache-tags)
  - [Setting cache keys in a block](#setting-cache-keys-in-a-block)
  - [Getting Cache Tags and Contexts for a block](#getting-cache-tags-and-contexts-for-a-block)
  - [Caching REST Resources](#caching-rest-resources)
  - [Caching in an API class wrapper](#caching-in-an-api-class-wrapper)
  - [Caching in a .module file](#caching-in-a-module-file)
  - [Logic for caching render arrays](#logic-for-caching-render-arrays)
  - [Development Setup](#development-setup)
    - [Enable Twig Debugging](#enable-twig-debugging)
    - [Disable Cache for development](#disable-cache-for-development)
 - [Articles](#articles)


# Caching and cache tags



## How to uncache a particular page or node

This will cause Drupal to rebuild the page internally, but won't stop browsers or CDN's from caching.

```php
\Drupal::service('page_cache_kill_switch')->trigger();
```
Use this statement in node_preprocess, controller, etc.

Create a custom module to implement setting max-age to 0. For example in ddd.module file:


```php
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;

function ddd_node_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
  $bundle = $entity->bundle();
  if ($bundle == 'search_home') {
    $build['#cache']['max-age'] = 0;
    \Drupal::service('page_cache_kill_switch')->trigger();
  }
}
```


## Don't cache data returned from a controller

From dev1/web/modules/custom/rsvp/src/Controller/ReportController.php

```php
  // Don't cache this page.
  $content['#cache']['max-age'] = 0;
  return $content;
}
```

Disable caching for a route in the module.routing.yml file.

```
requirements:
  _permission: 'access content'
options:
  no_cache: TRUE
```

## Disable caching for a content type

If someone tries to view a node of content type *search_home* (i.e. an entity of bundle *search_home*) caching is disabled and Drupal and the browser will always re-render the page. This is necessary for a page that is retrieving data from a third party source and you almost always expect it to be different. It wouldn't work for a search page to show results from a previous search.

```php
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;

/**
 * Implements hook_ENTITY_TYPE_view_alter().
 */
function ddd_node_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
  $bundle = $entity->bundle();
  if ($bundle == 'search_home') {
    $build['#cache']['max-age'] = 0;
    \Drupal::service('page_cache_kill_switch')->trigger();
  }
}
```


## Considering caching when retrieving query, get or post parameters 

For get variables use:
```php
$query = \Drupal::request()->query->get('name');
```

For post variables use:
```php
$name = \Drupal::request()->request->get('name');
```
For all items in get:

```php
$query = \Drupal::request()->query->all();
$search_term = $query['query'];
$collection = $query['collection'];
```

Be wary about caching. From
<https://drupal.stackexchange.com/questions/231953/get-in-drupal-8/231954#231954>
the code provided only works the first time so it is important to add a '#cache' context in the markup.

```php
namespace Drupal\newday\Controller;

use Drupal\Core\Controller\ControllerBase;

class NewdayController extends ControllerBase {
    public function new() {
      $day= [
        "#markup" => \Drupal::request()->query->get('id'),
       ];
      return $day;
    }
}
```

The request is being cached, you need to tell the system to vary by the query argument:

```php
$day = [
    '#markup' => \Drupal::request()->query->get('id'),
    '#cache' => [
        'contexts' => ['url.query_args:id'],
    ],
];
```
More about caching render arrays at <https://www.drupal.org/docs/8/api/render-api/cacheability-of-render-arrays>


## Debugging Cache tags

In development.services.yml set these parameters 

```
parameters:

http.response.debug_cacheability_headers: true
```

in Chrome, the network tab, click on the doc and view the Headers. You will see the following two headers showing both the cache contexts and
the cache tags

1.  **X-Drupal-Cache-Contexts:**

> languages:language_interface route session theme timezone url.path
> url.query_args url.site user

2.  **X-Drupal-Cache-Tags:**

> block_view config:block.block.bartik_account_menu
> config:block.block.bartik_branding
> config:block.block.bartik_breadcrumbs
> config:block.block.bartik_content config:block.block.bartik_footer
> config:block.block.bartik_help config:block.block.bartik_local_actions
> config:block.block.bartik_local_tasks
> config:block.block.bartik_main_menu config:block.block.bartik_messages
> config:block.block.bartik_page_title config:block.block.bartik_powered
> config:block.block.bartik_search config:block.block.bartik_tools
> config:block.block.helloworldsalutation config:block.block.modalblock
> config:block.block.productimagegallery config:block.block.rsvpblock
> config:block.block.views_block\_\_aquifer_listing_block_1
> config:block.block.views_block\_\_related_videos_block_1
> config:block.block.views_block\_\_user_guide_pages_referencing_a\_product_block_1
> config:block.block.views_block\_\_workshop_count_proposed_workshop_block
> config:bloc

## Using cache tags

If you are generating a list of cached node teasers and you want to make sure your list is alway accurate, you can use cache tags. To refresh the list every time a node is added, deleted or edited you could use a render array like this:

```php
$build = array(
  '#type' => 'markup',
  '#markup' => $sMarkup,        
  '#cache' => [
    'keys' => ['home-all','home'],
    'tags'=> ['node_list'], // invalidate cache when any node content is added/changed etc.
    'max-age' => '36600', // invalidate cache after 10h
  ],
);
```

It is possible to change this so the cache is invalidated only when a content type of *book* or *magazine* is changed in two possible ways:

1. Include all node tags (node:{#id}), if doesn\'t matter if a new node of a particular type was added.

2. Create and control your own cache tag, and invalidate it when you want.

If you want a block to be rebuilt every time that a term from a particular vocab_id is added, changed, or deleted you can cache the term list.
If you need to cache a term list per vocab_id - i.e.  every time that a term from a particular vocab_id is added, changed, or deleted the cache tag is invalided using
`Cache::invalidateTags($tag_id)` then my block will be rebuild.


```php
use Drupal\Core\Cache\Cache;

function filters_invalidate_vocabulary_cache_tag($vocab_id) {
  Cache::invalidateTags(array('filters_vocabulary:' . $vocab_id));
}
```
If you want this to work for nodes, you may be able to  just change `$vocab_id` for `$node_type`.


## Setting cache keys in a block

If you add some code to a block that includes the logged in user's name,
you may find that the username will not be displayed correctly -- rather
it may show the prior users name. This is because the cache context of
user doesn't bubble up to the display of the container (e.g. the node
that is displayed along with your custom block.)  Add this to bubble the cache contexts up.

```php
public function getCacheContexts() {
  Return Cache::mergeContexts(parent::getCacheContexts(),['user']);
}
```

and scrolling down a bit at this link shows some more info about getting cache tags and merging them.
<https://drupal.stackexchange.com/questions/145823/how-do-i-get-the-current-node-id>


## Getting Cache Tags and Contexts for a block

In this file /modules/custom/dana_pagination/src/Plugin/Block/VideoPaginationBlock.php I have a block that renders a form. The form queries some data from the database and will need to be updated depending on the node that I am on.

I added the following two functions:

```php
public function getCacheTags() {
  //When my node changes my block will rebuild
  if ($node = \Drupal::routeMatch()->getParameter('node')) {
    //if there is node add its cachetag
    return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
  } else {
    //Return default tags instead.
    return parent::getCacheTags();
  }
}

public function getCacheContexts() {
  //if you depend on \Drupal::routeMatch()
  //you must set context of this block with 'route' context tag.
  //Every new route this block will rebuild
  return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
}

```


## Caching REST Resources

Interesting article about caching REST resources at
<http://blog.dcycle.com/blog/2018-01-24/caching-drupal-8-rest-resource/>

We can get Drupal to cache our rest resource e.g. in dev1
/custom/iai_wea/src/Plugin/rest/resource/WEAResource.php where we add
this to our response:

```php
if (!empty($record)) {
  $response = new ResourceResponse($record, 200);
  $response->addCacheableDependency($record);
  return $response;
}
```


## Caching in an API class wrapper

From
docroot/modules/custom/cm_api/src/CmAPIClient.php

Here a member is set up in the class

```php
/**
 * Custom service to call APIs.
 *
 * @see \Drupal|cm_api|CmAPIClientInterface
 */
class CmAPIClient implements CmAPIClientInterface {
...
/**
 * Internal static cache.
 *
 * @var array
 */
protected static $cache = [];
```

The api call is made and the cache is checked. The index (or key) is
build from the api call "getPolicy" and the next key is the policy
number with the version number attached. So the `$response_data` is put
in the cache with:

```php
self::$cache['getPolicy'][$policy_number . $version] = $response_data;
```
and retrieved with:

```php
$response_data = self::$cache['getPolicy'][$policy_number . $version];
```
This relieves the back end load by rather getting the data from the
cache if is in the cache. (If the cache is warm.)

The entire function is shown below. It is from
docroot/modules/custom/cm_api/src/CmAPIClient.php:

```php
public function getPolicy($policy_number, $version = 'v2') {
  // Api action type.
  $this->params['api_action'] = 'Get policy';
  // Add policy number to display in watchdog.
  $this->params['policynumber'] = $policy_number;
  $base_api_url = $this->getBaseApiUrl();
  if (empty($policy_number) || !is_numeric($policy_number)) {
    $this->logger->get('cm_api_get_policy')
      ->error('Policy number must be a number.');
    return FALSE;
  }
  $endpoint_url = $base_api_url . '/' . $version . '/Policies/group?policies=' . $policy_number . '&include_ref=false&include_hist=true';

  if (isset(self::$cache['getPolicy'][$policy_number . $version])) {
    $response_data = self::$cache['getPolicy'][$policy_number . $version];
  } else {
    $response_data = $this->performRequest($endpoint_url, 'GET', $this->params);
    self::$cache['getPolicy'][$policy_number . $version] = $response_data;
  }
  return $response_data;
}
```

## Caching in a .module file

From
docroot/modules/custom/ncs_infoconnect/nzz_zzzzconnect.module

In the hook_preprocess_node function, we are calling an api to get some
data

```php
/**
 * Implements hook_preprocess_node().
 */
function nzz_zzzzconnect_preprocess_node(&$variables) {
```
Notice the references to '\Drupal::cache()`. First we check if this is
our kind of node to process. Then we derive the `$cid`. We check the
cache with a call to `->get($cid)` and if it fails we:

1.  call the api with `$client->request('GET')`

2.  pull out the body with `$response->getBody()`

3.  set the whole body into the cache with:
```php
\Drupal::cache()->set($cid, $contents, REQUEST_TIME + (300));
```
In future requests, we can just use the data from the cache. 

```php
if ($node_type == 'zzzzfeed' && $published) {

  $uuid = $variables['node']->field_uuid->getValue();
  $nid = $variables['node']->id();

  $nzz_auth_settings = Settings::get('nzz_api_auth', []);
  $uri = $ncs_auth_settings['default']['server'] . ':' . $ncs_auth_settings['default']['port'];
  $uri .= '/blahcontent/search';
  $client = \Drupal::httpClient();

  $cid = 'zzzzfeed-' . $nid;
  try {
    if ($cache = \Drupal::cache()->get($cid)) {
      $contents = $cache->data;
    }
    else {
      $response = $client->request('GET', $uri, [
        'auth' => [$nzz_auth_settings['default']['username'], $nzz_auth_settings['default']['password']],
        'query' => [
          'uuid' => $uuid[0]['value'],
        ],
        'timeout' => 1,
      ]);
      $contents = $response->getBody()->getContents();
      \Drupal::cache()->set($cid, $contents, REQUEST_TIME + (300));
    }
  }
  catch (RequestException $e) {
    watchdog_exception('nzz_zzzzconnect', $e);
    return FALSE;
  }
  catch (ClientException $e) {
    watchdog_exception('nzz_zzzzconnect', $e);
    return FALSE;
  }

  $contents = json_decode($contents, TRUE);
  $body = $contents['hits']['hits'][0]['versions'][0]['properties']['Text'][0];
  $variables['content']['body'] = [
    '#markup' => $body,
  ];

}
```
## Logic for caching render arrays

From
<https://www.drupal.org/docs/8/api/render-api/cacheability-of-render-arrays>

Please try to adopt the following thought process.

Whenever you are generating a render array, use the following 5 steps:

1.  I\'m rendering something. That means I must think of cacheability.

2.  Is this something that\'s expensive to render, and therefore is
    worth caching? If the answer is yes, then what identifies this
    particular representation of the thing I\'m rendering? Those are the
    cache keys.

3.  Does the representation of the thing I\'m rendering vary per
    combination of permissions, per URL, per interface language, per ...
    something? Those are the cache contexts. Note: cache contexts are
    completely analogous to HTTP\'s Vary header.

4.  What causes the representation of the thing I\'m rendering become
    outdated? I.e., which things does it depend upon, so that when those
    things change, so should my representation? Those are the cache
    tags.

5.  When does the representation of the thing I\'m rendering become
    outdated? I.e., is the data valid for a limited period of time only?
    That is the max-age (maximum age). It defaults to \"permanently
    (forever) cacheable\" (Cache::PERMANENT). When the representation is
    only valid for a limited time, set a max-age, expressed in seconds.
    Zero means that it\'s not cacheable at all.

Cache contexts, tags and max-age must always be set, because they affect
the cacheability of the entire response. Therefore they \"bubble\":
parents automatically receive them.

Cache keys must only be set if the render array should be cached.

There are more details at the link above


## Development Setup

### Enable Twig Debugging

Generally I enable twig debugging and disable caching while developing a site.  Here are the steps

Enable twig debugging output in source
In sites/default/development.services.yml set twig.config debug:true.  See core.services.yml for lots of other items to change for development

```
# Local development services.
#
# To activate this feature, follow the instructions at the top of the
# 'example.settings.local.php' file, which sits next to this file.
parameters:
  http.response.debug_cacheability_headers: true
  dino.roar.use_key_value_cache: true
  twig.config:
    debug: true
    auto_reload: true
    cache: false

# To disable caching, you need this and a few other items
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

to enable put this in settings.local.php:

```php
/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';

You also need to disable the render cache in settings.local.php with: 

$settings['cache']['bins']['render'] = 'cache.backend.null';
```

### Disable Cache for development


From https://www.drupal.org/node/2598914

1. Copy, rename, and move the sites/example.settings.local.php to sites/default/settings.local.php:
$ cp sites/example.settings.local.php sites/default/settings.local.php
2. Open settings.php file in sites/default and uncomment these lines:
```php
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
```
This will include the local settings file as part of Drupal's settings file.

3. Open settings.local.php and make sure development.services.yml is enabled.
```php
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
```
By default development.services.yml contains the settings to disable Drupal caching:
```
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```
NOTE: Do not create development.services.yml, it already exists under /sites.  You can copy it from there.

4. In settings.local.php change the following to be TRUE if you want to work with enabled css- and js-aggregation:
```php
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
```
5. Uncomment these lines in settings.local.php to disable the render cache and disable dynamic page cache:

```php
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
```
If you are using Drupal version greater than or equal to 8.4 then add the following lines to your settings.local.php
```php
$settings['cache']['bins']['page'] = 'cache.backend.null';
```
If you do not want to install test modules and themes, set the following to FALSE:
```php
$settings['extension_discovery_scan_tests'] = FALSE;
```

6. Open development.services.yml in the sites folder and add the following block to disable the twig cache:
```php
parameters:
  twig.config:
    debug: true
    auto_reload: true
    cache: false
```

NOTE: If the parameters block is already present in the yml file, append the twig.config block to it.

1. Afterwards rebuild the Drupal cache with `drush cr` otherwise your website will encounter an unexpected error on page reload.


## Articles

* [Drupal: cache tags for all, regardles of your backend From Matt Glaman 22, August 2022](https://mglaman.dev/blog/drupal-cache-tags-all-regardless-your-backend)
* [Cache contexts overview on drupal.org](https://www.drupal.org/docs/drupal-apis/cache-api/cache-contexts)
* [Caching in Drupal 8 a quick overview of Cache tags, cache context and cache max-age with simple examples](https://zu.com/articles/caching-drupal-8)

[home](../index.html)



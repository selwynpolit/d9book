---
title: Caching
---

# Caching, cache tags and performance optimization
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=caching.md)


### Set the max-age in a render array

This will show up in the headers for anonymous users as `x-drupal-cache-max-age: 3600`.

```php
$build = [
  '#markup' => 'Your content here',
  '#cache' => [
    'max-age' => 3600,
  ],
];

// Or.
$build['#cache']['max-age'] = 3600;
```

## Set max-age when returning JSON data from a route

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

Note. This will return JSON data and in the headers, you will get `Cache-Control: max-age=3607, public`



## How to uncache a page or node

This will cause Drupal to rebuild the page internally, but won\'t stop browsers or CDN\'s from caching. You can use this in a preprocess hook or a controller.

```php
\Drupal::service('page_cache_kill_switch')->trigger();
```


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


## Disable caching on a route

This will cause Drupal to rebuild the page internally on each page load but won't stop browsers or CDN's from caching. The line: `no_cache: TRUE` does the work. This is implemented in the `module.routing.yml` file.

```yaml
abc_time_publisher.program_detail:
  path: '/abc/admin/publisher/{node1}/program/{node2}'
  defaults:
    _controller: '\Drupal\abc_time_publisher\Controller\ProgramDetailController::content'
    _title: 'Program Detail'
  requirements:
    _permission: 'view any abc publisher+view own abc publisher'
  options:
    parameters:
      node1:
        type: entity:node
      node2:
        type: entity:node
    no_cache: 'TRUE'
```


## Prevent browser from caching a page using max-age

```php
  $content['#cache']['max-age'] = 0;
  return $content;
}
```


## Disable caching for a content type

If someone tries to view a node of content type `search_home` caching is disabled and Drupal and the browser will always re-render the page. This is necessary when retrieving data from a third party source that you is frequently changed. It wouldn't work for a search page to show results from a previous search.

```php
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;

/**
 * Implements hook_ENTITY_TYPE_view_alter().
 */
function ddd_node_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
  if ($entity->bundle() == 'search_home') {
    $build['#cache']['max-age'] = 0;
    \Drupal::service('page_cache_kill_switch')->trigger();
  }
}
```

## Set cache context correctly when retrieving query, get or post parameters 

Drupal will cache requests so be sure to correctly specify cache context when building your render arrays if they need those parameters.

For more on retrieving those, see [Retrieving query, get or post parameters](general#retrieve-query-get-or-post-parameters)


See [Stack Exchange article - March 2017](https://drupal.stackexchange.com/questions/231953/get-in-drupal-8/231954#231954). 

When trying to build the `$day` render array, this code needs the cache context `url.query_args:id` (to tell Drupal to vary by the query argument) to correctly return the appropriate values each time:

```php
$day = [
    '#markup' => \Drupal::request()->query->get('id'),
    '#cache' => [
        'contexts' => ['url.query_args:id'],
    ],
];
```

Read [more about Cacheability of render arrays on drupal.org - updated April 2023](https://www.drupal.org/docs/drupal-apis/render-api/cacheability-of-render-arrays)




## Using cache tags

To generate a list of cached node teasers that is always accurate, use cache tags. Here is a render array that will be updated time a node is added, deleted or edited:

```php
$build = [
  '#type' => 'markup',
  '#markup' => $sMarkup,        
  '#cache' => [
    'keys' => ['home-all','home'],
    'tags'=> ['node_list'], // invalidate cache when any nodes are added/changed etc.
    'max-age' => '36600', // invalidate cache after 10h
  ],
];
```

You can cause the cache to be invalidated only when a content type of `book` or `magazine` is changed in two ways:

1. Include all node tags `(node:{#id})`, it doesn\'t matter if a new node of a particular type was added.

2. Create and control your own cache tag, and invalidate it when you want.

If you want a block to be rebuilt every time that a term from a particular vocab_id is added, changed, or deleted you can cache the term list.
If you need to cache a term list per vocab_id - i.e.  every time that a term from a particular vocab_id is added, changed, or deleted the cache tag is invalided using `Cache::invalidateTags($tag_id)`. When Drupal goes to render this array, it will not use the cached version.

```php
use Drupal\Core\Cache\Cache;

function filters_invalidate_vocabulary_cache_tag($vocab_id) {
  Cache::invalidateTags(['filters_vocabulary:' . $vocab_id]);
}
```

If you want this to work for nodes, you may be able to  just change `$vocab_id` for `$node_type`.

## Debugging Cache tags

In `development.services.yml` set the `http.response debug_cacheability_headers` parameter:

```yml
parameters:
  http.response.debug_cacheability_headers: true
```

Open the inspect pane in the browser, on the network tab, click on the doc (the first item at the top of the list) and scroll down to the response headers. You will see the following headers: `X-Drupal-Cache-Tags`, `X-Drupal-Cache-Contexts` and `X-Drupal-Cache-Max-Age` which show the cache tags, contexts and max age.

e.g at URL: `https://tea.ddev.site/teks/admin/srp/v2/program/590536/team/vote_number/0`

![Debugging cache tags](/images/debug_cache_tags.png)

Read more [on debugging cache tags - CacheableResponseInterface](https://www.drupal.org/docs/8/api/responses/cacheableresponseinterface#debugging) and [debugging cache tags in the cache API](https://www.drupal.org/docs/drupal-apis/cache-api/cache-tags#s-debugging)

### Invalidate the cache tag for a specific node

In this function, we build a `$cache_tag` like `node: 123` and call `Cache:invalidateTags()` so Drupal will force a reload from the database for anything that depends on that node.


```php
use Drupal\Core\Cache\Cache;


public function vote(array $options): void {
  $this->load();
  $voter_uid = $options['voter_uid'];
  $vote_number = $options['vote_number'];
  /** @var VotingRound $voting_round */
  $voting_round = $this->votingRound[$vote_number];
  $voting_round->vote($voter_uid, $options);
  $cache_tag = 'node:' . $this->programNid;
  Cache::invalidateTags([$cache_tag]);
}
```

::: tip Note
According to <https://www.drupal.org/docs/drupal-apis/cache-api/cache-tags> Although many entity types follow a predictable cache tag format of `<entity type ID>:<entity ID>`, third-party code shouldn't rely on this. Instead, it should retrieve cache tags to invalidate for a single entity using its`::getCacheTags()` method, e.g., `$node->getCacheTags()`, `$user->getCacheTags()`, `$view->getCacheTags()` etc.
:::

## Setting cache keys in a block

If you add some code to a block that includes the logged in user's name, you may find that the username will not be displayed correctly -- rather it may show the prior users name. This is because the cache context of user doesn't bubble up to the display of the container (e.g. the node that is displayed along with your custom block.)  Add this to bubble the cache contexts up.

```php
public function getCacheContexts() {
  Return Cache::mergeContexts(parent::getCacheContexts(),['user']);
}
```

Read [more about getting cache tags and merging them on Stack Exchange](https://drupal.stackexchange.com/questions/145823/how-do-i-get-the-current-node-id)


## Getting Cache Tags and Contexts for a block

In this file `/modules/custom/dana_pagination/src/Plugin/Block/VideoPaginationBlock.php` there is a block that renders a form. The form queries some data from the database and will need to be updated depending on the node being viewed.

These two functions do the work to get both the cache contexts (based on the route) and get the cache tags based on the current node.  If the node has been viewed previously, this block will be cached.  If the :

```php
public function getCacheTags() {
  // When my node is changed my block will rebuild.
  if ($node = \Drupal::routeMatch()->getParameter('node')) {
    // Add the cache tag if a node is specified in the url.
    return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
  } 
  else {
    //Return default tags instead.
    return parent::getCacheTags();
  }
}

public function getCacheContexts() {
  // If you depend on \Drupal::routeMatch(),
  // you must set context of this block with 'route' context tag.
  // Every new route this block will rebuild.
  return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
}
```

## Caching REST Resources

Drupal can cache our rest resource e.g. in dev1
`/custom/iai_wea/src/Plugin/rest/resource/WEAResource.php` where we add this to our response:

```php
if (!empty($record)) {
  $response = new ResourceResponse($record, 200);
  $response->addCacheableDependency($record);
  return $response;
}
```
Read more in this [interesting article about caching REST resources](http://blog.dcycle.com/blog/2018-01-24/caching-drupal-8-rest-resource/)

## Caching in an API class wrapper

From `docroot/modules/custom/cm_api/src/CmAPIClient.php`

Here a member is set up in the class:

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

The api call is made and the cache is checked. The index (or key) is built from the api call `getPolicy` and the next key is the policy number with the version number attached. So the `$response_data` is put in the cache with:

```php
self::$cache['getPolicy'][$policy_number . $version] = $response_data;
```

and retrieved with:

```php
$response_data = self::$cache['getPolicy'][$policy_number . $version];
```

This relieves the back end load by rather getting the data from the cache if is in the cache (warm cache).

The entire function is shown below. It is from `docroot/modules/custom/cm_api/src/CmAPIClient.php`:

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

From `docroot/modules/custom/ncs_infoconnect/nzz_zzzzconnect.module`.

In the hook_preprocess_node function, we are calling an api to get some data.

```php
/**
 * Implements hook_preprocess_node().
 */
function nzz_zzzzconnect_preprocess_node(&$variables) {
```

Notice the references to `\Drupal::cache()`. First we check if this is our kind of node to process. Then we derive the `$cid` (cache id). We check the cache with a call to `->get($cid)` and if it fails we:

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
      // Request the data from the slow API.
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


## Caching data for better performance

For this use case, I needed to store data gathered from multiple nodes in a cache so I can access it really quickly.  I load up an array of data in `$this->expectations` and store it in the cache.  This stores the array into a row of the `cache_default` table identified by a cache id.

::: tip
To clear this data from cache, use `drush cr` or use the Drupal u/i (Configuration, Development, Performance and click clear all caches button). This will permanently erase each row of cached data from the `cache_default` table and the other `cache_*` tables.
:::

```php
// Write data to the cache.
$cache_id = "expectations.program.$this->programNid.vote.$this->voteNumber.publisher.$this->publisherNid";
\Drupal::cache()->set($cache_id, $this->expectations, Cache::PERMANENT);
// Read from the cache.
$cache_data = \Drupal::cache()->get($cache_id);
```
Here are some rows in the `cache_default` table:
![Cache row of data in cache_default table](/images/cached_data.png)

Here is what the `$this->expectations` array looks like from the `data` field:
![Contents of data field](/images/cache_longblob.png)

Here is a complete function which loads data from the cache.  If the cache is empty, the data is rebuilt from nodes and then stored in the cache:

```php
  protected function loadExpectations() {
    $cache_id = "expectations.program.$this->programNid.vote.$this->voteNumber.publisher.$this->publisherNid";
    $cache_data = \Drupal::cache()->get($cache_id);
    if (!$cache_data) {
      // Retrieve expectation info for this team.
      $expectation_nodes = Node::loadMultiple($this->expectationNids);
      foreach ($expectation_nodes as $expectation_node) {
        $expectation_nid = $expectation_node->id();
        $this->expectations[$expectation_nid] = [
          'nid' => $expectation_nid,
          'pub_status' => $expectation_node->get('field_tks_expectation_pub_status')->value ?? 'error',
          'kss_nid' => $expectation_node->get('field_tks_kss_parent_nid')->target_id,
          'cfitem_nid' => $expectation_node->get('field_tks_expectation')->target_id,
        ];
      }
      \Drupal::cache()->set($cache_id, $this->expectations, Cache::PERMANENT);
      return;
    }
    $this->expectations = $cache_data->data;
  }
```

Read more about the [Cache API](https://api.drupal.org/api/drupal/core!core.api.php/group/cache)


## Invalidating caches

When cache data becomes stale, quickly invalidate the cache bins by building an array of cache names (or cache_ids) and call `invalidateMultiple()`:

```php
  public function invalidateAllCaches() {
    $citation_cache_id = "citations.program.$this->programNid.vote.$this->voteNumber.publisher.$this->publisherNid";
    $correlation_cache_id = "correlations.program.$this->programNid.vote.$this->voteNumber.publisher.$this->publisherNid";
    $expectation_cache_id = "expectations.program.$this->programNid.vote.$this->voteNumber.publisher.$this->publisherNid";
    $cache_ids = [$citation_cache_id, $correlation_cache_id, $expectation_cache_id];
    \Drupal::cache()->invalidateMultiple($cache_ids);
  }
```

## Make a response dependent on any taxonomy term changes with cache tags

For this route Drupal will cache the page unless there is a change to a taxonomy term, then the content will be rebuilt.

```yaml
general.cache_example1:
  path: '/general/cache-example1/nid/{nid}'
  defaults:
    _controller: '\Drupal\general\Controller\CachePlay1::cacheExample1'
    _title: 'Cache Play1'
    _format: 'json'
  requirements:
    _permission: 'access content'
  options:
    parameters:
      nid:
        type: 'integer'
```

```php
  public function cacheExample1(int $nid){

    if ($nid == 0) {
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

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t("Node ID: $nid, Name: $data[name], Age: $data[age], Occupation: $data[occupation]"),
    ];

    // Make this dependent on any changes to taxonomy terms.
    $build['#cache']['tags'][] = 'taxonomy_term_list';
    // Or make this dependent on any changes to nodes.
    // $build['#cache']['tags'][] = 'node_list';
    return $build;
  }
```

Here is a screen shot where both `node_list` and `taxonomy_term_list` are specified for the current page.

![Cache tags in Chrome](/images/cache-tags1.png)


Sometimes you may want to build a new `ResourceResponse` to control the response.  This may be appropriate if building an API. See below:


```php
$build = [
  '#cache' => [
    'tags' => ['taxonomy_term_list']
    ]
  ];
return (new ResourceResponse($data, 200))->addCacheableDependency(CacheableMetadata::createFromRenderArray($build));
```


```php
/**
 * Implements hook_preprocess_node()
 *
 */
function mymodule_preprocess_node(&$variables) {
  if ($variables['node']->getType() === 'my_content_type') {
    $variables['#cache']['tags'][] = 'taxonomy_term_list';
  }
}
```
[More at Stack Exchange](https://drupal.stackexchange.com/questions/267346/invalidate-a-specific-cache-tag-on-any-saved-taxonomy-term)


## Specify custom cache bins

To specify your own cache bin e.g. `voting`, pass the name of your cache bin to the `\Drupal::cache()` calls like this:

```php
$cache_id = "expectations.program.$this->programNid.vote.$this->voteNumber.publisher.$this->publisherNid";
$cache_data = \Drupal::cache('voting')->get($cache_id);
// And.
\Drupal::cache('voting')->set($cache_id, $this->expectations, Cache::PERMANENT);
```

This does require an entry in your `module.services.yml` file.  e.g. like in `docroot/modules/custom/abc/modules/def/abc_voting.services.yml`:

```yml
services:
  cache.voting:
    class: Drupal\Core\Cache\CacheBackendInterface
    tags:
      - { name: cache.bin }
    factory: cache_factory:get
    arguments: [voting]
```

Your data will be stored in a table called `cache_voting`.

::: tip Note
To clear this data from cache, use `drush cr` or use the Drupal u/i (Configuration, Development, Performance and click clear all caches button). This will permanently erase each line of cached data from the various `cache_*` tables. If you want your cache to survive a `drush cr` use the [Permanent Cache Bin module.](https://www.drupal.org/project/pcb)
:::

Read more about the [Drupal Cache API](https://api.drupal.org/api/drupal/core!core.api.php/group/cache):




## Caching data so it doesn't get cleared by a cache rebuild

Using the [Permanent Cache Bin module](https://www.drupal.org/project/pcb) you can put your cached data into a cache bin and have it survive cache rebuilding.  This can be really useful if you need some cached data to stay around while you are clearing Drupal's caches.

This requires an entry in your `module.services.yml` file.  e.g. in docroot/modules/custom/tea_teks/modules/tea_teks_voting/tea_teks_voting.services.yml.  Notice the `default_backend:` value which makes it permanent:


```yml
services:
  cache.voting:
    class: Drupal\Core\Cache\CacheBackendInterface
    tags:
      - { name: cache.bin, default_backend: cache.backend.permanent_database }
    factory: cache_factory:get
    arguments: [voting]
```

::: tip Note
Executing invalidate in code does **not** clear any caches that are using `cache.backend.permanent_database`. 
:::

** Some items to be aware of **

1. To clear permanent cache, use `drush pcbf [bin]` e.g. `drush pcbf voting`.

2. To clear permanent cache programmatically use `\Drupal::service('cache.voting')->deleteAllPermanent();`

3. To clear permanent cache through the admin UI. For each cache bin using pcb, use the clear cache button in Admin -> Development -> Performance page (`admin/config/development/performance`).


## Development Setup

### Disable caching and enable TWIG debugging

Generally I enable twig debugging and disable caching while developing a site.  This means I don't have to do a `drush cr` each time I make a change to a template file.

To enable TWIG debugging output in source, in `sites/default/development.services.yml` set `twig.config debug:true`.  See `core.services.yml` for lots of other items to change for development.

You can also navigate to `/admin/config/development/settings` where you can click checkboxes in the Drupal admin u/i.


TWIG debugging output looks like this:

![TWIG debugging output](/images/twig_debug_output.png)

```yml
# Local development services.
#
# To activate this feature, follow the instructions at the top of the
# 'example.settings.local.php' file, which sits next to this file.
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    debug: true
    auto_reload: true
    cache: false

# To disable caching, you need this and a few other items
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

Here is the entire `development.services.yml` file that I usually use:

```yml
# Local development services.
#
# put this in /sites/development.services.yml
#
# To activate this feature, follow the instructions at the top of the
# 'example.settings.local.php' file, which sits next to this file.
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    # Twig debugging:
    #
    # When debugging is enabled:
    # - The markup of each Twig template is surrounded by HTML comments that
    #   contain theming information, such as template file name suggestions.
    # - Note that this debugging markup will cause automated tests that directly
    #   check rendered HTML to fail. When running automated tests, 'debug'
    #   should be set to FALSE.
    # - The dump() function can be used in Twig templates to output information
    #   about template variables.
    # - Twig templates are automatically recompiled whenever the source code
    #   changes (see auto_reload below).
    #
    # For more information about debugging Twig templates, see
    # https://www.drupal.org/node/1906392.
    #
    # Not recommended in production environments
    # @default false
    debug: true
    # Twig auto-reload:
    #
    # Automatically recompile Twig templates whenever the source code changes.
    # If you don't provide a value for auto_reload, it will be determined
    # based on the value of debug.
    #
    # Not recommended in production environments
    # @default null
    #    auto_reload: null
    auto_reload: true
    # Twig cache:
    #
    # By default, Twig templates will be compiled and stored in the filesystem
    # to increase performance. Disabling the Twig cache will recompile the
    # templates from source each time they are used. In most cases the
    # auto_reload setting above should be enabled rather than disabling the
    # Twig cache.
    #
    # Not recommended in production environments
    # @default true
    cache: false
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

You need to enable your `development.services.yml` file so add this to your `settings.local.php`:

```php
/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
```

You also need to disable caches and JS/CSS preprocessing in `settings.local.php` with: 

```php
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
```

### Disable Caching for development (more detailed steps)

From https://www.drupal.org/node/2598914

1. Copy, rename, and move the `sites/example.settings.local.php` to `sites/default/settings.local.php` with:

```bash
$ cp sites/example.settings.local.php sites/default/settings.local.php
```

2. Edit `sites/default/settings.php` and uncomment these lines:

```php
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
```

This will include the local settings file as part of Drupal's settings file.

1. In `settings.local.php` make sure `development.services.yml` is enabled with:

```php
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
```

By default `development.services.yml` contains the settings to disable Drupal caching:

```yml
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

::: tip Note
Don't create `development.services.yml`, it already exists under `/sites` so you can copy it from there.
:::

4. In `settings.local.php` change the following to be `TRUE` if you want to work with enabled css- and js-aggregation:

```php
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
```

5. Uncomment these lines in `settings.local.php` to disable the render cache and disable dynamic page cache:

```php
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
```

6. In `sites/development.services.yml` add the following block to disable the twig cache:

```yaml
parameters:
  twig.config:
    debug: true
    auto_reload: true
    cache: false
```

::: tip Note 
If the `parameters` block is already present in `sites/development.services.yml`, append the `twig.config` block to it.
:::

Rebuild the Drupal cache with `drush cr` otherwise your website will encounter an unexpected error on page reload.

Here is the entire `development.services.yml` file that I usually use:

```yml
# Local development services.
#
# put this in /sites/development.services.yml
#
# To activate this feature, follow the instructions at the top of the
# 'example.settings.local.php' file, which sits next to this file.
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    # Twig debugging:
    #
    # When debugging is enabled:
    # - The markup of each Twig template is surrounded by HTML comments that
    #   contain theming information, such as template file name suggestions.
    # - Note that this debugging markup will cause automated tests that directly
    #   check rendered HTML to fail. When running automated tests, 'debug'
    #   should be set to FALSE.
    # - The dump() function can be used in Twig templates to output information
    #   about template variables.
    # - Twig templates are automatically recompiled whenever the source code
    #   changes (see auto_reload below).
    #
    # For more information about debugging Twig templates, see
    # https://www.drupal.org/node/1906392.
    #
    # Not recommended in production environments
    # @default false
    debug: true
    # Twig auto-reload:
    #
    # Automatically recompile Twig templates whenever the source code changes.
    # If you don't provide a value for auto_reload, it will be determined
    # based on the value of debug.
    #
    # Not recommended in production environments
    # @default null
    #    auto_reload: null
    auto_reload: true
    # Twig cache:
    #
    # By default, Twig templates will be compiled and stored in the filesystem
    # to increase performance. Disabling the Twig cache will recompile the
    # templates from source each time they are used. In most cases the
    # auto_reload setting above should be enabled rather than disabling the
    # Twig cache.
    #
    # Not recommended in production environments
    # @default true
    cache: false
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

## How to specify the cache backend for Memcache, Redir or APCu

This is relevant for using [Memcache](https://www.drupal.org/project/memcache), [Redis](https://www.drupal.org/project/redis) and also [APCu](https://www.php.net/manual/en/book.apcu.php).  By default, Drupal caches information in the database.  Tables includes `cache_default`, `cache_render`, `cache_page`, `cache_config` etc.  By using the configuration below, Drupal can instead store this info in memory to increase performance.

Drupal will no longer automatically use the custom global cache backend specified in `$settings['cache']['default']` in `settings.php` on certain specific cache bins that define their own `default_backend` in their service definition. In order to override the default backend, a line must be added explicitly to `settings.php` for each specific bin that provides a `default_backend`. This change has no effect for users that do not use a custom cache backend configuration like Redis or Memcache, and makes it possible to remove workarounds that were previously necessary to keep using the default fast chained backend for some cache bins defined in Drupal core.

**Detailed description with examples**

In modern Drupal there are several ways to specify which cache backend is used for a certain cache bin (e.g. the `discovery` cache bin or the `render` cache bin).

In Drupal, cache bins are defined as services and are tagged with name: `cache.bin`. Additionally, some cache bins specify a `default_backend` service within the tags. For example, the discovery cache bin from Drupal core defines a fast chained default backend:

```yml
  cache.discovery:
    class: Drupal\Core\Cache\CacheBackendInterface
    tags:
      - { name: cache.bin, default_backend: cache.backend.chainedfast }
    factory: cache_factory:get
    arguments: [discovery]
```
Independent of this, the `$settings` array can be used in `settings.php` to assign cache backends to cache bins. For example:

```php
$settings['cache']['bins']['discovery'] = 'cache.backend.memory';
$settings['cache']['default'] = 'cache.backend.redis';
```

Before Drupal 8.2.0, the order of steps through which the backend was selected for a given cache bin was as follows:

* First look for a specific bin definition in settings. E.g., `$settings['cache']['bins']['discovery']`
* If not found, then use the global default defined in settings. I.e., `$settings['cache']['default']`
* If a global default is not defined in settings, then use the `default_backend` from the tag in the service definition.

This was changed to:

* First look for a specific bin definition in settings. E.g., `$settings['cache']['bins']['discovery']`
* If not found, then use the `default_backend` from the tag in the service definition.
* If no `default_backend` for the specific bin was provided, then use the global default defined in settings. I.e., `$settings['cache']['default']`

The old order resulted in unexpected behaviors, for example, the fast chained backend was no longer used when an alternative cache backend was set as default.

The order has been changed, so that the cache bin services that explicitly set `default_backends` are always used unless explicitly overridden with a per-bin configuration. In core, this means, fast chained backend will be used for `bootstrap`, `config`, and `discovery` cache bins and `memory` backend will be used for the `static` cache bin, unless they are explicitly overridden in settings.

For example, to ensure Redis is used for all cache bins, before 8.2.0, the following configuration would have been enough:

```php
$settings['cache']['default'] = 'cache.backend.redis';
```
However, now the following configuration in settings.php would be required to achieve the same exact behavior:

```php
$settings['cache']['bins']['bootstrap'] = 'cache.backend.redis';
$settings['cache']['bins']['discovery'] = 'cache.backend.redis';
$settings['cache']['bins']['config'] = 'cache.backend.redis';
$settings['cache']['bins']['static'] = 'cache.backend.redis';
$settings['cache']['default'] = 'cache.backend.redis';
```

Before proceeding to override the cache bins that define fast cached default backends blindly, please also read why they exist, particularly when using multiple webserver nodes. See [ChainedFastBackend on api.drupal.org](http://api.drupal.org/ChainedFastBackend).

Read [more on New cache backend configuration order, per-bin default before default configuration on drupal.org](https://www.drupal.org/node/2754947)

Fabian Franz in [his article](https://drupalsun.com/fabianx/2015/12/01/day-1-tweak-drupal-8-performance-use-apcu-24-days-performance-goodies) suggests that we can configure `APCu` to be used for caches with the following:

```php
 $settings['cache']['default'] = 'cache.backend.apcu';
 $settings['cache']['bins']['bootstrap'] = 'cache.backend.apcu';
 $settings['cache']['bins']['config'] = 'cache.backend.apcu';
 $settings['cache']['bins']['discovery'] = 'cache.backend.apcu';
 ```

::: warning
Proceed with caution with the above as it seems that APCu may only suitable for single server setups. TODO: I couldn't find any references to using APCu with multi-server setups so I'm not sure if that is a safe configuration. 
:::

**Pantheon and Redis or APCu**
Can APCu be used as a cache backend on Pantheon?
Yes, APCu can be used as a cache backend or a "key-value store"; however, this is not recommended. APCu lacks the ability to span multiple application containers. Instead, Pantheon provides a Redis-based Object Cache as a caching backend for Drupal and WordPress, which has coherence across multiple application containers. See more at [Pantheon docs](https://docs.pantheon.io/apcu)




**Creating custom cache bins**

If you are defining a cache bin that is:

* relatively small (likely to have few enough entries to fit within APCu memory), and
* high-read (many cache gets per request, so reducing traffic to the database or other networked backend is worthwhile), and
* low-write (because every write to the bin will invalidate the entire APCu cache of that bin)

then, you can add the default_backend tag to your bin, like so:

```yml
#example.services.yml
services:
  cache.example:
    class: Drupal\Core\Cache\CacheBackendInterface
    tags:
      - { name: cache.bin, default_backend: cache.backend.chainedfast }
    factory_method: get
    factory_service: cache_factory
    arguments: [example]
```

**For site administrators customizing `$settings['cache']`**
Any entry for `$settings['cache']['default']` takes precedence over the default_backend service tag values, so you can disable all `APCu` caching by setting `$settings['cache']['default'] = 'cache.backend.database'`. If you have `$settings['cache']['default']` set to some alternate backend (e.g., `memcache`), but would still like to benefit from `APCu` front caching of some bins, you can add those assignments, like so:

```php
$settings['cache']['bins']['default'] = 'cache.backend.memcache';
$settings['cache']['bins']['bootstrap'] = 'cache.backend.chainedfast';
$settings['cache']['bins']['config'] = 'cache.backend.chainedfast';
$settings['cache']['bins']['discovery'] = 'cache.backend.chainedfast';
```

The bins set to use `cache.backend.chainedfast` will use `APCu` as the front cache to the default backend (e.g., `memcache` in the above example).

**For site administrators of single-server sites that don't need Drush or other CLI access**

::: warning
This references single-server sites not needing Drush.  TODO: I couldn't find any references to using APCu with multi-server setups so I'm not sure if that is a safe configuration. 
:::

Pantheon docs ask in their FAQ: Can APCu be used as a cache backend on Pantheon?
Yes, APCu can be used as a cache backend or a "key-value store"; however, this is not recommended. APCu lacks the ability to span multiple application containers. Instead, Pantheon provides a Redis-based Object Cache as a caching backend for Drupal and WordPress, which has coherence across multiple application containers. See [Pantheon docs](https://docs.pantheon.io/apcu) FAQ's: 

You can optimize further by using APCu exclusively for certain bins, like so:

```php
$settings['cache']['bins']['bootstrap'] = 'cache.backend.apcu';
$settings['cache']['bins']['config'] = 'cache.backend.apcu';
$settings['cache']['bins']['discovery'] = 'cache.backend.apcu';
```

**For site administrators wanting a different front cache than APCu** 

You can copy the `cache.backend.chainedfast` service definition from `core.services.yml` to `sites/default/services.yml` and add arguments to it. For example:

```yml
#services.yml
services:
  cache.backend.chainedfast:
    class: Drupal\Core\Cache\ChainedFastBackendFactory
    arguments: ['@settings', , 'cache.backend.eaccelerator']
    calls:
      - [setContainer, ['@service_container']]
```

## Manually clearing caches


Using SequelAce or similar tool, truncate all the tables that start with `cache_`:

![truncating cache tables](/images/truncate_cache_tables.png)

More [at Drupalize.me](https://drupalize.me/tutorial/clear-drupals-cache)


## Using cache tags with reverse proxies


More [about cache tags on drupal.org](https://www.drupal.org/docs/drupal-apis/cache-api/cache-tags)

Rather than caching responses in Drupal and invalidating them with cache tags, you could also cache responses in reverse proxies (Varnish, CDN …) and then invalidate responses they have cached using cache tags associated with those responses. To allow those reverse proxies to know which cache tags are associated with each response, you can send the cache tags along with a header.

Just like Drupal can send an `X-Drupal-Cache-Tags` header for debugging, it can also send a `Surrogate-Keys` header with space-separated values as expected by some CDNs or a `Cache-Tag` header with comma-separated values as expected by other CDNs. And it could also be a reverse proxy you run yourself, rather than a commercial CDN service.

As a rule of thumb, it's recommended that both your web server and your reverse proxy support response headers with values of up to 16 KB.

1. HTTP is text-based. Cache tags are therefore also text-based. Reverse proxies are free to represent cache tags in a different data structure internally. The 16 KB response header value limit was selected based on 2 factors: A) to ensure it works for the 99% case, B) what is practically achievable. Typical web servers (Apache) and typical CDNs (Fastly) support 16 KB response header values. This means roughly 1000 cache tags, which is enough for the 99% case.
2. The number of cache tags varies widely by site and the specific response. If it's a response that depends on many other things, there will be many cache tags. More than 1000 cache tags on a response will be rare.
3. But, of course, this guideline (~1000 tags/response is sufficient) may and will evolve over time, as we A) see more real-world applications use it, B) see systems specifically leverage/build on top of this capability.

Finally, anything beyond 1000 cache tags probably indicates a deeper problem: that the response is overly complex, that it should be split up. Nothing prevents you going beyond that number in Drupal, but it may require manual fine-tuning which is acceptable for such extremely complex use cases. Arguably, that's the case even for far less than 1000 cache tags.

Read [some details about using cache tags with Varnish on drupal.org - updated July 2023](https://www.drupal.org/docs/drupal-apis/cache-api/cache-tags-varnish)
Also check out [Configuring Varnish for Drupal](https://www.varnish-software.com/developers/tutorials/configuring-varnish-drupal/)

Here are links to some CDN's implementations of tag-based invalidation:
- [CloudFlare](https://developers.cloudflare.com/cache/how-to/purge-cache)
- [Fastly](https://www.fastly.com/documentation/reference/api/#purge_077dfb4aa07f49792b13c87647415537)
- [KeyCDN](https://www.keycdn.com/api#purge-zone-tag)
- [Akamai](https://techdocs.akamai.com/purge-cache/reference/api#concepts)


## class ChainedFastBackend

Drupal has a [ChainedFastBackend](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Cache%21ChainedFastBackend.php/class/ChainedFastBackend/10) as the default cache backend, which allows to store data directly on the web server while ensuring it is correctly synchronized across multiple servers. APCu is the user cache portion of APC (Advanced PHP Cache), which has served us well till PHP 5.5 got its own zend opcache. You can think of it as a key-value store that is stored in memory and the basic operations are `apc_store($key, $data)`, `apc_fetch($keys)` and `apc_delete($keys)`. 

ChainedFastBackend defines a backend with a fast and a consistent backend chain.

In order to mitigate a network roundtrip for each cache get operation, this cache allows a fast backend to be put in front of a slow(er) backend. Typically, the fast backend will be something like `APCu`, and be bound to a single web node, and will not require a network round trip to fetch a cache item. The fast backend will also typically be inconsistent (will only see changes from one web node). The slower backend will be something like Mysql, Memcached or Redis, and will be used by all web nodes, thus making it consistent, but also require a network round trip for each cache get.

In addition to being useful for sites running on multiple web nodes, this backend can also be useful for sites running on a single web node where the fast backend (e.g., APCu) isn't shareable between the web and CLI processes. Single-node configurations that don't have that limitation can just use the fast cache backend directly.

We always use the fast backend when reading (`get()`) entries from cache, but check whether they were created before the last write (`set()`) to this (chained) cache backend. Those cache entries that were created before the last write are discarded, but we use their cache IDs to then read them from the consistent (slower) cache backend instead; at the same time we update the fast cache backend so that the next read will hit the faster backend again. Hence we can guarantee that the cache entries we return are all up-to-date, and maximally exploit the faster cache backend. This cache backend uses and maintains a "last write timestamp" to determine which cache entries should be discarded.

Because this backend will mark all the cache entries in a bin as out-dated for each write to a bin, it is best suited to bins with fewer changes.

Note that this is designed specifically for combining a fast inconsistent cache backend with a slower consistent cache back-end. To still function correctly, it needs to do a consistency check (see the "last write timestamp" logic). This contrasts with \Drupal\Core\Cache\BackendChain, which assumes both chained cache backends are consistent, thus a consistency check being pointless.  See [class ChainedFastBackend API docs on drupal.org](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Cache%21ChainedFastBackend.php/class/ChainedFastBackend/10)

## APCu

`APCu` is the official replacement for the outdated APC extension. `APC` provided both opcode caching and object caching. As PHP versions 5.5 and above include their own opcache, `APC` was no longer compatible, and its opcache functionality became useless. The developers of `APC` then created `APCu`, which offers only the object caching (read \"in memory data caching\") functionality (they removed the outdated opcache). Read [more on php.net](https://www.php.net/manual/en/book.apcu.php)

::: tip Note
APCu is not the same as apc!
:::

APCu support is built into Drupal Core. More at this [change record from Sep 2014](https://www.drupal.org/node/2327507): 

In order to improve cache performance, Drupal 8 now has:

A `cache.backend.apcu` service that site administrators can assign as the backend of a cache bin via `$settings['cache']` in `settings.php` for sites running on a single server, with a PHP installation that has APCu enabled, and that do not use Drush or other command line scripts.

::: warning
This references single-server sites not needing Drush.  This may not be suitable for multi-server setups.
:::

A `cache.backend.chainedfast` service that combines APCu availability detection, APCu front caching, and cross-server / cross-process consistency management via chaining to a secondary backend (either the database or whatever is configured for `$settings['cache']['default']`).

A `default_backend` service tag (the value of which can be set to a backend service name, such as `cache.backend.chainedfast`) that module developers can assign to cache bin services to identify bins that are good candidates for specialized cache backends.

The above tag assigned to the `cache.bootstrap`, `cache.config`, and `cache.discovery` bin services.

This means that by default (on a site with nothing set for `$settings['cache']` in `settings.php`), the bootstrap, config, and discovery cache bins automatically benefit from `APCu` caching if `APCu` is available, and this is compatible with Drush usage (e.g., Drush can be used to clear caches and the web process receives that cache clear) and multi-server deployments.

`APCu` will act as a very fast local cache for all requests. Other cache backends can act as bigger, more general cache backend that is consistent across processes or servers.

## The Basics

### Internal page cache vs Dynamic page cache
The Internal page cache caches up pages for use by anonymous users. Pages requested by anonymous users are stored the first time they are requested and then reused.  Depending on your site configuration, the performance improvement may be significant. The Internal page cache assumes that all pages served to anonymous users will be identical, regardless of the implementation of cache contexts. If you want to use cache contexts to vary the content served to anonymous users, this core module (machine name: `page_cache`) must be disabled, and the performance impact that entails incurred.

The Dynamic page cache is used to cache pages minus the personalized parts, and is therefore useful for all users (both anonymous & authenticated). Dynamic page cache requires no configuration. The module uses the metadata (cache contexts) of all the components on a page to figure out if it can be cached. This core module\'s machine name is `dynamic_page_cache`. It was previously known as `Smart Cache`.

Read [more about the Internal Page Cache on drupal.org updated November 2023](https://www.drupal.org/docs/administering-a-drupal-site/internal-page-cache)
Also for more on [Dynamic page cache on drupal.org](https://www.drupal.org/docs/8/core/modules/dynamic-page-cache/overview)




### Cache tags

Cache tags are for dependencies on data that are managed by Drupal, and are the easiest way to control cache. For example, if we have a news story content type and a block that shows a list of three nodes on the homepage. How would the homepage cache (or more specifically the news block cache) be cleared if we changed one of the news stories?  The answer is cache tags.

Cache tags are strings that are passed around in sets (order doesn't matter), so they are typehinted to string[]. They're sets because a single cache item can depend on (or be invalidated by) many cache tags.

By convention, they are of the form `thing:identifier` — and when there's no concept of multiple instances of a thing, it is of the form `thing`. The only rule is that it cannot contain spaces. There is no strict syntax.

**Some examples:**
- `node:5` — cache tag for Node id 5
- `user:3` — cache tag for User id 3
- `node_list` — list cache tag for Node entities (invalidated whenever any Node entity is updated, deleted or created, i.e., when a listing of nodes may need to change). Applicable to any entity type in following format: `{entity_type}_list`.
- `node_list:article` — list cache tag for the article content type (or bundle). Applicable to any entity + bundle type in following format: `{entity_type}_list:{bundle}`.
- `config:node_type_list` — list cache tag for Node type entities (invalidated whenever any **content types** are updated, deleted or created). Applicable to any entity type in the following format: config:`{entity_bundle_type}_list`.
`config:system.performance` — cache tag for the `system.performance` configuration
`library_info` — cache tag for asset libraries

The data that Drupal manages fall in 3 categories:

1. `entities` — these have cache tags of the form `<entity type ID>:<entity ID>` as well as `<entity type ID>_list` and `<entity type ID>_list:<bundle>` to invalidate lists of entities. Config entity types use the cache tag of the underlying configuration object.
2. `configuration` — these have cache tags of the form config:`<configuration name>`.
3. custom (for example `library_info`)

Drupal provides cache tags for `entities` & `configuration` — see the [EntityBase class](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Entity%21EntityBase.php/class/EntityBase/10) and the [ConfigBase class](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Config%21ConfigBase.php/class/ConfigBase/10). All specific entity types and configuration objects inherit from those.

Although many entity types follow a predictable cache tag format of `<entity type ID>:<entity ID>`, your code shouldn't rely on this. Instead, it should retrieve cache tags to invalidate for a single entity using its `::getCacheTags()` method, e.g., `$node->getCacheTags()`, `$user->getCacheTags()`, `$view->getCacheTags()` etc.

In addition, it may be necessary to invalidate listings-based caches that depend on data from the entity in question e.g., refreshing the rendered HTML for a listing when a new entity for it is created. This can be done using `EntityTypeInterface::getListCacheTags()`, then invalidating any returned by that method along with the entity's own tag(s). Entities with bundles also automatically have a more specific cache tag that includes their bundle, to allow for more targeted invalidation of lists.

It is also possible to define custom, more specific cache tags based on values that entities have, for example a term reference field for lists that show entities that have a certain term. Invalidation for such tags can be put in custom presave/delete entity hooks:

```php
function yourmodule_node_presave(NodeInterface $node) {
  $tags = [];
  if ($node->hasField('field_category')) {
    foreach ($node->get('field_category') as $item) {
      $tags[] = 'mysite:node:category:' . $item->target_id;
    }
  }
  if ($tags) {
    Cache::invalidateTags($tags);
  }
}
```
These tags can then be used in code and in views using the [Views Custom Cache Tag module](https://www.drupal.org/project/views_custom_cache_tag).

::: tip Note
There is currently no API to get per-bundle and more specific cache tags from an entity or other object. That is because it is not the entity that decided which list cache tags are relevant for a certain list/query, that depends on the query itself. Future Drupal core versions will likely improve out of the box support for per-bundle cache tags and for example integrate them into the entity query builder and views.
:::


## Reference

* [Drupal: cache tags for all, regardles of your backend From Matt Glaman 22, August 2022](https://mglaman.dev/blog/drupal-cache-tags-all-regardless-your-backend)
* [Debugging your render cacheable metadata in Drupal From Matt Glaman 14, February 2023](https://mglaman.dev/blog/debugging-your-render-cacheable-metadata-drupal)
* [Cache contexts overview on drupal.org](https://www.drupal.org/docs/drupal-apis/cache-api/cache-contexts)
* [Caching in Drupal 8 a quick overview of Cache tags, cache context and cache max-age with simple examples](https://zu.com/articles/caching-drupal-8)
* [Nedcamp video on caching by Kelly Lucas from November 2018](https://www.youtube.com/watch?v=QCZe2K13bd0&list=PLgfWMnl57dv5KmHaK4AngrQAryjO_ylaM&t=0s&index=16)
* [#! code: Drupal 9: Debugging Cache Problems With The Cache Review Module, September 2022](https://www.hashbangcode.com/article/drupal-9-debugging-cache-problems-cache-review-module)
* [#! code: Drupal 9: Using The Caching API To Store Data, April 2022](https://www.hashbangcode.com/article/drupal-9-using-caching-api-store-data)
* [#! code: Drupal 8: Custom Cache Bin, September 2019](https://www.hashbangcode.com/article/drupal-8-custom-cache-bins)
* [New cache backend configuration order, per-bin default before default configuration (How to specify cache backend), June 2016](https://www.drupal.org/node/2754947)
* [Drupal Core Cache API ](https://api.drupal.org/api/drupal/core!core.api.php/group/cache)
* [Drupal BigPipe Module: The Phenomenal to Improve Website Performance](https://www.lnwebworks.com/Insight/drupal-bigpipe-module)
* [Drupal performance — a complete Drupal self-help guide to ensuring your website’s performance by Kristen Pol Sep 2023](https://salsa.digital/insights/drupal-performance-a-complete-drupal-self-help-guide-to-ensuring-your-websites-performance)
* [Cache tags on Drupal.org updated March 2023](https://www.drupal.org/docs/drupal-apis/cache-api/cache-tags#s-debugging)
* [Cacheability of render arrays on drupal.org - updated April 2023](https://www.drupal.org/docs/drupal-apis/render-api/cacheability-of-render-arrays)


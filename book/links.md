---
title: Links
---

# Links, Aliases and URLs
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=links.md)




## The Drupal Core Url Class

The `Drupal\Core\Url` class is often used to create URL's. Two important methods are:

`Url::fromRoute()` which takes a route name and parameters and

`Url::fromUri()` which takes an internal or external URL

You can also set attributes for the url using:

```php
$helpdesk_url->setOptions(['attributes' => ['target' => '_blank']]);
```
See how these are used in some of the examples below.

## Create an internal url

First a simple URL:

```php
use Drupal\Core\Url

$url = Url::fromUri('internal:/reports/search');

// Or. 

$url = Url::fromUri('internal:/dashboard/achievements')

// Or Using link generator to create a GeneratedLink.
$url = Url::fromUri('internal:/node/1');
$link = \Drupal::service('link_generator')->generate('My link', $url);

// OR
$url = Url::fromUri('internal:/');
// OR
'url' => Url::fromRoute('<front>'),
// OR
'url' => Url::fromRoute('hello_world.hello'),
```

Then something more complicated like this URL to `/reports/search?user=admin`

```php
  $option = [
    'query' => ['user' => 'admin'],
  ];
  $url = Url::fromUri('internal:/reports/search', $option);
```

## Create an external url

```php
use Drupal\Core\Url

$url = Url::fromUri('http://testsite.com/go/here');
// Set options like target = '_blank' to open link in new window.
$url->setOptions(['attributes' => ['target' => '_blank']]);
```



## The Drupal Core Link Class

Closely related and often used in conjunction with the Drupal Core `Url` class is the `Drupal\Core\Link` class.  These can be used in render arrays. Note that you specify attributes like `target = "_blank"` in the Url (using `setOptions`), rather than the link.  It doesn't seem like you can specify attributes in the link.


You can generate links several different ways.

### Create a link to a node

```php
//Using link generator to create a GeneratedLink.
$url = Url::fromUri('internal:/node/1');
$link = \Drupal::service('link_generator')->generate('Scottish Pie', $url);

// To get the string of the link:
$str = $link->__toString();
// or
$str = (string) $link;
// $str = <a href="/recipes/scottish-pie">Scottish Pie</a>
```

### Render a link to a node

To create a render array element for the link:

```php
$build['scottish_pie'] = [
  '#type' => 'link',
  '#title' => $this->t('Scottish Pie'),
  '#url' => Url::fromUri('internal:/node/9'),
  '#attributes' => [
    'class' => ['scottish-pie-class'],
  ],
];

// OR.
$url = Url::fromUri('internal:/node/9');
$link = \Drupal::service('link_generator')->generate('Scottish Pie', $url);
// Render the link.
$build['link'] = [
  '#markup' => $link,
];
```

To build the render array manually:

```php
$nid = 9;
$url = Url::fromRoute('entity.node.canonical', ['node' => $nid]);
//$url = Url::fromUri('internal:/node/9');
$link_text =  [
  '#type' => 'html_tag',
  '#tag' => 'div',
  '#attributes' => [
    'class' => ['load-more-class'],
  ],
  '#value' => $this->t('Load related recipe'),
];
$link = Link::fromTextAndUrl($link_text, $url);
$build['load_more'] = [
  '#markup' => $link->toString(),
];
```

[More at Stack Exchange](https://drupal.stackexchange.com/questions/144992/how-do-i-create-a-link):


### Create an absolute link to a node

To create an absolute link, you add this option to the URL, not the link:

[From Stack Exchange](https://drupal.stackexchange.com/questions/144992/how-do-i-create-a-link):

```php
$url = Url::fromRoute('entity.node.canonical', ['node' => $nid], ['absolute' => TRUE]);
$link = Link::fromTextAndUrl($this->t('Read more'), $url);
$build['read_more'] = $link->toRenderable();
```

### Create a link to a route
  
```php
use Drupal\Core\Url;
use Drupal\Core\Link;

$url = Url::fromRoute('my_route', ['param1' => $value1, 'param2' => $value2]);
// The Link class implements __toString() so you can print this directly.
$link = \Drupal::service('link_generator')->generate('My link', $url);
// Or
$link = Link::fromTextAndUrl('My link', $url);
```

Links can generate a render array  of `#type => 'link'`using `toRenderable()`.

Using the Link object, we can gerneate a link:


```php
$link = \Drupal::service('link_generator')->generatefromLink($link_object)
```


### Add a class to your link

To add a class to your link, you also need to add this to the URL, not the link:

[From Stack Exchange](https://drupal.stackexchange.com/questions/144992/how-do-i-create-a-link):

```php
$options = [
  'attributes' => [
    'class' => [
      'read-more-link',
    ],
  ],
];
$url = Url::fromRoute('entity.node.canonical', ['node' => $nid], $options);
$link = Link::fromTextAndUrl($this->t('Read more'), $url);
$build['read_more'] = $link->toRenderable();
```

### Add a query string to a link

To add a query string to your link, you also need to this to the URL, not the link.

[From Stack Exchange](https://drupal.stackexchange.com/questions/144992/how-do-i-create-a-link):

```php
$options = [
  'query' => [
    'car' => 'BMW',
    'model' => 'mini-cooper',
  ],
  'attributes' => [
    'class' => [
      'read-more-link',
    ],
  ],
];
$url = Url::fromRoute('entity.node.canonical', ['node' => $nid], $options);
$link = Link::fromTextAndUrl($this->t('Read more'), $url);
$build['read_more'] = $link->toRenderable();
```

### Create a link that opens in a new window

To set the link to open in a new window with target = _blank:

Note. see Url::setOptions as well.

```php
$options = [
  'attributes' => [
    'target' => '_blank'
  ],
];
$url = Url::fromRoute('entity.media.edit_form', ['media' => $entity->id()], $options);
$link = Link::fromTextAndUrl(t('Edit'), $url);
$form['entity']['edit_link'] = $link->toRenderable();
```



### Create a link to a path with parameters 

To create a link to a path like `/reports/search?user=admin` use this code.

```php
$option = [
 'query' => ['user' => 'admin'],
];
$url = Url::fromUri('internal:/reports/search', $option);

// use the Link class.
$link = Link::fromTextAndUrl('My link', $url);
$renderable_array = $link->toRenderable();
return $renderable_array;
```

### Another way to create a link to a node: 

```php
$nid = $item->id();
$options = ['relative' => TRUE];  //could be absolute instead of relative.

$url = Url::fromRoute('entity.node.canonical',['node' => $nid], $options);
$link = \Drupal::service('link_generator')->generate('My link', $url);
```

### Create a link to an internal URL

```php
use Drupal\Core\Url

$url = Url::fromUri('internal:/reports/search');
$link = \Drupal::service('link_generator')->generate('My link', $url);

// ->toString() will extract the string of the URL.
$url_string = Url::fromUri('internal:/node/' . $id)->toString();
```

### Create a link to homepage

```php
$url = Url::fromRoute('<front>');
$link = Link::fromTextAndUrl($this->t('Home'), $url);
$build['homepage_link'] = $link->toRenderable();
```

## Link Fields

### Check if a link field is empty
```php
if (!$citation_node->field_link->uri) {
  // Empty.
}
```

### Retrieve a link field from a node or a paragraph

The link field `field_link` is extracted from the node and a valid uri is extracted from that field.

```php
$correction_node = Node::load($nid);
$url = $correction_node->get('field_link')->uri;
```

Or from a paragraph field

```php
use Drupal\paragraphs\Entity\Paragraph;

$para = Paragraph::load($target_id);
$link = $para->field_link;
$link_uri = $para->field_link->uri;
```

Or a more convoluted example that extracts the url string for display
from a link field.

```php
if ($sf_contract) {
  // first() returns a Drupal\link\Plugin\FieldType\LinkItem
  $vendor_url = $sf_contract->field_vendor_url->first();
  if ($vendor_url) {
    // returns a Drupal\Core\Url.
    $vendor_url = $vendor_url->getUrl();  
    $vendor_url_string = $vendor_url->toString();
  }
```

Removing `->first()` as in:

```php
$vendor_url = $sf_contract->field_vendor_url;
```

returns a `Drupal\Core\Field\FieldItemList` which is a list of fields so you then would have to pull out the first field and extract the URI out of that. I'm not sure why Drupal considers it multiple values instead of just one. This was not set up as a multivalue field.


### Extract an external URL from a link field

You can get the URL (for external links) and then just the text part. Note this doesn't work for internal links. 

```php
$citation_link = $citation->get('field_link');
if (!$citation_link->isEmpty()) {
  $url_string = $citation->field_link->first()->getUrl()->toString();
}
```

This slightly convoluted example has a reference field `field_sf_contract_ref` which has a link to another entity which has a field `field_vendor_url`. The call : `field_vendor_url->first()->getUrl()` does the work to retrieve the URL. Also, this is a single-value field (not a multivalue field) -- so the `first()` call may be a little confusing. Once we have the Url object, we can extract the URI with `getUri()` or `toString()`:

```php
$vendor_url = $node->field_sf_contract_ref->entity->field_vendor_url->first()->getUrl();
if ($vendor_url) {
  $vendor_url = $vendor_url->getUri();
  //OR
  $vendor_url = $vendor_url->toString();
}
```



### Extract an internal URL from a link field

For internal links, use `getUrl()`for the URL and `->title` for the title. Here we extract the URL and the title from a `field_link` field.

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

## Get the NID from a URL Alias

To get the nid for a node, you can pass the URL alias parameter to `getPathByAlias()` of the `path_alias.manager service.

```php
// Given "/test-node, returns "/node/32".
$alias = "/test-node";
$path = \Drupal::service('path_alias.manager')->getPathByAlias($alias);
```

OR

If you have a URL for a node and you want its nid

```php
$route = $url->getRouteParameters();
// first check if it's a node.
if (isset($route['node'])) {
  $nid = $route['node'];
}
```

## Get the Taxonomy Term ID from a URL alias

Returns taxonomy/term/5

```php
$term_path_with_tid = \Drupal::service('path_alias.manager')->getPathByAlias('/hunger-strike');
```

## Get URL alias for a taxonomy term

This returns term/5 if no alias is set, otherwise it returns the alias.

```php
$term5_url = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => 5], $options);
$term5_alias = $term5_url->toString();
```

## Get the User ID from a URL alias

Use `path_alias.manager` service to return `"/user/2"`

```php
//User
$user_path_with_uid = \Drupal::service('path_alias.manager')->getPathByAlias('/selwyn-the-chap');
```

## Get the URL alias for a node

If no alias is set, this will return `"/node/32"`. Note. If there are multiple aliases, you will get the most recently created one.

```php
$node_path = '/node/32';
$node32_alias = \Drupal::service('path_alias.manager')->getAliasByPath($node_path);
```

Use this code if you need the absolute URL . If `node/32` has a URL alias set to \"/test-node\" it returns \"https://d9book2.ddev.site/test-node\" . If you specify `absolute => FALSE`, it returns \"/test-node\" .

```php
use Drupal\Core\Url;

// Note. If a pathauto url alias is not set, it returns '/node/32'
$nid = 32;
$options = ['absolute' => TRUE]; 
$url = Url::fromRoute('entity.node.canonical', ['node' => $nid], $options);
// make a string
$url_string = $url->toString(); 
```

## Create a Node Alias

URL aliases are entities so you create them like you would any entity.
Be sure to save() them.

```php
$node_path = "/node/32";
$new_alias = "/test-node";

/** @var \Drupal\path_alias\PathAliasInterface $path_alias */
$my_node_alias = \Drupal::entityTypeManager()->getStorage('path_alias')->create([
  'path' => $node_path,
  'alias' => $new_alias,
  'langcode' => 'en',
]);
$my_node_alias->save();
```

## Delete a Node alias

```php
$path_alias_manager = \Drupal::entityTypeManager()->getStorage('path_alias');
// Load path alias by path.
$alias_objects = $path_alias_manager->loadByProperties([
  'path' => '/node/' . $nid
]);
foreach ($alias_objects as $alias_object) {
  // Delete the path alias
  $alias_object->delete();
} 
```

More on [Stack Exchange](https://drupal.stackexchange.com/questions/317693/how-can-i-delete-an-existing-path-alias/317694#317694)


## Get the current Path

`\Drupal::service('path.current')->getPath()` returns the current relative path. For node pages, the return value will be in the form \"/node/32\" For taxonomy \"taxonomy/term/5\", for user \"user/2\" if it exists otherwise it will return the current request URI.

```php
$current_path  = \Drupal::service('path.current')->getPath();
// Get the alias (i.e. if the user entered node/123, this will return e.g. /bicycles/super-cool-one) 
$alias = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);

// Get path with query string e.g. /abc/def/123?a=fred.
$current_path_and_alias = \Drupal::request()->getRequestUri();

// Get path e.g. /abc/def/123
$current_path = Url::fromRoute('<current>')->toString();
```
[Lots more on the Drupal Stackexchange](https://drupal.stackexchange.com/questions/106103/how-do-i-get-the-current-path-alias-or-path)


## Get current nid, node type and title

There are two ways to retrieve the current node -- via the request or via the route

```php
$node = \Drupal::request()->attributes->get('node');
$nid = $node->id();
```

OR

```php
$node = \Drupal::routeMatch()->getParameter('node');
if ($node instanceof \Drupal\node\NodeInterface) {
  // You can get nid and anything else you need from the node object.
  $nid = $node->id();
  $nodeType = $node->bundle();
  $nodeTitle = $node->getTitle();
}
```

If you need to use the node object in `hook_preprocess_page()` on the preview page, you need to use the \"node_preview\" parameter, instead of the \"node\" parameter:

```php
function mymodule_preprocess_page(&$vars) {

  $route_name = \Drupal::routeMatch()->getRouteName();

  if ($route_name == 'entity.node.canonical') {
    $node = \Drupal::routeMatch()->getParameter('node');
  }
  elseif ($route_name == 'entity.node.preview') {
    $node = \Drupal::routeMatch()->getParameter('node_preview');
  }
```

And from <https://drupal.stackexchange.com/questions/145823/how-do-i-get-the-current-node-id> when you are using or creating a custom block then you have to follow this code to get current node id. Not sure if it is correct

```php
use Drupal\Core\Cache\Cache;

$node = \Drupal::routeMatch()->getParameter('node');
if ($node instanceof \Drupal\node\NodeInterface) {
  $nid = $node->id();
}

// for cache
public function getCacheTags() {
  //With this when your node changes your block will rebuild
  if ($node = \Drupal::routeMatch()->getParameter('node')) {
    //if there is node add its cachetag
    return Cache::mergeTags(parent::getCacheTags(), ['node:' . $node->id()]);
  } 
  else {
    //Return default tags instead.
    return parent::getCacheTags();
  }
}

public function getCacheContexts() {
  //if you depend on \Drupal::routeMatch()
  //you must set context of this block with 'route' context tag.
  //Every new route this block will rebuild
  return Cache::mergeContexts(parent::getCacheContexts(), ['route']);
}
```

## Get current Route name

A Drupal route is returned in the form of a string e.g.
view.files_browser.page_1

```php
$current_route = \Drupal::routeMatch()->getRouteName();
```

It returns \"entity.node.canonical\" for the nodes, \"system.404\" for the 404
pages, \"entity.taxonomy_term.canonical\" for the taxonomy pages,
\"entity.user.canonical\" for the users and custom route name that we define
in `modulename.routing.yml` file.

## Get the current Document root path

This will return the current document root path like
\"/var/www/html/project1\".

```php
$image_path = \Drupal::service('file_system')->realpath();
```

## Retrieve URL argument parameters

You can extract the url arguments with

```php
$current_path = \Drupal::service('path.current')->getPath();
$path_args = explode('/', $current_path);
$term_name = $path_args[3];
```
For <https://txg.ddev.site/newsroom/search/?country=1206>

![Image path arguments](/images/image-path-args.png)


## Retrieve query and GET or POST parameters (\$\_POST and \$\_GET)

For get variables

```php
$query = \Drupal::request()->query->get('name');
$name  = $_GET['abc'];
```

For POST variables:

```php
$name = \Drupal::request()->request->get('name');
//or 
$name  = $_POST['abc'];
```

For all items in a GET:

```php
$query = \Drupal::request()->query->all();
$search_term = $query['query'];
$collection = $query['collection'];
```

Be wary about caching. From [Stack Exchange](https://drupal.stackexchange.com/questions/231953/get-in-drupal-8/231954#231954) the code provided only works the first time so it is important to add a '#cache' context in the markup.

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

The request is being cached, you need to tell the system to vary by the
query arguments:

```php
$day = [
    '#markup' => \Drupal::request()->query->get('id'),
    '#cache' => [
        'contexts' => ['url.query_args:id'],
    ],
];
```

[More about caching render arrays](https://www.drupal.org/docs/8/api/render-api/cacheability-of-render-arrays)


## Modify URL Aliases programmatically with hook_pathauto_alias_alter

The [pathauto](https://www.drupal.org/project/pathauto) contrib module includes a nice hook that you can use to modify url aliases on the fly.

You just do your necessary checks (the current entity is stored in `\context['data']`) and change the alias that is passed. Pathauto does the rest.

As implemented in a module file.

```php
/**
 * Implements hook_pathauto_alias_alter().
 *
 * Note. This function is a stopgap measure to handle pathauto
 * token failing to return the parent menu item alias.
 * Using the pattern:
 * [node:menu-link:parent:url:path]/[node:title]
 * never returns the parent alias for some inexplicable reason.
 * This works fine on a fresh Drupal 9 site. Much research
 * yielded no results so this function hijacks pathauto's
 * efforts to create the alias.  It checks for the presence
 * of the parent menu alias.
 * That parent alias is inserted in the path if it isn't found.
 * If the parent alias is correctly inserted, it should not impact
 * the correct functioning of pathauto and token.
 */
function dirt_pathauto_alias_alter(&$alias, array &$context) {

  // Change the alias if it doesn't include the parent item.
  /** @var Drupal\pathauto\Entity\PathautoPattern $pattern*/
  $pattern = $context["pattern"];
  /* The pattern will be something like this
   * [node:menu-link:parent:url:path]/[node:title]
   * so I could test for it
   * but it doesn't seem necessary.
   * Of course, if they change the pattern later this
   * pattern will overwrite it.
   *
   */
  $bundles = ['page', 'audience', 'overview', 'program_area'];
  if (in_array($context["bundle"], $bundles)) {
    $op = $context['op'];
    if (in_array($op, ['insert', 'update', 'bulkupdate'])) {
      $parts = explode('/', $alias);
      if (count($parts) == 2) {
        if ($parts[0] == "") {
          //Missing the parent link.
          $id = $pattern->id();
          //Is this the default pathauto pattern?
          if ($id == "default") {
            //Is there a parent link?
            $nid = $context["data"]["node"]->id();
            $parent_link = _findParentMenuItem($nid);
            if ($parent_link) {
              $parent_alias = $parent_link['#url']->toString();
              //Update the alias to include the parent alias.
              if ($parent_alias) {
                $alias = $parent_alias . $alias;
              }
            }
          }
        }
      }
    }
  }
}
```

Also see [an example on makedrupaleasy.com from Dec 2017](https://makedrupaleasy.com/articles/drupal-version-7-9-how-update-alias-programmatically-using-value-field)

## Drupal l() is deprecated

The `l()`<Badge type="danger" text="Deprecated" /> method was a convenience wrapper for the link generator service's `generate()` method.
So do this instead:

```php
use Drupal\Core\Url;
use Drupal\Core\Link;

$url = Url::fromRoute('entity.node.edit_form', ['node' => NID]);
$project_link = Link::fromTextAndUrl(t('Open Project'), $url);
$project_link = $project_link->toRenderable();
// If you need some attributes.
$project_link['#attributes'] = ['class' => ['button', 'button-action', 'button--primary', 'button--small']];
print render($project_link);
```

## Reference links

- [#! code: Drupal 9: Programmatically Creating And Using URLs And Links, March 2022](https://www.hashbangcode.com/article/drupal-9-programmatically-creating-and-using-urls-and-links)
- [Creating Links in Code for Drupal 8 by David Valdez and Benjamin Melançon - Apr 2017](https://agaric.coop/blog/creating-links-code-drupal-8) 
- [How do I create a link from Stack Exchange - Jan 2017](https://drupal.stackexchange.com/questions/144992/how-do-i-create-a-link)

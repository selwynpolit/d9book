- [Blocks](#blocks)
  - [Create a block with Drush generate](#create-a-block-with-drush-generate)
  - [Anatomy of a custom block with dependency injection](#anatomy-of-a-custom-block-with-dependency-injection)
  - [Create a block with an entityQuery](#create-a-block-with-an-entityquery)
  - [Create a Block with a corresponding config form](#create-a-block-with-a-corresponding-config-form)
  - [Modify a block with hook_block_view_alter or hook_block_build_alter](#modify-a-block-with-hook_block_view_alter-or-hook_block_build_alter)
  - [Disable caching in a block](#disable-caching-in-a-block)
  - [Add a configuration form to your block](#add-a-configuration-form-to-your-block)
  - [Block display not updating after changing block content](#block-display-not-updating-after-changing-block-content)
  - [Block Permission (blockAccess)](#block-permission-blockaccess)

# Blocks

Blocks are plugins, which are re-usable pieces of code following design patterns. Plugins are also used to define views arguments, field formatters, field widgets etc. etc. They are found all over Drupal core and contrib modules in the /src/Plugin directory.

![Graphical user interface Description automatically generated with
medium confidence](images/media/image1.png)

For more see
<https://www.drupal.org/docs/drupal-apis/plugin-api/plugin-api-overview>
and
<https://www.drupal.org/docs/8/api/plugin-api/annotations-based-plugins>

## Create a block with Drush generate

Use Drush's code generation ability to quickly generate the code you need to create your own custom block.

First generate a module if you don't have one. Here we generate a module called Block Module with a machine name: block_module.

```
$ drush generate module

Welcome to module generator!

------------------------------------------------------------

Module name \[Web\]:

➤ Block Module

Module machine name \[block_module\]:

➤

Module description \[Provides additional functionality for the site.\]:

➤ Custom module to explore Drupal blocks

Package \[Custom\]:

➤

Dependencies (comma separated):

➤

Would you like to create module file? \[No\]:

➤ yes

Would you like to create install file? \[No\]:

➤

Would you like to create libraries.yml file? \[No\]:

➤

Would you like to create permissions.yml file? \[No\]:

➤

Would you like to create event subscriber? \[No\]:

➤

Would you like to create block plugin? \[No\]:

➤

Would you like to create a controller? \[No\]:

➤

Would you like to create settings form? \[No\]:

➤

The following directories and files have been created or updated:

----------------------------------------------------------

•
/Users/selwyn/Sites/ddev93/web/modules/custom/block_module/block_module.info.yml

•
/Users/selwyn/Sites/ddev93/web/modules/custom/block_module/block_module.module
```

Use "drush generate" to create the code for a block. Specify the module name e.g. block_module so Drush knows where to put the block code. We also must give the block an admin label, plugin ID and class.

```
$ drush generate block

Welcome to block generator!

----------------------------------------------------------

Module machine name \[web\]:

➤ block_module

Block admin label \[Example\]:

➤ Block Module Example

Plugin ID \[block_module_block_module_example\]:

➤

Plugin class \[BlockModuleExampleBlock\]:

➤

Block category \[Custom\]:

➤

Make the block configurable? \[No\]:

➤

Would you like to inject dependencies? \[No\]:

➤

Create access callback? \[No\]:

➤

The following directories and files have been created or updated:

----------------------------------------------------------

•
/Users/selwyn/Sites/ddev93/web/modules/block_module/src/Plugin/Block/BlockModuleExampleBlock.php
```

This generates a file at `web/modules/custom/block_module/src/Plugin/Block/BlockModuleExampleBlock.php` which looks like this:

```php
<?php

namespace Drupal\block_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block module example block.
 *
 * @Block(
 *   id = "block_module_block_module_example",
 *   admin_label = @Translation("Block Module Example"),
 *   category = @Translation("Custom")
 * )
 */
class BlockModuleExampleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
```

Enable the module with:

`ddev drush en block_module`

clear cache with :

`ddev drush cr`

In Drupal, navigate to /admin/structure/block and place the block (block
module example) in the content area. See the diagram below on how to
place the block in the content area.

![Graphical user interface, table Description automatically
generated](images/media/image2.png)

![Graphical user interface Description automatically
generated](images/media/image3.png)

You may have to clear the Drupal cache again to get the new block to show up in the list. After clicking "place block," a "configure block" screen appears. You can safely just click "save block."

![Graphical user interface, application Description automatically
generated](images/media/image4.png)

Navigate back to the home page of the site and you'll see your block appearing. Screenshot below:

![Graphical user interface, text, application, email Description
automatically generated](images/media/image5.png)

You can safely remove the block by to the block layout page, choose "remove" from the dropdown next to your "Block Module Example"

![Graphical user interface, application Description automatically
generated](images/media/image6.png)

## Anatomy of a custom block with dependency injection

The block class php file is usually in `\<Drupal web root
\>/modules/custom/mymodule/src/Plugin/Block`.

e.g.
`dev1/web/modules/custom/image_gallery/src/Plugin/Block/ImageGalleryBlock.php`

or

`dev1/web/modules/contrib/examples/block_example/src/Plugin/Block/ExampleConfigurableTextBlock.php`

Specify namespace:

`namespace Drupal\iai_wea\Plugin\Block;`

Blocks always extend BlockBase but can also implement other
interfaces... see below.

`Class ImageGalleryBlock extends BlockBase`

If you want to use Dependency Injection implement

`ContainerFactoryPluginInterface`

e.g.

```php
class ImageGalleryBlock extends BlockBase implements
ContainerFactoryPluginInterface {
Selwyn, what is this?
```
Be sure to include:

```php
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
```
and for annotation translation:

```php
use Drupal\Core\Annotation\Translation;
```

You can annotate like this:

```php
/**
 * Hello World Salutation block.
 *
 * @Block(
 *   id = "hello_world_salutation_block",
 *   admin_label = @Translation("Hello world salutation"),
 *   category = @Translation("Custom")
 * )
 */
```

Or like this.

```php
/**
 * Provides an image gallery block.
 *
 * @Block(
 *   id = "ig_product_image_gallery",
 *   admin_label = @Translation("Product Image Gallery"),
 *   category = @Translation("Image Display"),
 *   context = {
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node")
 *     )
 *   }
 * )
 */
```

In most cases you will implement ContainerFactoryPluginInterface.
Plugins require this for dependency injection.

So don't forget:

```php
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;


class HelloWorldSalutationBlock extends BlockBase implements ContainerFactoryPluginInterface {
```

If you want dependency injection, you will need a create() function.

This will call the constructor (to do lazy loading) and call the
container to `->get()` the service you need. In the example below
`$container->get('hello_world.salutation')` does the trick. `return new static()` calls your class constructor.

Be sure to add your service to the list of parameters in the
constructor. `$container->get('hello_world.salutation')`.

```PHP
/**
 * {@inheritdoc}
 */
public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
  return new static(
    $configuration,
    $plugin_id,
    $plugin_definition,
    $container->get('hello_world.salutation')
  ); 
}
```

Here are your `__constructor()` and a `build()` functions. See the 4th param -- `HelloWorldSalutationService $salutation` -- that's the injected service.

```PHP
/**
 * Construct.
 *
 * @param array $configuration
 *   A configuration array containing information about the plugin instance.
 * @param string $plugin_id
 *   The plugin_id for the plugin instance.
 * @param string $plugin_definition
 *   The plugin implementation definition.
 * @param \Drupal\hello_world\HelloWorldSalutation $salutation
 */
public function __construct(array $configuration, $plugin_id, $plugin_definition, HelloWorldSalutationService $salutation) {
  parent::__construct($configuration, $plugin_id, $plugin_definition);
  $this->salutation = $salutation;
}
```

```PHP
/**
 * {@inheritdoc}
 */
public function build() {
  return [
    '#markup' => $this->salutation->getSalutation(),
  ];
}
```
TODO: NEED A BETTER EXAMPLE OF A D.I. BLOCK HERE especially showing a build()

## Create a block with an entityQuery

You often need to query some data from Drupal and display it in a block.

From \~/Sites/oag/docroot/modules/custom/oag_opinions

Here is a simple block that loads all published content of type "page" and renders the titles. You could sort them by creation date by adding this to the `$query` variable: `->sort('created' , 'DESC');`

```PHP
namespace Drupal\opinions_module\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Annotation\Translation;

/**
 * Provides OpinionLanding Block.
 *
 * @Block(
 *   id = "opinion_landing",
 *   admin_label = @Translation("Opinion landing block"),
 *   )
 *
 * @package Drupal\oag_opinions\Plugin\Block
 */
class OpinionLanding extends BlockBase {

  public function build() {

    $entity_type = 'node';
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'page')
      ->condition('status', 1) ;
    $nids = $query->execute();
    $nodes = $storage->loadMultiple($nids);

    $render_array = [];
    foreach ($nodes as $node) {
      $render_array[] = [
        '#type' => 'markup',
        '#markup' => '<p>' . $node->getTitle(),
      ];
    }

    return $render_array;
```

## Create a Block with a corresponding config form

This example includes a block and a corresponding config form that will control what goes in the block. The block can be placed using the Block Layout system in Drupal at /admin/structure/block

![Graphical user interface Description automatically
generated](images/media/image7.png)

In `/Users/selwyn/Sites/singer-lando/docroot/modules/custom/quick_pivot/quick_pivot.routing.yml`

We have all the pieces (including some cool little API work)

So the admin piece has a form defined at
`/Users/selwyn/Sites/singer-lando/docroot/modules/custom/quick_pivot/src/Form/QuickPivotConfigForm.php`

The class which defines the config form extends ConfigFormBase because this form does all sorts of nice configuring:

`class QuickPivotConfigForm extends ConfigFormBase {`

In the class are the `getFormId()`, `getEditableConfigName()`, `buildForm()` and `submitForm()` functions. Pretty straightforward..

Then in `/Users/selwyn/Sites/singer-lando/docroot/modules/custom/quick_pivot/quick_pivot.routing.yml` we specify the route and invoke the form.

```yml
quick_pivot.config:
  path: '/admin/config/quick_pivot/settings'
  defaults:
    _form: 'Drupal\quick_pivot\Form\QuickPivotConfigForm'
    _title: 'Quick Pivot Settings'
  requirements:
    _permission: 'administer site configuration'
```

We also specify a menu item at `/Users/selwyn/Sites/singer-lando/docroot/modules/custom/quick_pivot/quick_pivot.links.menu.yml`.

```yml
quick_pivot.config:
  title: 'QuickPivot API settings'
  description: 'Configure the QuickPivot API Settings.'
  parent: system.admin_config_services
  route_name: quick_pivot.config
  weight: 1
```

Besides the quick_pivot.info.yml file, that should be all you need to make the config for the block.

Now for the block that users see (also the one that pops up in the block configuration) in `/Users/selwyn/Sites/singer-lando/docroot/modules/custom/quick_pivot/src/Plugin/Block/QuickPivotSubscribeBlock.php`

We define the block with it's annotation:

```PHP
/**
 * Provides a cart block.
 *
 * @Block(
 *   id = "quick_pivot_subscribe_block",
 *   admin_label = @Translation("QuickPivot Subscribe Block"),
 *   category = @Translation("QuickPivot Subscribe")
 * )
 */
class QuickPivotSubscribeBlock extends BlockBase implements ContainerFactoryPluginInterface {
```

It implements `ContainerFactoryPluginInterface` to allow dependency injection. This is critical for plugins or blocks. More at https://chromatichq.com/blog/dependency-injection-drupal-8-plugins. All this interface defines is the `create()` method.

Because you are using dependency injection, you have a `create()` and a `__constructor()` :

```PHP
public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
  return new static(
    $configuration,
    $plugin_id,
    $plugin_definition,
    $container->get('config.factory'),
    $container->get('form_builder')
  );
}
```


```PHP
public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, FormBuilderInterface $form_builder) {
  parent::__construct($configuration, $plugin_id, $plugin_definition);

  $this->configFactory = $config_factory;
  $this->formBuilder = $form_builder;
}
```

and finally the `build()` method:

```PHP
public function build() {
  return $this->formBuilder->getForm('Drupal\quick_pivot\Form\QuickPivotSubscribeForm');
}
```

## Modify a block with hook_block_view_alter or hook_block_build_alter

If you need to modify a block, you can use `hook_block_view_alter` or
`hook_block_build_alter` although I haven't been able to make this work... hmm.

There is a comment that may be worth exploring at https://api.drupal.org/api/drupal/core%21modules%21block%21block.api.php/function/hook_block_view_alter/8.2.x.

To alter the block content you must add a `#pre_render` in this hook, `hook_block_view_alter`.

From <https://drupal.stackexchange.com/a/215948> there is an example which fills in the `$build['#pre_render'][]` array with a string. 

In the later example, a function is provided

```PHP
function yourmodule_block_view_alter(array &$build, \Drupal\Core\Block\BlockPluginInterface $block) {
  if ($block->getBaseId() === 'system_powered_by_block') {
    $build['#pre_render'][] = '_yourmodule_block_poweredby_prerender';
  }
```

I think this is the version I tried

```PHP
/**
 * Implements hook_block_build_alter().
 */
function pega_academy_core_block_build_alter(array &$build, \Drupal\Core\Block\BlockPluginInterface $block) {
  if ($block->getPluginId() == 'system_menu_block:account') {
    $build['#cache']['contexts'][] = 'url';
  }
//  else if ($block->getBaseId() === 'block_content') {
//    if ($block->label() === "Home Page Alert") {
//      $build['content'] = '<p>New content built here!</p>';
//
//    }
//  }
}
```

And I discovered an example from a project where the
`$build['#pre_render'][]` array is populated with a function. I'm
not sure what that function did -- presumably returned some text to be rendered.

```PHP
/**
 * Implements hook_block_view_alter().
 */
function pega_academy_core_block_view_alter(array &$build, \Drupal\Core\Block\BlockPluginInterface $block) {
  if ($block->getBaseId() === 'block_content') {
    if ($block->label() === "Home Page Alert") {
      $build['#pre_render'][] = 'Drupal\pega_academy_core\Controller\DashboardController::home_page_alert_prerender';
//      $build['content'] = '<p>New content built here!</p>';

    }
  }
}
```

## Disable caching in a block

From `/Users/selwyn/Sites/singer-lando/docroot/modules/custom/websphere_commerce/modules/cart/src/Plugin/Block/CartSummary.php`

```PHP
/**
 * {@inheritdoc}
 */
public function getCacheMaxAge() {
  return 0;
}
```

## Add a configuration form to your block

Making a block configurable means it has a form where you can specify its settings e.g. for menu block you specify menu levels. Ignore this if your block does not need any configuration.

To make your block configurable, override 3 methods from BlockBase.

1.  defaultConfiguration

2.  blockForm

3.  blockSubmit

Here `defaultConfiguration()` returns a block_count of 5.

```PHP
/**
 * {@inheritdoc}
 */
public function defaultConfiguration() {
  // By default, the block will display 5 thumbnails.
  return array(
    'block_count' => 5,
  );
}
```

`blockForm()` is used to create a configuration form

```PHP
/**
 * {@inheritdoc}
 */
public function blockForm($form, FormStateInterface $form_state) {
  $range = range(2, 20);
  $form['block_count'] = array(
    '#type' => 'select',
    '#title' => $this->t('Number of product images in block'),
    '#default_value' => $this->configuration['block_count'],
    '#options' => array_combine($range, $range),
  );
  return $form;
}

```

And `blockSubmit()` handles the submission of the config form. You don't need to save anything. This is handled for you. You just specify a configuration key like `$this->configuration['block_count']` and the rest is handled for you.

```PHP
/**
 * {@inheritdoc}
 */
public function blockSubmit($form, FormStateInterface $form_state) {
  $this->configuration['block_count'] = $form_state->getValue('block_count');
}
```

The `build()` method does all the work of building a render array to display your block.

In this case, it uses the context annotation to get a node (From
dev1/iai_pig module -- see `source/Plugin/Block/ImageGalleryBlock.php`)

```php
/**
 * {@inheritdoc}
 */
public function build() {
  $build = array();
  $node = $this->getContextValue('node');

  // Determine if we are on a page that points to a product.
  $product = $this->getProduct($node);
  if ($product) {

    // Retrieve the product images
    $image_data = $this->productManagerService->retrieveProductImages($product);
    $block_count = $this->configuration['block_count'];
    $item_count = 0;
    $build['list'] = [
      '#theme' => 'item_list',
      '#items' => [],
    ];

    $build['list']['#items'][0] = [
      '#type' => 'markup',
      '#markup' => $this->t('There were no product images to display.')
    ];

    while ($item_count < $block_count && isset($image_data[$item_count])) {
      $file = File::load($image_data[$item_count]['target_id']);
      $link_text = [
        '#theme' => 'image_style',
        '#uri' => $file->getFileUri(),
        '#style_name' => 'product_thumbnail',
        '#alt' => $image_data[$item_count]['alt'],
      ];

      /***********************************************************************
**                                                                          
** This is the Modal API.                                                   **
** @see: https://www.drupal.org/node/2488192 for more information.          **
**                                                                          **
***********************************************************************/
      $options = array(
        'attributes' => array(
          'class' => array(
            'use-ajax',
          ),
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 700,
          ]),
        ),
      );
      $url = Url::fromRoute('iai_pig.display_product_image', array('node' => $product->nid->value, 'delta' => $item_count));
      $url->setOptions($options);
      $build['list']['#items'][$item_count] = [
        '#type' => 'markup',
        '#markup' => Link::fromTextAndUrl(drupal_render($link_text), $url)
          ->toString(),
      ];
      $item_count++;
    }
    $build['#attached']['library'][] = 'core/drupal.dialog.ajax';
  }
  else {

    /******************************************************************************
     **                                                                          **
     ** This logic is just to give some positive feedback that the block is being**
     ** rendered. In reality, we'd likely just not have the block render anything**
     ** in this situation.                                                       **
     **                                                                          **
     ******************************************************************************/
    $build['no_data'] = [
      '#type' => 'markup',
      '#markup' => $this->t('This page does not reference a product.'),
    ];
  }

  return $build;
}
```

One last item. Configuration expects a schema for things being saved.
Here we create a iai_aquifer.schema.yml in config/schema and it looks
like:

```yml
# Schema for the configuration files of the IAI aquifer module.

block.settings.aquifer_block:
  type: block_settings
  label: 'Aquifer block'
  mapping:
    block_count:
      type: integer
      label: 'Block count'
```

## Block display not updating after changing block content

From
<https://www.youtube.com/watch?v=QCZe2K13bd0&list=PLgfWMnl57dv5KmHaK4AngrQAryjO_ylaM&t=0s&index=16>
Nedcamp video on caching

In a twig template, let's you say you just render one field (and don't
render others), Drupal won't know the content has been updated and will
sometimes show the old cached content. You can define a view mode or
tweak the twig template a smidge with something like this:


  \{\% set blah = content\|render \%\}


Then add your fields:

```
  {content.field_one}  etc.
```

Not sure why but...

## Block Permission (blockAccess)

This code is taken from the user_login_block (UserLoginBlock.php.) It
allows access to the block if the user is logged out and is not on the
login or logout page. The access is cached based on the current route
name and the user's current role being anonymous. If these are not
passed, the access returned is forbidden and the block is not built.

Don't forget:

```php
use Drupal\Core\Access\AccessResult;
```

and `$account` comes from

```php
$account = \Drupal::currentUser();
```

```PHP
/**
 * {@inheritdoc}
 */
protected function blockAccess(AccountInterface $account) {
  $route_name = $this->routeMatch->getRouteName();
  if ($account->isAnonymous() && !in_array($route_name, ['user.login', 'user.logout'])) {
    return AccessResult::allowed()
      ->addCacheContexts(['route.name', 'user.roles:anonymous']);
  }
  return AccessResult::forbidden();
}
```

And from the Copyright.php file some piddlings:

`$account` comes from

```php
$account = \\Drupal::currentUser();
```

```PHP
//Get the route.
$route_name = \Drupal::routeMatch()->getRouteName();

// not on the user login and logout pages
if (!in_array($route_name,array('user.login', 'user.logout'))) {
  return AccessResult::allowed();
}

//Auth user
if ($account->isAuthenticated()) {
  return AccessResult::allowed();
}
//Anon.
if ($account->isAnonymous()) {
  return AccessResult::forbidden();
}
```

From
`/Users/selwyn/Sites/dev1/web/modules/custom/rsvp/src/Plugin/Block/RSVPBlock.php`

Here we check to make sure the user is on a node and that they have
`view rsvplist` permission.

```PHP
protected function blockAccess(AccountInterface $account) {
  /** @var \Drupal\node\Entity\Node $node */
  $node = \Drupal::routeMatch()->getParameter('node');
  if ($node) {
    $nid = $node->id();
    if (is_numeric($nid)) {
      // See rsvp.permissions.yml for the permission string.
      return AccessResult::allowedIfHasPermission($account, 'view rsvplist');
    }
  }
  return AccessResult::forbidden();
}
```

some options:

```php
return AccessResult::forbidden();
```

```php
return AccessResult::allowed();
```

```php
return AccessResult::allowedIf(TRUE);
```

[home](../index.html)

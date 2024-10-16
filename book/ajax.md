---
title: AJAX
---

# AJAX
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=ajax.md)

## Overview

AJAX is a way to update parts of a page without refreshing the entire page.  This is done by sending an HTTP request to the server and getting a response back.


## Update content using a custom AJAX link in a block
To render a block with an AJAX link that updates the content of the block, follow these steps:

In `web/modules/custom/block_play/block_play.info.yml` add the following:

```yaml
name: 'Block Play'
type: module
description: 'Playing with Blocks'
package: Custom
core_version_requirement: ^10 || ^11
```

In `web/modules/custom/block_play/src/Plugin/Block/AjaxLinkBlockBlock.php` add the following:

```php
<?php

declare(strict_types=1);

namespace Drupal\block_play\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

/**
 * Provides an ajax link block block.
 *
 * @Block(
 *   id = "block_play_ajax_link_block",
 *   admin_label = @Translation("Ajax Link Block"),
 *   category = @Translation("Custom"),
 * )
 */
final class AjaxLinkBlockBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build['content'] = [
      '#markup' => $this->t('Here is the block showing up!'),
      '#prefix' => '<div class="my-menu-list">',
      '#suffix' => '</div>',
    ];

    $url = Url::fromRoute('block_play.hide_block');
    $url->setOption('attributes', ['class' => ['use-ajax']]);
    $build['link'] = [
      '#type' => 'link',
      '#url' => $url,
      '#title' => 'Click my special link to make something happen',
    ];

    return $build;
  }

}
```
Then in a controller at `web/modules/custom/block_play/src/Controller/BlockPlayController.php` add the following:

```php
<?php

declare(strict_types=1);

namespace Drupal\block_play\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Returns responses for Block Play routes.
 */
final class BlockPlayController extends ControllerBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  public function __construct(
    AccountInterface $current_user,
  ) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('current_user'),
    );
  }

  /**
   * Hides the block or updates the content in the block.
   */
  public function changeBlock(Request $request) {
    if (!$request->isXmlHttpRequest()) {
      throw new HttpException(400, 'This is not an AJAX request.');
    }
    $user_name = $this->currentUser->getDisplayName();
    $response = new AjaxResponse();
    // $command = new RemoveCommand('#block-olivero-ajaxlinkblock');
    // $command = new AppendCommand('#block-olivero-ajaxlinkblock', 'Block hidden - or is it?');
    // $response->addCommand(new AppendCommand('#ajax-example-destination-div', 'Block hidden'));
    $command = new ReplaceCommand('#block-olivero-ajaxlinkblock', '<div>The block has totally new content now!!!' . $user_name . ' </div>');
    $response->addCommand($command);
    return $response;
  }

}
```
Add the routing file at `web/modules/custom/block_play/block_play.routing.yml`:

```yaml
block_play.hide_block:
  path: '/hide-block'
  defaults:
    _controller: '\Drupal\block_play\Controller\BlockPlayController::changeBlock'
  requirements:
    _permission: 'access content'
```


To see this work, place the block on a page, view the page, and click the link.  The block will be replaced with the text "The block has totally new content now!!!".  Try commenting out the `ReplaceCommand` and uncommenting the `AppendCommand` to see the difference.


## Table with a pager using AJAX

To display a table with a pager that updates via AJAX requires some fidding. The code is in a module called `ajax_pager_table`. The idea is that when the user clicks a pager link, the table is updated with the new page of data and the pager updates correctly. To make this a little more interesting, the table is displayed via a block. Also this is not a form which makes this a little more interesting.

For reference, check out the [class Pager in the Drupal API](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Element%21Pager.php/class/Pager/10)

### The block
Starting with the block, in the `web/modules/custom/ajax_pager_table/src/Plugin/Block/AjaxPagerTableBlock.php``` file the following code is added:
  
```php
<?php
namespace Drupal\ajax_pager_table\Plugin\Block;

use Drupal\ajax_pager_table\Service\TableContentService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an AJAX Pager Table Block.
 *
 * @Block(
 *   id = "ajax_pager_table_block",
 *   admin_label = @Translation("AJAX Pager Table Block"),
 *   category = @Translation("DOH"),
 * )
 */
class AjaxPagerTableBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The table content service.
   *
   * @var \Drupal\ajax_pager_table\Service\TableContentService
   */
  protected $tableContentService;

  /**
   * Constructs a new AjaxPagerTableBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param $plugin_id
   *   The plugin ID for the plugin instance.
   * @param $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ajax_pager_table\Service\TableContentService $tableContentService
   *   The table content service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MessengerInterface $messenger,
    TableContentService $tableContentService,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->tableContentService = $tableContentService;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('messenger'),
      $container->get('ajax_pager_table.table_content_service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $page = \Drupal::request()->query->get('page') ?? 0;
    $build = $this->tableContentService->getTableContent($page, TRUE);
    return $build;
  }

}
```

### The custom service that draws the table and the pager

The block calls the `tableContentService` to get the table content and the pager.  That service is in the `web/modules/custom/ajax_pager_table/src/Service/TableContentService.php` file:

```php
<?php

namespace Drupal\ajax_pager_table\Service;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Service for fetching table content.
 */
class TableContentService {

  public function __construct(
    private readonly MessengerInterface $messenger,
    private readonly AccountProxyInterface $currentUser,
    private readonly PagerManagerInterface $pagerManager,
  ) {}

  /**
   * Generates the table content.
   *
   * @param int $page
   *   The current page number.
   *
   * @return array
   *   The render array of the table.
   */
  public function getTableContent(int $page): array {
    $items_per_page = 10;
    $total_items = 105;

    $pager = $this->pagerManager->getPager(0);
    if (is_null($pager)) {
      $pager = $this->pagerManager->createPager($total_items, $items_per_page, 0);
    }
    $current_page = $pager->getCurrentPage();
    $current_page = $page;
    $start = $current_page * $items_per_page;
    $end = min($start + $items_per_page, $total_items);

    $header = [
      ['data' => t('Item Number')],
    ];

    $rows = [];
    for ($i = $start; $i < $end; $i++) {
      $rows[] = ['data' => ['Item ' . $i]];
    }

    $build['table_content']['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#prefix' => '<div id="table-wrapper">',
      '#suffix' => '</div>',
    ];
    //if ($retrieve_pager) {
      if ($total_items > 1) {
        $build['table_content']['pager'] = [
          '#type' => 'pager',
          '#element' => 0,
          '#route_name' => 'ajax_pager.refresh_table',
          //'#parameters' => ['page' => $current_page],
          '#prefix' => '<div id="pager-wrapper">',
          '#suffix' => '</div>',
        ];
      }
    //}
    // add wrapper around whole thing
    $build['table_content']['#prefix'] = '<div id="ajax-pager-table-wrapper">';
    $build['table_content']['#suffix'] = '</div>';
    return $build;
  }

}
```

::: tip Note
The most important part in the pager definition array is this code which provide the route for the AJAX callback and the parameters to pass to it:
```php
  '#route_name' => 'ajax_pager.refresh_table',
  '#parameters' => ['page' => $current_page],
```
:::


The required service definition along with its arguments is in the `web/modules/custom/ajax_pager_table/ajax_pager_table.services.yml` file:

```yaml
services:
  ajax_pager_table.table_content_service:
    class: Drupal\ajax_pager_table\Service\TableContentService
    arguments: ['@messenger', '@current_user', '@pager.manager']
```

### The hook_preprocess_pager function

In order to get the pager links to all respond to AJAX, each link in the pager needs to be updated.  This is done in the `web/modules/custom/ajax_pager_table/ajax_pager_table.module` file using a `hook_preprocess_pager`:

```php
<?php

/**
 * Add use-ajax class to each link in the pager.
 *
 * Implements hook_preprocess_pager().
 */
function ajax_pager_table_preprocess_pager(&$variables): void {
    // Make sure we only add the class to our pager.
  if ($variables['pager']['#route_name'] !== 'ajax_pager.refresh_table') {
    return;
  }
  if (isset($variables['items'])) {
    foreach ($variables['items']['pages'] as &$page) {
      $page['attributes']->addClass('use-ajax');
      }
  }
  if (isset($variables["items"]["next"])) {
    $variables["items"]["next"]["attributes"]->addClass("use-ajax");
  }
  if (isset($variables["items"]["previous"])) {
    $variables["items"]["previous"]["attributes"]->addClass("use-ajax");
  }
  if (isset($variables["items"]["first"])) {
    $variables["items"]["first"]["attributes"]->addClass("use-ajax");
  }
  if (isset($variables["items"]["last"])) {
    $variables["items"]["last"]["attributes"]->addClass("use-ajax");
  }
}
```

This actually causes the links in the pager to no longer point to `?page=2` for example, but rather will point to `https://ddev102.ddev.site/refresh-selwyn-wrapper?_wrapper_format=drupal_ajax&page=2`. 

### The controller with the AJAX callback

Next is the controller that handles the callback for the AJAX request.  This is in the `web/modules/custom/ajax_pager_table/src/Controller/AjaxPagerTableController.php` file. The callback is `refreshAjaxBlock` and it returns an `AjaxResponse` object with a `ReplaceCommand` that updates the table content.  The controller also checks to make sure the request is an AJAX request:

```php
<?php

namespace Drupal\ajax_pager_table\Controller;

use Drupal\ajax_pager_table\Service\TableContentService;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\BeforeCommand;
use Drupal\Core\Ajax\RemoveCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Controller for the AJAX pager table.
 */
class AjaxPagerTableController extends ControllerBase {

  /**
   * The table content service.
   *
   * @var \Drupal\ajax_pager_table\Service\TableContentService
   */
  protected $tableContentService;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Request Stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  public function __construct(
    MessengerInterface $messenger,
    TableContentService $tableContentService,
    AccountInterface $current_user,
    RequestStack $requestStack,
  ) {
    $this->tableContentService = $tableContentService;
    $this->messenger = $messenger;
    $this->currentUser = $current_user;
    $this->requestStack = $requestStack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('messenger'),
      $container->get('ajax_pager_table.table_content_service'),
      $container->get('current_user'),
      $container->get('request_stack'),
    );
  }

  /**
   * Refreshes the table content via AJAX.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   The AJAX response.
   */
  //public function refreshAjaxBlock(int $page = 0) {
  public function refreshAjaxBlock() {
    //$request = \Drupal::request();
    $request = $this->requestStack->getCurrentRequest();
    if (is_null($request) || !$request->isXmlHttpRequest()) {
      throw new HttpException(400, 'This is not an AJAX request.');
    }
    $page_number = (int) $request->query->get('page');
    $response = new AjaxResponse();
    $command = new ReplaceCommand('#ajax-pager-table-wrapper', $this->tableContentService->getTableContent($page_number));
    // Alternatively, you can replace individual wrappers.
    // $command = new ReplaceCommand('#table-wrapper', $this->tableContentService->getTableContent($page_number));
    $response->addCommand($command);

    return $response;
  }

}
```

### The routing file for the callback

And finally, we need a route to handle the AJAX request.  This is in the `web/modules/custom/ajax_pager_table/ajax_pager_table.routing.yml` file:

```yaml
ajax_pager.refresh_table:
  path: '/refresh-selwyn-wrapper'
  defaults:
    _controller: '\Drupal\ajax_pager_table\Controller\AjaxPagerTableController::refreshAjaxBlock'
  requirements:
    _permission: 'access content'
```



## Finding the AJAX commands to use with addCommand()

Look in `docroot/core/lib/Drupal/Core/Ajax` for a list of files. Each file is a class that implements a command e.g. `AddCssCommand`, `RedirectCommand` or `OpenModalDialogCommand`. Also check out the [Core AJAX Callback Commands on drupal.org - updated May 2024](https://www.drupal.org/docs/develop/drupal-apis/ajax-api/core-ajax-callback-commands)


## Copy to Clipboard from an AJAX modal dialog

In the [AI Content Creator module](https://www.drupal.org/project/ai_content_creator) there is a feature that allows the user to copy the generated content to the clipboard. Unfortunately you have to tweak this module slightly to make it actually work but the code is useful.  My `tweaks` to make the module work are in the code below if you want to make it work.

The module alters the node add/edit form using a `hook_form_alter()` which adds some elements to the form as well as attached the `clipboard.js` library:

```php
/**
 * Implements hook_form_alter() for node form alter.
 */
function ai_content_creator_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  $form_object = $form_state->getFormObject();
  $isNodeForm = $form_object instanceof NodeForm;
  if ($isNodeForm) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $form_state->getFormObject()->getEntity();
    $types_enabled = \Drupal::config('ai_content_creator.adminsettings')->get('api_node_type');
    if (in_array($node->bundle(), $types_enabled)) {
      $form_state->setRebuild(TRUE);
      $form['ai_content_creator'] = [
        '#type' => 'details',
        '#title' => t('AI Content Generator'),
        '#group' => 'advanced',
        '#open' => TRUE,
        '#weight' => 1000,
      ];
      // This is the prompt.
      $form['ai_content_creator']['keywords'] = [
        '#type' => 'textarea',
        '#title' => t('Keywords'),
        '#attributes' => ['placeholder' => t('Write me a post that ...')],
        '#description' => t('Provide a context of what the AI will generate with keywords as well as number of words if needed.'),
      ];
      $form['ai_content_creator']['generate_content'] = [
        '#type' => 'button',
        '#value' => t('Generate content'),
        '#attributes' => ['class' => ['button--primary']],
        '#attached' => [
          'library' => [
            'core/drupal.dialog.ajax',
            'ai_content_creator/clipboardjs',
          ],
        ],
        '#ajax' => [
          'callback' => 'ai_content_creator_generate_content_callback',
          'event' => 'click',
          'progress' => [
            'type' => 'throbber',
            'message' => t('Verifying entry...'),
          ],
        ],
      ];
    }
  }
}
```


The `ai_content_creator.libraries.yml` file specifies the `clipboard.js` from cloudflare:

```yaml
clipboardjs:
  remote: https://github.com/zenorocha/clipboard.js/
  version: "2.0.10"
  license:
    name: MIT
    url: https://zenorocha.mit-license.org/
    gpl-compatible: true
  js:
    https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js: { type: external, minified: true }
```


Here is the callback that is called when you click the `Generate content` button.  The content is generated by calling the OpenAI API.  The generated content is then displayed in a modal dialog.  The `Copy to clipboard` button is added to the modal dialog.

Notice in the `$dialog_options` the code below how the `onclick` event is set to copy the content to the clipboard and then close the modal dialog:
```php
  'onclick' => "new ClipboardJS('.button--clipboard'); jQuery('.ui-icon-closethick').click()",
```



```php
/**
 * Ajax callback function to call the OpenAI API.
 *
 * @param array $form
 *   An associative array containing the structure of the forms.
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 *   The current state of the form.
 */
function ai_content_creator_generate_content_callback(array $form, FormStateInterface $form_state) {
  $values = $form_state->getUserInput();
  $keywords = trim(preg_replace('/\s\s+/', ' ', $values['keywords']));
  $ajax_response = new AjaxResponse();
  $access_token = \Drupal::config('ai_content_creator.adminsettings')->get('api_key');
  $url = \Drupal::config('ai_content_creator.adminsettings')->get('api_url');
  $max_token = \Drupal::config('ai_content_creator.adminsettings')->get('api_max_token');

  $payload = [
//    "model" => "text-davinci-003",
    "model" => "gpt-3.5-turbo-0125",
//    "prompt" => $keywords,
    "messages" => [
      [
        "role" => "user",
        "content" => $keywords,
      ],
    ],
    "temperature" => 0.9,
    "max_tokens" => (int) $max_token,
  ];

  $header = [
    'Content-Type' => 'application/json',
    'Authorization' => 'Bearer ' . $access_token,
  ];
  $options = [
    'headers' => $header,
    'json' => $payload,
  ];
  $dialog_options = [
    'width' => '80%',
    'resizable' => TRUE,
    'buttons' => [
      'close' => [
        'text' => t('Close'),
        'id' => 'close-button',
        'onclick' => "jQuery('.ui-icon-closethick').click()",
      ],
      'clipboard' => [
        'text' => t('Copy to clipboard'),
        'class' => 'button--primary button--clipboard',
        'onclick' => "new ClipboardJS('.button--clipboard'); jQuery('.ui-icon-closethick').click()",
      ],
    ],
  ];
  $client = new Client();
  $data = [];
  try {
    $response = $client->request('POST', $url, $options);
    $result = $response->getBody()->getContents();
    $data = Json::decode($result);
  }
  catch (RequestException $exception) {
    // Error handling for OpenAI API callbacks.
    $error_msg = $exception->getMessage();
    $ajax_response->addCommand(new AlertCommand($error_msg));
    return $ajax_response;
  }
  //$text = (string) $data['choices'][0]['text'];
  $text = (string) $data['choices'][0]['message']['content'];
  $dialog_options['buttons']['clipboard']['data-clipboard-text'] = $text;
  // Open the modal dialog with the generated content.
  $ajax_response->addCommand(new OpenModalDialogCommand(t('AI generated content.'), nl2br($text), $dialog_options));
  return $ajax_response;
}
```

The [full source for the module is available here](https://git.drupalcode.org/project/ai_content_creator).




## The Basics of the AJAX framework

If you prefer watching a video presentation on this, check out Michael Miles [Drupal 8 Day: Demystifying AJAX Callback Commands in Drupal 8](https://www.youtube.com/watch?v=6YhJq01jlpY). This session outlines and explains Drupal 8 AJAX callback commands and how to use them. AJAX callback commands are the sets of PHP and JavaScript functions that control all AJAX functionality on a Drupal site.  The slides are available [on slideshare](https://www.slideshare.net/slideshow/drupal8day-demystifying-drupal-8-ajax-callback-commands/69024610) His [Ajax Dblog project can be viewed here ](https://www.drupal.org/project/ajax_dblog)

### Callback Commands
Callback commands have two parts, a JavaScript function and a PHP class. The JavaScript function is called when the AJAX request is successful. The PHP class is used to define the JavaScript function to call.  Core, contrib and custom modules can define their own callback commands. See views, ctools etc. for examples.

Drupal core provides a number of callback commands (\~40) which are basically wrappers to jQuery functions. e.g. `insert`, `remove`, `slideDown`. See [jQuery API docs for a complete list of jQuery functions](https://api.jquery.com/).

#### JavaScript side of the callback command
The AJAX framework provides a global JS object with functions attached. These functions are the JavaScript part of the callback commands. The Global JS object is `Drupal.AjaxCommands.prototype` and is defined in `misc/ajax.js`. All callback commands are attached to this object

Every function that is a callback accepts 3 args:
- ajax: information about the ajax request - the element that triggered it, the endpoint that is being requested, the element that is marked to be altered etc.
- Response: Contains all the data that has been sent back by the server to this function - i.e. data to be placed on the page e.g. html markup, elements to select or to trigger, any other data that the JS function is expected to be acted on
- status: The status code of the request - hopefully a 200, but could be a 500/504 etc.

These can be any JavaScript that you want and is location in the `js` directory of a module.
```JS
(function ($, window, Drupal, drupalSettings) {
  'use strict';
  Drupal.AjaxCommands.prototype.myCommand = function (ajax, response, status) {
    // Custom JS code here.

    // Do something with the response.

    console.log(response.data);
  };
})(jQuery, this, Drupal, drupalSettings);
```


### PHP side of the callback command

The PHP class implements the `CommandInterface` interface. This interface has a single method `render()` that returns an associative array with at least an element with a key of `command`. The value of `command` is the name of the JavaScript function to call. The array can also have other data passed which becomes the response data.

The PHP class is in a path like `module/src/Ajax/[CommandName]Command.php`


```php
<?php
namespace Drupal\MyModule\Ajax;

use Drupal\Core\Ajax\CommandInterface;

// Ajax command called as a Javascript method in the form MyCommand().
class MyCommand implements CommandInterface {

  // Implements Drupal\Core\Ajax\CommandInterface::render().
  public function render() {
    return [
      'command' => 'myCommand', // The JS function to call
      'data' => 'some data', // Other response arguments.
    ];
  }
}
```

#### Core example: RemoveCommand

Here is the RemoveCommand.php file from core.  It is at `web/core/lib/Drupal/Core/Ajax/RemoveCommand.php`:
```php
<?php

namespace Drupal\Core\Ajax;

/**
 * AJAX command for calling the jQuery remove() method.
 *
 * The 'remove' command instructs the client to use jQuery's remove() method
 * to remove each of elements matched by the given selector, and everything
 * within them.
 *
 * This command is implemented by Drupal.AjaxCommands.prototype.remove()
 * defined in misc/ajax.js.
 *
 * @see http://docs.jquery.com/Manipulation/remove#expr
 *
 * @ingroup ajax
 */
class RemoveCommand implements CommandInterface {

  /**
   * The CSS selector for the element(s) to be removed.
   *
   * @var string
   */
  protected $selector;

  /**
   * Constructs a RemoveCommand object.
   *
   * @param string $selector
   *   The selector.
   */
  public function __construct($selector) {
    $this->selector = $selector;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   */
  public function render() {
    return [
      'command' => 'remove',
      'selector' => $this->selector,
    ];
  }

}
```

Here is the JS function that is called is in `web/core/misc/ajax.js`. Notice that it uses data from the `response` to target elements on the page and remove them. Also it removes any behaviors that are attached to the elements that are about to be removed.:

```javascript
    /**
     * Command to remove a chunk from the page.
     *
     * @param {Drupal.Ajax} [ajax]
     *   {@link Drupal.Ajax} object created by {@link Drupal.ajax}.
     * @param {object} response
     *   The response from the Ajax request.
     * @param {string} response.selector
     *   A jQuery selector string.
     * @param {object} [response.settings]
     *   An optional array of settings that will be used.
     * @param {number} [status]
     *   The XMLHttpRequest status.
     */
    remove(ajax, response, status) {
      const settings = response.settings || ajax.settings || drupalSettings;
      $(response.selector)
        .each(function () {
          Drupal.detachBehaviors(this, settings);
        })
        .remove();
    },
```


### Using Ajax callback commands
1. Include the Ajax framework and commands onto the page
2. Return an AjaxResponse object
3. Attach commands with `addCommand()` method



## Resources
- [Drupal AJAX API](https://api.drupal.org/api/drupal/core%21core.api.php/group/ajax/10)
- [Change records for Drupal core - jQuery](https://www.drupal.org/list-changes/drupal/published?keywords_description=jquery&to_branch=&version=&created_op=%3E%3D&created%5Bvalue%5D=&created%5Bmin%5D=&created%5Bmax%5D=)
- [Drupal API Form Element Reference with examples](https://api.drupal.org/api/drupal/elements/10)
- [Drupal AJAX AJAX forms updated Dec 2022](https://www.drupal.org/docs/8/api/javascript-api/ajax-forms)
- [Drupal AJAX Dialog boxes updated Nov 2022](https://www.drupal.org/docs/drupal-apis/ajax-api/ajax-dialog-boxes)

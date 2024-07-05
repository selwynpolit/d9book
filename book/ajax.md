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


## Resources
- [Drupal API Form Element Reference with examples](https://api.drupal.org/api/drupal/elements/10)
- [Drupal AJAX AJAX forms updated Dec 2022](https://www.drupal.org/docs/8/api/javascript-api/ajax-forms)
- [Drupal AJAX Dialog boxes updated Nov 2022](https://www.drupal.org/docs/drupal-apis/ajax-api/ajax-dialog-boxes)

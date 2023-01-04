<?php

namespace Drupal\di_examples\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for DI Examples routes.
 */
class DiExamplesController extends ControllerBase {

  protected AccountProxyInterface $account;
  protected CurrentPathStack $pathStack;
  protected PathValidatorInterface $pathValidator;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('path.current'),
      $container->get('path.validator'),
    );
  }

  public function __construct(AccountProxyInterface $account, CurrentPathStack $path_stack, PathValidatorInterface $path_validator) {
    $this->account = $account;
    $this->pathStack = $path_stack;
    $this->pathValidator = $path_validator;
  }


  /**
   * Builds the response.
   */
  public function build() {

    // Use the injected account.
    $account = $this->account->getAccount();

    // Use the ControllerBase static version.
    $account = $this->currentUser();

    $username = $account->getAccountName();
    $uid = $account->id();

    $message = "<br>Account info user id: " . $uid . " username: " . $username;

    $name = 'hello';

    // Use the ControllerBase static version to create an entityQuery.
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery();
    $query
      ->condition('type', 'article')
      ->condition('title', $name)
      ->count();
    $count_nodes = $query->execute();
    $message .= "<br>Retrieved " . $count_nodes . " nodes";

    $path = $this->pathStack->getPath();
    $message .= "<br> Path: " . $path;

    $test_path = "/vote1";
    $valid_path = $this->pathValidator->isValid($test_path);
    $message .= "<br> Check for valid path: " . $test_path . " returned: " . $valid_path;


    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t($message),
    ];

    return $build;
  }

}

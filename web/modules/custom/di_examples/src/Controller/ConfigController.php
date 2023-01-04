<?php

namespace Drupal\di_examples\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for DI Examples routes.
 */
class ConfigController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    /*
     * Retrieves the configuration factory.
     *
     * This is mostly used to change the override settings on the configuration
     * factory. For example, changing the language, or turning all overrides on
     * or off.
     */
    $mail_config = \Drupal::configFactory()->getEditable('system.mail');
    // returns 'php_mail'
    $mail_plugins = $mail_config->get('interface');

    $maintenance_message = \Drupal::configFactory()->getEditable('system.maintenance')->get('message');

    $to = \Drupal::configFactory()->getEditable('system.site')->get('mail');

//    $other_config = \Drupal::config('system.email')->get('interface');
//    $message = \Drupal::config('system.maintenance')->get('message');

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}

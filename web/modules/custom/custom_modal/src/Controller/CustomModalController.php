<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 3/26/18
 * Time: 4:52 PM
 */

/**
 * @file
 *
 * Contains \Drupal\custom_modal\Controller\CustomModalController
 *
 * From example at http://befused.com/drupal/modal-module.
 */
namespace Drupal\custom_modal\Controller;


use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;

class CustomModalController extends ControllerBase {

  public function modal() {
    $options = [
      'dialogClass' => 'popup-dialog-class',
      'width' => '25%',
    ];
    $response = new AjaxResponse();
    $response->addCommand(new OpenModalDialogCommand(t('Modal title'), t('The modal text'), $options));

    return $response;
  }

}
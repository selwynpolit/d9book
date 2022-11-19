<?php
/**
 * @file
 * Contains \Drupal\custom_modal\Plug\Block\ModalBlock.
 *
 * From example at http://befused.com/drupal/modal-module.
 */

namespace Drupal\custom_modal\Plugin\Block;


use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Component\Serialization\Json;


/**
 * Provides a 'Modal' Block
 *
 * @Block(
 *   id = "modal_block",
 *   admin_label = @Translation("Modal block"),
 * )
 */
class ModalBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $link_url = Url::fromRoute('custom_modal.modal');
    $link_url->setOptions([
      'attributes' => [
        'class' => ['use-ajax', 'button', 'button--small'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(['width' => 400]),
      ]
    ]);

//    return [
//      '#type' => 'markup',
//      '#markup' => Link::fromTextAndUrl(t('Open modal'), $link_url)->toString(),
//      '#attached' => ['library' => ['core/drupal.dialog.ajax']]
//    ];


    // Just the button
    if (FALSE) {
      $rArray =  [
        '#type' => 'markup',
        '#markup' => Link::fromTextAndUrl(t('Open modal'), $link_url)->toString(),
        '#attached' => ['library' => ['core/drupal.dialog.ajax']]
      ];
    }


    // two paragraphs and the button.
    $rArray = [
      'first_para' => [
        '#type' => 'markup',
        '#markup' => '...para 1 here....<br>',
      ],
      'second_para' => [
        '#type' => 'markup',
        '#markup' => '...para 2 here....<br>',
      ],
      'the_button' => [
        '#type' => 'markup',
        '#markup' => Link::fromTextAndUrl(t('Open modal'), $link_url)->toString(), '#attached' => ['library' => ['core/drupal.dialog.ajax']]
      ],
    ];

    if (FALSE) {
      $rArray['first_para'] = [
        '#type' => 'markup',
        '#markup' => '...para 1 here....<br>',
      ];

      $rArray['second_para'] = [
        '#type' => 'markup',
        '#markup' => '...para 2 here....<br>',    ];

      $rArray['butthead'] = [
        '#type' => 'markup',
        '#markup' => Link::fromTextAndUrl(t('Open modal'), $link_url)->toString(), '#attached' => ['library' => ['core/drupal.dialog.ajax']]
      ];
    }



    return $rArray;
  }
}
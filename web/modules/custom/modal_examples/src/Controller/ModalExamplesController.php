<?php

namespace Drupal\modal_examples\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Returns responses for Modal Examples routes.
 */
class ModalExamplesController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];
    $build['link1-to-modal1'] = [
      '#type'       => 'link',
      '#title'      => t('Link to modal1'),
      '#url'        => Url::fromRoute('modal_examples.modal1', [
        'program_id'     => 123,
        'type' => 'all',
      ]),
      '#attributes' => [
        'id' => 'view-correlation-' . 12345,
        'class' => ['use-ajax'],
        'aria-label' => 'View useful information pertaining to item ' . '12345',
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(
          [
            'width' => 'auto',
          ]
        ),
      ],
    ];

    return $build;
  }

  public function buildModal1(int $program_id, string $type) {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Some useful information!'),
    ];
    $build['other_content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Program id: @program_id. Type: @type', [
        '@program_id' => $program_id,
        '@type' => $type,
      ]),
    ];

    return $build;
  }

}

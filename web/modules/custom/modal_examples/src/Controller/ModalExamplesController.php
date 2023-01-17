<?php

namespace Drupal\modal_examples\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Returns responses for Modal Examples routes.
 */
class ModalExamplesController extends ControllerBase {

  public function buildExample1() {

    $route_name = \Drupal::routeMatch()->getRouteName();
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Route: %route', ['%route' => $route_name]),
    ];


    $build['link-to-modal1'] = [
      '#type'       => 'link',
      '#title'      => t('Link to a custom modal (modal1)'),
      '#url'        => Url::fromRoute('modal_examples.modal1', [
        'program_id'     => 123,
        'type' => 'all',
      ]),
      '#attributes' => [
        'id' => 'view-correlation-' . 12345,
        'class' => ['use-ajax'],
        'aria-label' => 'View useful information pertaining to item ' . '12345',
        '#prefix' => '<div class="abcdef">',
        '#suffix' => '</div>',
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode(
          [
            'width' => 'auto',
          ]
        ),
      ],
    ];


    $build['link-to-modal2'] = [
      '#type' => 'link',
      '#url' => new Url('modal_examples.modal2'),
      '#title' => 'Link to a node in a dialog (modal2)',
      '#prefix' => '<div class="pqrst">',
      '#suffix' => '</div>',
      '#attributes' => [
        'class' => ['use-ajax'],
      ],
    ];

    $build['#attached']['library'][] = 'core/drupal.dialog.ajax';

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

<?php

namespace Drupal\general\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a general example block.
 *
 * @Block(
 *   id = "general_example_block",
 *   admin_label = @Translation("General Example"),
 *   category = @Translation("DrupalBook")
 * )
 */
class ExampleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $current_route_name = \Drupal::routeMatch()->getRouteName();
    $current_path  = \Drupal::service('path.current')->getPath();

    $str = "Current route name = $current_route_name";
    $str .= "<br/> Current path = $current_path";
    //    $str = "Alias = $alias";
//    $str .= "<br/> path = $path";
//    $str .= "<br/> abc = $abc_val";

    $build['content'] = [
      '#type' => 'item',
      //      '#markup' => $this->t('It works!'),
      '#markup' => $str,
    ];



//    $build['content'] = [
//      '#markup' => $this->t('It works!'),
//    ];
    return $build;
  }

}

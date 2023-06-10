<?php

namespace Drupal\state_examples\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for State API, TempStore and UserData Examples routes.
 */
class StateExamplesController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build1() {

    // Set single value.
    \Drupal::state()->set('selwyn1', 'abc');
    \Drupal::state()->set('selwyn2', 'def');
    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved State API data: selwyn1: %selwyn1 and selwyn2: %selwyn2 .', [
        '%selwyn1' => \Drupal::state()->get('selwyn1'),
        '%selwyn2' => \Drupal::state()->get('selwyn2'),
      ]),
    ];

    // Delete single value.
    \Drupal::state()->delete('selwyn1');
    \Drupal::state()->delete('selwyn2');
    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Deleted (single) State API data.'),
    ];



    // Set multiple values.
    \Drupal::state()->setMultiple(['selwyn1' => 'ghi', 'selwyn2' => 'jkl']);
    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved State API data: selwyn1: %selwyn1 and selwyn2: %selwyn2 .', [
        '%selwyn1' => \Drupal::state()->get('selwyn1'),
        '%selwyn2' => \Drupal::state()->get('selwyn2'),
      ]),
    ];

    \Drupal::state()->deleteMultiple(['selwyn1', 'selwyn2']);
    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Deleted (multiple) State API data.'),
    ];


    return $build;
  }

  public function build2() {

    $str = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque porttitor urna consequat dolor convallis iaculis. Nullam blandit ipsum eget odio semper rhoncus. Phasellus fringilla ultricies augue et pretium. In pretium suscipit ligula, non ullamcorper urna viverra dictum. Etiam eget ante ut nibh consectetur volutpat. Integer luctus sem ac vulputate hendrerit. Aenean iaculis fringilla ante, sed tincidunt nibh tincidunt ac. Sed sed condimentum est.
Aenean libero tortor, ullamcorper vitae porttitor sit amet, posuere a ante. Vestibulum ipsum tellus, porta eu ligula at, porttitor congue nunc. Suspendisse felis lacus, tristique vel aliquam eget, congue non mi. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut sed massa sed diam condimentum elementum. Etiam viverra, ligula sit amet lacinia rutrum, felis augue laoreet turpis, ut gravida nisl urna eget nulla. Vestibulum a metus at mauris volutpat pulvinar. Fusce pulvinar mauris eu ante iaculis pharetra. Ut justo libero, ornare aliquam orci id, suscipit condimentum dui. Curabitur id enim faucibus, vehicula metus id, volutpat neque.
Sed venenatis diam justo, eget sodales est rhoncus facilisis. Morbi euismod turpis ipsum, et sollicitudin orci ultricies non. Pellentesque mattis turpis eget lacus aliquam, vitae accumsan lorem tempor. Sed tincidunt nisi non iaculis dignissim. Nulla sit amet metus elit. Fusce ac dolor et dolor facilisis interdum et ac lorem. Donec velit tortor, aliquam convallis sodales vel, elementum a dui. Vestibulum vestibulum dui eget efficitur pretium. Nulla non imperdiet mauris. Ut feugiat mollis est at ullamcorper. Proin ex eros, tempus a ex sit amet, gravida ultricies metus. Aliquam ipsum felis, ultricies malesuada urna at, congue placerat ex. Nullam ipsum felis, lacinia convallis libero sed, malesuada ullamcorper nisi. Vestibulum lobortis, sem et blandit porttitor, magna justo finibus dolor, id vehicula massa quam et dui.
Vivamus ullamcorper orci ac enim elementum, sit amet lobortis massa ultrices. Donec ullamcorper dapibus sagittis. Suspendisse volutpat risus vitae posuere faucibus. Nunc ut ex quis justo placerat congue. Proin sit amet imperdiet orci. Phasellus nec sapien a purus convallis vulputate non sit amet felis. Mauris efficitur nibh odio, quis viverra diam ullamcorper in. Duis id turpis orci. Phasellus ut arcu sit amet lacus convallis placerat ac sit amet dolor. Integer urna leo, sollicitudin et vehicula sit amet, mattis a risus. Praesent mollis non massa a semper. Aliquam vitae porttitor justo.
Morbi vehicula lectus et odio facilisis feugiat. Mauris enim tellus, lobortis in dui semper, tempor congue enim. Mauris eros leo, ultricies vel pulvinar sed, pharetra at nisl. Aenean consectetur nunc sed felis malesuada euismod. Vivamus luctus mauris quis ante porttitor, quis eleifend augue vestibulum. Mauris commodo nibh non nisl efficitur, eu interdum sapien aliquet. Vestibulum sed volutpat turpis. Proin dapibus pharetra libero, id pellentesque lorem bibendum feugiat. Nam mollis cursus dapibus. Integer vehicula tortor vitae velit congue, sit amet sagittis urna hendrerit. Sed at odio justo.';

    // Explode string into array of paragraphs.
    $test2 = explode( "\n", \Drupal::state()->get('selwyn.long.string'));

    \Drupal::state()->set('selwyn.long.string', $str);
    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved State API data: selwyn.long.string: %selwyn1.', [
        '%selwyn1' => \Drupal::state()->get('selwyn.long.string'),
      ]),
    ];
    return $build;
  }

  public function build3() {

    \Drupal::state()->set('selwyn.important.string', 'abc');
    \Drupal::state()->set('selwyn.more.important.string', 'def');

    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved State API data: selwyn.important.string: %value1 and selwyn.more.important.string: %value2 .', [
        '%value1' => \Drupal::state()->get('selwyn.important.string'),
        '%value2' => \Drupal::state()->get('selwyn.more.important.string'),
      ]),
    ];
    return $build;
  }

  public function build4() {

    /** @var \Drupal\user\UserDataInterface $userData */
    $userData = \Drupal::service('user.data');
    $user_id = 2;

    $userData->set('state_examples', $user_id, 'program.123.vote.0.finalized', 'COMPLETED');

    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved User Data API data: %value1.', [
        '%value1' => $userData->get('state_examples', $user_id, 'program.123.vote.0.finalized'),
      ]),
    ];

    $userData->delete('state_examples', $user_id, 'program.123.vote.0.finalized');

    return $build;
  }

  public function build5() {

    // Private TempStore example.

    // Get the private TempStore for the state_examples module.
    $tempstore = \Drupal::service('tempstore.private')->get('state_examples');
    $tempstore->set('selwyn.important.string', 'abcdef');

    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved Private TempStore API data: %value1.', [
        '%value1' => $tempstore->get('selwyn.important.string'),
      ]),
    ];

//    $tempstore->delete('selwyn.important.string');

    // Store an array.
    $array = [
      'id' => '123',
      'name' => 'Dave',
    ];
    $tempstore->set('selwyn.important.array', $array);
    $array = $tempstore->get('selwyn.important.array');

    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved Private TempStore API data: id = %value1, name= %value2.', [
        '%value1' => $array['id'],
        '%value2' => $array['name'],
      ]),
    ];

    // Delete the data.
    $tempstore->delete('selwyn.important.array');

    return $build;
  }

  public function build6() {

    // Shared TempStore example.

    // Get the shared TempStore for the state_examples module.
    /** @var \Drupal\Core\TempStore\SharedTempStoreFactory $factory */
    $factory = \Drupal::service('tempstore.shared');
    $tempstore = $factory->get('state_examples');

    // Store an array.
    $agent_array = [
      'id' => '007',
      'name' => 'James Bond',
    ];
    $tempstore->set('selwyn.important.agent', $agent_array);

    // Retrieve the data.
    $array = $tempstore->get('selwyn.important.agent');
    $build['content'][] = [
      '#type' => 'item',
      '#markup' => $this->t('Retrieved Private TempStore API data: id = %value1, name= %value2.', [
        '%value1' => $array['id'],
        '%value2' => $array['name'],
      ]),
    ];

    // Delete the data.
//    $tempstore->delete('selwyn.important.agent');

    return $build;
  }


}

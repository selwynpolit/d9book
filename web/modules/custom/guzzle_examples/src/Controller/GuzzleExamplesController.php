<?php

namespace Drupal\guzzle_examples\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use GuzzleHttp\Exception\ClientException;

/**
 * Returns responses for Guzzle Examples routes.
 */
class GuzzleExamplesController extends ControllerBase {

  /**
   * Example1
   */
  public function example1() {

    //Initialize client;
    $client = \Drupal::httpClient();
    $uri = 'https://demo.ckan.org/api/3/action/package_list';

    // Returns a GuzzleHttp\Psr7\Response.
    $response = $client->request('GET', 'https://demo.ckan.org/api/3/action/package_list');

    // Returns a GuzzleHttp\Psr7\Response.
    $response = $client->get($uri);

    // Returns a GuzzleHttp\Psr7\Stream.
    $stream = $response->getBody();
    $json_data = Json::decode($stream);
    $help = $json_data['help'];
    $success = $json_data['success'];
    $result = $json_data['result'][0];

    $msg = "<br><strong>GET</strong>";
    $msg .= "<br>URI: " . $uri;
    $msg .= "<br>Help: " . $help;
    $msg .= "<br>Success: " . $success;
    $msg .= "<br>Result: " . $result;


    // Post example.
    $uri = 'http://demo.ckan.org/api/3/action/group_list';
    $request = $client->post($uri, [
      'json' => [
        'id'=> 'data-explorer'
      ]
    ]);
    $stream = $request->getBody();
    $json_data = Json::decode($stream);
    $help = $json_data['help'];
    $success = $json_data['success'];
    $result = $json_data['result'][0] . ' and ' . $json_data['result'][1];


    $msg .= "<br><strong>POST</strong>";
    $msg .= "<br>URI: " . $uri;
    $msg .= "<br>Help: " . $help;
    $msg .= "<br>Success: " . $success;
    $msg .= "<br>Result: " . $result;


    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t($msg),
    ];

    return $build;
  }

  /**
   * Example2
   */
  public function example2() {

    $msg = "";
    $client = \Drupal::httpClient();
    $uri = 'https://api.github.com/user';

    try {
      $request = $client->get($uri, [
        'auth' => ['username', 'password']
      ]);
      $response = $request->getBody();
      $msg .= "<br><strong>GET</strong>";
      $msg .= "<br>URI: " . $uri;
    }

    catch (ClientException $e) {
      \Drupal::messenger()->addError($e->getMessage());
      watchdog_exception('guzzle_examples', $e);
    }

    catch (\Exception $e) {
      \Drupal::messenger()->addError($e->getMessage());
      watchdog_exception('guzzle_examples', $e);
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t($msg),
    ];

    return $build;
  }

  /**
   * Example3
   */
  public function example3() {

    $msg = "";
    $client = \Drupal::httpClient();
    $uri = 'https://api.github.com/user';

    try {

      $source_uri = 'https://www.austinprogressivecalendar.com/sites/default/files/styles/medium/public/inserted-images/2018-04-02_5.jpg';
      // Note sites/default/files/abc directory must exist for this to succeed.
      $destination_uri = 'sites/default/files/abc/test.png';
      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $client->get($source_uri, ['sink' => $destination_uri]);
      // file gets downloaded to /sites/default/files/abc/test.png

      $msg .= "<br><strong>Retrieve File via Guzzle</strong>";
      $msg .= "<br>Source: " . $source_uri;
      $msg .= "<br>Dest: " . $destination_uri;
    }
    catch (ClientException $e) {
      \Drupal::messenger()->addError($e->getMessage());
      watchdog_exception('guzzle_examples', $e);
    }

    catch (\Exception $e) {
      \Drupal::messenger()->addError($e->getMessage());
      watchdog_exception('guzzle_examples', $e);
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t($msg),
    ];

    return $build;
  }

}

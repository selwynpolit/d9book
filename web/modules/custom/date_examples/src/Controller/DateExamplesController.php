<?php

namespace Drupal\date_examples\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;


/**
 * Returns responses for Date Examples routes.
 */
class DateExamplesController extends ControllerBase {

  public function test1() {

    $event_node = Node::load(25);

    // Returns a Drupal\datetime\Plugin\Field|FieldType\DateTimeFieldItemList.
    $d1 = $event_node->field_event_date;
    // Returns a Drupal\Core\DateTime\DrupalDateTime.
    $d2 = $event_node->field_event_date->date;


    // Returns 2021-12-27
    $event_date = $event_node->field_event_date->value;

    // Returns 2021-12-28T16:00:00
    $event_datetime = $event_node->field_event_datetime->value;

    // returns Unix timestamp
    // Timestamp for field_event_date: 1640606400
    $timestamp1 = $event_node->field_event_date->date->getTimestamp();
    // Timestamp for field_event_datetime: 1640707200
    $timestamp2 = $event_node->field_event_datetime->date->getTimestamp();

    // field_event_date_range start: 2021-12-29T14:00:00
    $event_date_range_start = $event_node->field_event_date_range->value;
    // field_event_date_range end: 2021-12-30T23:00:00
    $event_date_range_end = $event_node->field_event_date_range->end_value;

    // returns a DrupalDateTime object with all its goodness
    $ddt_object = $event_node->field_event_datetime->date;

    //Format the date nicely for output: 12/28/21
    $formatted_date = $ddt_object->format('m/d/y');

    // Format created date using a DrupalDateTime object.
    $created_date = $event_node->getCreatedTime();
    $cdt = DrupalDateTime::createFromTimestamp($created_date);
    $formatted_created_date = $cdt->format('m/d/Y g:i a');

    // Format created date using date.formatter service.
    $created_date = $event_node->getCreatedTime();
    // Get date formatter service.
    $formatter = \Drupal::service('date.formatter');
    // Parameters: format($timestamp, $type = 'medium', $format = '', $timezone = NULL, $langcode = NULL).
    $formatted_created_date = $formatter->format($created_date, 'custom', 'm/d/Y g:i a');

    // Displays 05/04/2022 3:49 pm
    $formatted_created_date = \Drupal::service('date.formatter')->format($created_date, 'custom', 'm/d/Y g:i a');

    // Displays 2022-05-04 15:49:30
    $formatted_created_date = \Drupal::service('date.formatter')->format($created_date, 'custom', 'Y-m-d H:i:s');

    // Displays Wed, 05/04/2022 - 15:49
    $formatted_created_date = \Drupal::service('date.formatter')->format($created_date);



    $str = "Results: ";
    $str .= "<br/>field_event_date: $event_date";
    $str .= "<br/>field_event_datetime: $event_datetime";
    $str .= "<br/>Timestamp for field_event_date: $timestamp1";
    $str .= "<br/>Timestamp for field_event_datetime: $timestamp2";
    $str .= "<br/>field_event_date_range start: $event_date_range_start";
    $str .= "<br/>field_event_date_range end: $event_date_range_end";
    $str .= "<br/>Formatted date: $formatted_date";
    $str .= "<br/>Unformatted created date: $created_date";
    $str .= "<br/>Formatted created date: $formatted_created_date";


    // You can also retrieve a date range field using this syntax.
    $from = $event_node->get('field_event_date_range')->getValue()[0]['value'];
    $to = $event_node->get('field_event_date_range')->getValue()[0]['end_value'];
    $str .= "<br/>From: $from";
    $str .= "<br/>To: $to";


    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];

    //$date = new \DateTime();
    $date = new DrupalDateTime();
    $render_array['copyright'] = [
      '#markup' => t('Copyright @year&copy; My Company', [
        '@year' => $date->format('Y'),
      ]),
    ];

    $date = new DrupalDateTime();
    $date->setTimezone(new \DateTimeZone('America/Chicago'));
    $formatted_date = $date->format('m/d/Y g:i a');
    $render_array['formatted'] = [
      '#type' => 'item',
      '#markup' => t('Formatted DrupalDateTime = @date', ['@date' => $formatted_date]),
    ];


    return $render_array;
  }

  public function test2() {

    $str = "Results";
    $year = '2022';
    $term_id = 5;
    $titles = $this->eventsForYear($year, $term_id);
    $count = count($titles);
    $str .= "<br/>Found $count titles for query $year, term_id $term_id";

    foreach ($titles as $title) {
      $str .= "<br/>$title";
    }
    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];
    return $render_array;
  }



  private function eventsForYear($year, $term_id): array {

    // Build valid range of start dates/times.
    $format = 'Y-m-d H:i';
    $start_date = DrupalDateTime::createFromFormat($format, $year . "-01-01 00:00");
    $end_date = DrupalDateTime::createFromFormat($format, $year . "-12-31 23:59");

    $start_date_ts = $start_date->getTimestamp();
    $end_date_ts = $end_date->getTimestamp();

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'event')
      ->condition('field_event_category', $term_id, '=')
      ->condition('created', $start_date_ts, '>=')
      ->condition('created', $end_date_ts, '<=')
      ->sort('title', 'DESC');
    $nids = $query->execute();
    $titles = [];
    if ($nids) {
      foreach ($nids as $nid) {
        $node = Node::load($nid);
        $titles[]= $node->getTitle();
      }
    }
    return $titles;
  }


//  use Drupal\Core\Datetime\DrupalDateTime;
//  use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
  /*
   * Query a date field with a time.
   */
  public function test3() {

    // Get a date string suitable for use with entity query.
    $date = new DrupalDateTime();
    // This is a date/time from my local timezone.
    $date = DrupalDateTime::createFromFormat('d-m-Y: H:i A', '28-12-2021: 10:00 AM');
    $date->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
    $query_date = $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

    $str = "Results";

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'event')
      ->condition('status', 1)
      ->condition('field_event_datetime.value', $query_date, '=')
      ->sort('title', 'ASC');
    $nids = $query->execute();
    $count = count($nids);

    $print_date = $date->format('d-m-Y: H:i A');
    $str .= "<br/><strong>$count event(s) for field_event_datetime = $print_date (UTC)</strong> ";
    $date->setTimezone(new \DateTimeZone('America/Chicago'));
    $print_date = $date->format('d-m-Y: H:i A');
    $str .= "<br/>For my timezone (America/Chicago), that is $print_date";

    foreach ($nids as $nid) {
      $event_node = Node::load($nid);
      $title = $event_node->getTitle();
      $display_date = $event_node->field_event_datetime->value;
      $str .= "<br/>$title - date: $display_date";
    }
    $str .= "<br/>";

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'event')
      ->condition('status', 1)
      ->condition('field_event_datetime.value', $query_date, '>')
      ->sort('title', 'ASC');
    $nids = $query->execute();
    $count = count($nids);

    $print_date = $date->format('d-m-Y: H:i A');
    $str .= "<br/><strong>$count event(s) for field_event_datetime > $print_date (UTC)</strong> ";
    $date->setTimezone(new \DateTimeZone('America/Chicago'));
    $print_date = $date->format('d-m-Y: H:i A');
    $str .= "<br/>For my timezone (America/Chicago), that is $print_date";

    foreach ($nids as $nid) {
      $event_node = Node::load($nid);
      $title = $event_node->getTitle();
      $display_date = $event_node->field_event_datetime->value;
      $str .= "<br/>$title - date: $display_date";
    }
    $str .= "<br/>";



    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];
    return $render_array;
  }

  /**
   * Query a Date field with no time.
   */
  public function test4() {

    // Requires a date string suitable for use with entity query.
    $query_date = '2021-12-27';

    // Get a date string suitable for use with entity query.
    $date = DrupalDateTime::createFromFormat('j-M-Y', '27-Dec-2021');
    $date->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
    // NB. Specify the date-only storage format - not the datetime storage format!
    $query_date = $date->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

    $query = \Drupal::entityQuery('node')
      ->condition('type', 'event')
      ->condition('status', 1)
      ->condition('field_event_date.value', $query_date, '=')
      ->sort('title', 'ASC');
    $nids = $query->execute();
    $count = count($nids);

    $str = "Results";
    $str .= "<br/>Found $count events for field_event_date = $query_date";
    foreach ($nids as $nid) {
      $event_node = Node::load($nid);
      $title = $event_node->getTitle();
      $date = $event_node->field_event_date->value;
      $str .= "<br/>$title - date: $date";
    }
    $str .= "<br/>";


    $query = \Drupal::entityQuery('node')
      ->condition('type', 'event')
      ->condition('status', 1)
      ->condition('field_event_date.value', $query_date, '>')
      ->sort('title', 'ASC');
    $nids = $query->execute();
    $count = count($nids);

    $str .= "<br/>Found $count events for field_event_date > $query_date";

    foreach ($nids as $nid) {
      $event_node = Node::load($nid);
      $title = $event_node->getTitle();
      $date = $event_node->field_event_date->value;
      $str .= "<br/>$title - date: $date";
    }

    $render_array['content'] = [
      '#type' => 'item',
      '#markup' => $str,
    ];
    return $render_array;
  }


}

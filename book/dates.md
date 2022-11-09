# Dates and Times

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

- [Dates and Times](#dates-and-times)
  - [Get a Date Field](#get-a-date-field)
  - [Formatting date fields](#formatting-date-fields)
  - [Formatting a DateTime as a year only](#formatting-a-datetime-as-a-year-only)
  - [Formatting a date string with an embedded timezone](#formatting-a-date-string-with-an-embedded-timezone)
  - [Date Range fields: Load start and end values](#date-range-fields-load-start-and-end-values)
  - [Formatting a date range for display](#formatting-a-date-range-for-display)
  - [Saving Date Fields](#saving-date-fields)
  - [Create new DrupalDateTime objects](#create-new-drupaldatetime-objects)
  - [Add some days to a date field and save](#add-some-days-to-a-date-field-and-save)
  - [Custom Date formatting of created time with Drupal date.formatter service](#custom-date-formatting-of-created-time-with-drupal-dateformatter-service)
  - [Date arithmetic](#date-arithmetic)
  - [Comparing DrupalDateTime values](#comparing-drupaldatetime-values)
  - [Comparing Dates (without comparing times)](#comparing-dates-without-comparing-times)
  - [Comparing Dates to see if a node has expired?](#comparing-dates-to-see-if-a-node-has-expired)
  - [Node creation and changed dates](#node-creation-and-changed-dates)
  - [Query the creation date (among other things) using entityQuery](#query-the-creation-date-among-other-things-using-entityquery)
  - [Smart Date](#smart-date)
    - [Smart date: Load and format](#smart-date-load-and-format)
    - [Smart date: all-day](#smart-date-all-day)
    - [Smart date: Range of values](#smart-date-range-of-values)
  - [Reference](#reference)
    - [DrupalDateTime API reference](#drupaldatetime-api-reference)
    - [PHP Date format strings:](#php-date-format-strings)
    - [UTC](#utc)
    - [Unix Timestamps](#unix-timestamps)

![visitors](https://page-views.glitch.me/badge?page_id=selwynpolit.d9book-gh-pages-dates)

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

Date fields in Drupal are stored in UTC date strings (e.g. `2022-06-30T12:00:00`) while node created and changed values are stored as Unix Epoch timestamps (e.g. `1656379475`) in the `node_field_data table` (fields: `created` and `changed`).

## Get a Date Field

You can retrieve date fields a few different ways. Remember, they are stored in UTC date strings e.g. `2022-06-30T12:00:00`

```php
//returns whatever the string is e.g. 2024-08-31
$end_date = $contract_node->field_contract_end_date->value;

// returns unix timestamp e.g. 1725105600
$end_date = $contract_node->field_contract_end_date->date->getTimestamp();

// returns a DrupalDateTime object with all its goodness
 $end_date = $contract_node->field_contract_end_date->date;

//Format the date nicely for output
 $formatted_date = $end_date->format('m/d/y');
```

From <https://drupal.stackexchange.com/questions/252333/how-to-get-formatted-date-string-from-a-datetimeitem-object>

A date field has two properties, 
-   value to store the date in UTC and 
-   date, a computed field returning a DrupalDateTime object, on which you can use the methods getTimestamp() or format():

```php
*// get unix timestamp
\* $timestamp = $node->field_date->date->getTimestamp();
 *// get a formatted date
\* $date_formatted = $node->field_date->date->format('Y-m-d H:i:s');
```

Using `$node->field_mydatefield->date` is ideal as it returns a `DrupalDateTime` class which gives you all sorts of goodness. More [about DrupalDateTime here](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Datetime%21DrupalDateTime.php/class/DrupalDateTime/9.4.x)

If you need to do calculations involving Unix timestamps, then using `$node->field_mydatefield->getTimestamp()` is useful although `DrupalDateTime` can also do calculations.

## Formatting date fields 

Here we have a date field for start_date and another for end_date.

```php
// formatted start date
$start_date_formatted = $node->field_date->start_date->format('Y-m-d H:i:s');
// formatted end date
$end_date_formatted = $node->field_date->end_date->format('Y-m-d H:i:s');
```

See [Nodes and Fields](book/nodes_n_fields.html) Date Fields section for more on date fields

## Formatting a DateTime as a year only

This code creates a `\DateTime` object and returns the year in a render array with some markup. It is probably better manners to use the  `Drupal\Core\Datetime\DrupalDateTime` class which is just a wrapper for `\DateTime`.

This example is used in a block build function

```php
public function build() {
  $date = new \DateTime();
  return [
    '#markup' => t('Copyright @year&copy; My Company', [
      '@year' => $date->format('Y'),
    ]), ];
}
```

## Formatting a date string with an embedded timezone

Here you have a date string with an embedded timezone so you can make it into a `DrupalDateTime` and then format it into a usable string you can store in the database.

```php
use Drupal\\Core\\Datetime\\DrupalDateTime;

\$date_string = \"2020-08-24T15:28:04+00:00\";\
\$given = new DrupalDateTime(\$date_string);\
\$newstring = \$given-\>format(\"Y-m-d\\Th:i:s\");
```

## Date Range fields: Load start and end values

Retrieve a date range field from a node. Specify value for the start date and end_value for the end date.

```php
$start = $node->get('field_cn_start_end_dates')->value
$end = $node->get('field_cn_start_end_dates')->end_value
```

## Formatting a date range for display

Making a date range like `3/30/2019 - 3/31/2019` show like `Mar 30-31, 2019`.

If you are viewing a node, there will also be a way to get to the node's fields like this where we are looking at a date range:

From a .theme file:

```php
$from = $variables["node"]->get('field_date')->getValue()[0]['value'];
$to = $variables["node"]->get('field_date')->getValue()[0]['end_value'];
```

Here is an example of a `hook_preprocess_node` function  in a `.theme` file. We are creating a `scrunch_date` variable to be rendered.  See the section: 

```php
$variables['scrunch_date'] = [
    '#type' => 'markup',
    ...
```

Don't forget the use statement:

```php
use Drupal\Core\Datetime\DrupalDateTime;
```

You can then use it in your hooks.

```php
/**
 * Implements hook_preprocess_node
 *
 * @param $variables
 */
function vst_preprocess_node(&$variables) {
  if (!empty($variables['content']['field_date'])) {
    $date = $variables['content']['field_date'];

    $from = new DrupalDateTime($variables["node"]->get('field_date')->getValue()[0]['value']);
    $date_array = explode("-", $from);
    $from_day = substr($date_array[2], 0, 2);
    $from_month = $date_array[1];

    $to = new DrupalDateTime($variables["node"]->get('field_date')->getValue()[0]['end_value']);
    $date_array = explode("-", $to);
    $to_day = substr($date_array[2], 0, 2);
    $to_month = $date_array[1];

    if ($from_month === $to_month && $from_day != $to_day) {
      $variables['scrunch_date'] = [
        '#type' => 'markup',
        '#markup' => $from->format("M j-") . $to->format("j, Y"),
      ];
    }

  }
//  For debugging
// kint($variables);
}
```

Now in the twig node template we can output the `scrunch_date` we created.

From `/web/themes/verygood/templates/node/node--seminar--teaser.html.twig`.

```twig
{%  if content.field_date %}
  {% if scrunch_date %}
    <div>
      {{ scrunch_date }}
    </div>
  {% else %}
    <div>
      {{ content.field_date }}
    </div>
  {% endif %}
{% endif %}
```

## Saving Date Fields

Date fields in Drupal are stored in strings and when you use `get()` or `set()`, they return strings.

```php
$node->set('field_date', '2025-12-31');
$node->set('field_datetime', '2025-12-31T23:59:59');
// It’s different for created and changed.
$node->set('created', '1760140799');
$node->save();
```

If you want to manipulate them, convert them to DrupalDateTime objects, then convert them back to strings for saving.

## Create new DrupalDateTime objects

```php
use Drupal\Core\Datetime\DrupalDateTime;

$date = DrupalDateTime::createFromFormat('j-M-Y', '20-Jul-2022');

// Use current date and time 
$date = new DrupalDateTime('now');  
// format it 
// prints nicely formatted like Tue, Jul 16, 2022 - 11:34:am
print $date->format('l, F j, Y - H:i'); 

// OR

// Use current date and time 
$date = new DrupalDateTime('now');

// prints nicely formatted like 16-07-2022: 11:43 AM
print $date->format('d-m-Y: H:i A');
```

How about with timezones?

```php
$date = new DrupalDateTime();
$date->setTimezone(new \DateTimeZone('America/Chicago'));
// Prints current time for the given time zone like 07/16/2022 10:59 am
print $date->format('m/d/Y g:i a');

// Another variation using specific date and UTC zone
$date = new DrupalDateTime('2019-07-31 11:30:00', 'UTC');
$date->setTimezone(new \DateTimeZone('America/Chicago'));
// prints 07/31/2019 6:30 am
print $date->format('m/d/Y g:i a');
```

UTC: <https://en.wikipedia.org/wiki/Coordinated_Universal_Time>

Nice article on writing date fields programmatically with more info on
UTC timezone at
<https://gorannikolovski.com/blog/set-date-field-programmatically#:~:text=Get%20the%20date%20field%20programmatically,)%3B%20%2F%2F%20For%20datetime%20fields>.

## Add some days to a date field and save

The code below shows adding `$days` (an integer) to the date value retrieved from the field: `field_cn_start_date`.

Don't forget to add the use statement.

```php
use Drupal\Core\Datetime\DrupalDateTime;

$start_date_val = $node->get('field_cn_start_date')->value;
$days = intval($node->get('field_cn_suspension_length')->value) - 1;

//$end_date = DrupalDateTime::createFromFormat('Y-m-d H:i:s', $start_date_val . " 00:00:00");
$end_date = DrupalDateTime::createFromFormat('Y-m-d', $start_date_val );
$end_date->modify("+$days days");
$end_date = $end_date->format("Y-m-d");

$node->set('field_cn_end_date', $end_date);
$node->save();
```

## Custom Date formatting of created time with Drupal date.formatter service

If you want to use a custom date format

```php
$date = $node->getCreatedTime();

// You could also use Drupal's format_date() function, or some custom PHP date formatting.
// $format is a PHP date string like 'M Y'
$variables['date'] = \Drupal::service('date.formatter')->format($date, 'custom', '$format'); 
```
See PHP Date format strings:
<https://www.php.net/manual/en/datetime.format.php#:~:text=format%20parameter%20string-,format,-character>

## Date arithmetic

Here is an example from a module showing a `hook_entity_type_presave()` where some data is changed as the node is being saved. The date arithmetic is pretty simple but the rest of the code is kinda messy.

This is the date arithmetic part:

```php
$end_date = DrupalDateTime::createFromFormat('Y-m-d', $start_date_val);
$end_date->modify("+$days days");
$end_date = $end_date->format("Y-m-d");
$node->set('field_cn_end_date', $end_date);
```

The rest of this code does some convoluted wrangling to figure out end dates based on user permissions, changes a node title, looks to see if this is an extension of a previously submitted notice and grabs some date fields from the original notice for use those in the current node.

```php
/**
 * Implements hook_ENTITY_TYPE_presave().
 */
function oag_mods_node_presave(NodeInterface $node) {
  switch ($node->getType()) {
    case 'cat_notice':
      $end_date = NULL != $node->get('field_cn_start_end_dates')->end_value ? $node->get('field_cn_start_end_dates')->end_value : 'n/a';
      $govt_body = NULL != $node->field_cn_governmental_body->value ? $node->field_cn_governmental_body->value : 'Unnamed Government Body';
      $start_date_val = $node->get('field_cn_start_date')->value;

      $accountProxy = \Drupal::currentUser();
      $account = $accountProxy->getAccount();
      // Anonymous users automatically fill out the end_date.
      if (!$account->hasPermission('administer cat notice')) {
        $days = intval($node->get('field_cn_suspension_length')->value) - 1;

        $end_date = DrupalDateTime::createFromFormat('Y-m-d', $start_date_val);
        $end_date->modify("+$days days");
        $end_date = $end_date->format("Y-m-d");
        $node->set('field_cn_end_date', $end_date);
      }

      // Always reset the title.
      $title = substr($govt_body, 0, 200) . " - $start_date_val";
      $node->setTitle($title);

      /*
       *  Fill in Initial start and end dates if this is an extension of
       * a previously submitted notice.
       */
      $extension = $node->get('field_cn_extension')->value;
      if ($extension) {
        $previous_notice_nid = $node->get('field_cn_original_notice')->target_id;
        $previous_notice = Node::load($previous_notice_nid);
        if ($previous_notice) {
          $initial_start = $previous_notice->get('field_cn_start_date')->value;
          $initial_end = $previous_notice->get('field_cn_end_date')->value;
          $node->set('field_cn_initial_start_date', $initial_start);
          $node->set('field_cn_initial_end_date', $initial_end);
        }
      }

      break;
  }
}
```

## Comparing DrupalDateTime values

DrupalDateTimes are derived from DateTimePlus which is a wrapper for PHP DateTime class. That functionality allows you to do comparisons. It is probably better manners to use DrupalDateTime instead of DateTime.

```php
date_default_timezone_set('Europe/London');

$d1 = new DateTime('2008-08-03 14:52:10');
$d2 = new DateTime('2008-01-03 11:11:10');
var_dump($d1 == $d2);
var_dump($d1 > $d2);
var_dump($d1 < $d2);

// Returns.
bool(false)
bool(true)
bool(false)
```

## Comparing Dates (without comparing times)

Use the setTime() function to remove the time part of a datetime so we can make comparisons of just the date.

From a form validation in a `.module` file.

```php
function oag_mods_cn_form_validate($form, FormStateInterface $form_state) {
    $start_date = $form_state->getValue('field_cn_start_date');
    if ($start_date) {
      $start_date = $start_date[0]['value'];
      $start_date->setTime(0, 0, 0);
      $now = new Drupal\Core\Datetime\DrupalDateTime();
      $now->modify("-2 days");
      $now->setTime(0, 0, 0);

      \Drupal::messenger()->addMessage("Start date = $start_date");
      \Drupal::messenger()->addMessage("Now date - 2 days = $now");

      if ($start_date < $now) {
        $form_state->setErrorByName('edit-field-cn-start-date-0-value-date', t('The starting date is more than 2 days in the past. Please select a later date'));
      }
    }
}
```

## Comparing Dates to see if a node has expired?

Checking to see if the value in the field field_expiration_date has passed. The field_expiration_date is a standard Drupal date field in a
node.

From a controller:

```php
$source_node = $node_storage->load($nid);
$expiration_date = $source_node->field_expiration_date->value;

// Use expiration date to un-publish expired resellers to hide them.
$status = 1;
if ($expiration_date) {
  $expirationDate = DrupalDateTime::createFromFormat('Y-m-d', $expiration_date);
  $now = new DrupalDateTime();
  if ($expiration_date < $now) {
    $status = 0;
  }
}
// When status is 0, unpublish the node like
// $node->set(‘status’, $status);
// $node->save();
```

TODO: date fields are stored in UTC. Need to factor in timezone. See https://en.wikipedia.org/wiki/Coordinated_Universal_Time

## Node creation and changed dates

Both `created` and `changed` are stored as Unix Epoch timestamps in the `node_field_data` table. Here is a function which does an `entityQuery` for a node and returns a formatted string version of the creation date.

Note. This will handle epoch dates before 1970. You can also use `$node->get('changed')` to retrieve the changed date.

```php
use Drupal\Core\Datetime\DrupalDateTime;

  protected function loadFirstOpinionYear($term_id) {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'opinion')
      ->condition('field_category', $term_id, '=')
      ->sort('title', 'ASC') // or DESC
      ->range(0, 1);
    $nids = $query->execute();
    if ($nids) {
      $node = $storage->load(reset($nids));
    }
    $time = $node->get('created')->value;

    $d = new DrupalDateTime("@$time"); //can use either this
    $d = new \DateTime("@$time"); // or this..

    $str = $d->format('Y-m-d H:i:s');
    return $str;
  }
```

## Query the creation date (among other things) using entityQuery

Note. The `created` and `changed` fields use a Unix timestamp. This is an int 11 field in the database with a value like `1525302749` Drupal date fields data looks like `2019-05-15T21:32:00` (varchar 20)

If you want to query a `date` field in a content type, you will have to dork around with the setTimezone stuff that is commented out below. More at <https://blog.werk21.de/en/2018/02/05/date-range-fields-and-entity-query-update>

and

<https://drupal.stackexchange.com/questions/198324/how-to-do-a-date-range-entityquery-with-a-date-only-field-in-drupal-8>

From a controller:

```php
  protected function loadOpinionForAYear($year, $term_id) {
    $storage = \Drupal::entityTypeManager()->getStorage('node');

    // Get a date string suitable for use with entity query.
    //    $date = new DrupalDateTime();  // now
    $format = 'Y-m-d H:i';
    $start_date = DrupalDateTime::createFromFormat($format, $year . "-01-01 00:00");
    $end_date = DrupalDateTime::createFromFormat($format, $year . "-12-31 23:59");

    $start_date = $start_date->getTimestamp();
    $end_date = $end_date->getTimestamp();

//    $start_date->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
//    $end_date->setTimezone(new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE));
//    $start_date = $start_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
//    $end_date = $end_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

// Set the condition.
//    $query->condition('field_date.value', $start_date, '>=');
//    $query->condition('field_date.value', $end_date, '<=');

    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'opinion')
      ->condition('field_category', $term_id, '=')
      ->condition('created', $start_date, '>=')
      ->condition('created', $end_date, '<=')
      ->sort('title', 'DESC');
    $nids = $query->execute();
    $titles = [];
    if ($nids) {
      $nodes = $storage->loadMultiple($nids);
      foreach ($nodes as $node) {
        $titles[]= $node->getTitle();
      }
    }
    return $titles;
  }
```

## Smart Date

This module is super handy as it fills in lots of the functionality that Drupal core dates lack. From <https://www.drupal.org/project/smart_date>

This module attempts to provide a more user-friendly date field, by upgrading the functionality of core in several ways:

Easy Admin UI: Includes the concept of duration, so that a field can have a configurable default duration (e.g. 1 hour) and the end time will be auto-populated based on the start. The overall goal is to provide a smart interface for time range/event data entry, more inline with calendar applications which editors will be familiar with.

All Day Events Most calendar applications provide a one-click option to make a an event, appointment, or other time-related content span a full day. This module brings that same capability to Drupal.

Zero Duration Events Show only a single time for events that don't need a duration.

Formatting: More sophisticated output formatting, for example to show the times as a range but with a single output of the date. In the settings a site builder can control how date the ranges will be output, at a very granular level.

Performance: Dates are stored as timestamps to improve performance, especially when filtering or sorting. Concerns with the performance of core's date range have been documented in [#3048072: Date Range field creates very slow queries in Views](https://www.drupal.org/project/drupal/issues/3048072).

Overall, the approach in this module is to leverage core's existing Datetime functionality, using the timestamp storage capability also in core, with some custom Javascript to add intelligence to the admin interface, and a suite of options to ensure dates can be formatted to suit any site's needs.

Display configuration is managed through translatable Smart Date Formats, so your detailed display setup is easily portable between fields, views, and so on.

### Smart date: Load and format

Load the smart date field and use the Drupal date formatting service (`date.formatter`). Smart date fields are always stored as unix timestamp values e.g. `1608566400` which need conversion for human consumption.

```php
$start = $node->field_when->value;
$formatter = \Drupal::service('date.formatter');

//returns something like 12/21/2020 10:00 am
$start_time = $formatter->format($start, 'custom', 'm/d/Y g:ia'); 
```

Alternatively, you could load it, create a DrupalDateTime and then format it:

```php
$start = $node->field_when->value;
$dt = DrupalDateTime::createFromTimestamp($start);
$start_date = $dt->format('m/d/y'); //returns 12/21/22
$start_time = $dt->format('g:ia'); // returns 10:00am
```

### Smart date: all-day

To check if a smart date is set to all day, check the duration. If it is 1439, that means all day.

```php
$start_ts = $node->field_when->value;
$start_dt = DrupalDateTime::createFromTimestamp($start_ts);
$start_date = $start_dt->format('m/d/Y');
$duration = $node->field_when->duration;  //1439 = all day
if ($duration == 1439) {
  $start_time = "all day";
}
else {
  $start_time = $start_dt->format('g:ia');
}
```

### Smart date: Range of values

```php
//Event start date.

//returns a SmartDateFieldItemList
$whens = $node->get('field_when'); 

// Each $when is a \Drupal\smart_date\Plugin\Field\FieldType\SmartDateItem.
foreach ($whens as $when) {
  $start = $when->value;
  $end = $when->end_value;
  $duration = $when->duration;  //1439 = all day
  $tz = $when->timezone;  //"" means default. Uses America/Chicago type format.
}
```

You can also peek into the repeating rule and repeating rule index. These are in the `smart_date_rule` table and I believe the `index` column identifies which item is in the "instances" column.

```php
$rrule = $when->rrule;
$rrule_index = $when->rrule_index;
```

## Reference

### DrupalDateTime API reference

from https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Datetime%21DrupalDateTime.php/class/DrupalDateTime/9.4.x

Extends class `DateTimePlus`.

This class extends the basic component and adds in Drupal-specific handling, like translation of the format() method.

Static methods in base class can also be used to create `DrupalDateTime` objects. For example:

```php
 DrupalDateTime::createFromArray(['year' => 2010, 'month' => 9, 'day' => 28]);
```

### PHP Date format strings:
<https://www.php.net/manual/en/datetime.format.php#:~:text=format%20parameter%20string-,format,-character>

### UTC

From <https://en.wikipedia.org/wiki/Coordinated_Universal_Time>

Coordinated Universal Time or UTC is the primary time standard by which the world regulates clocks and time. It is within about 1 second of mean solar time at 0° longitude (at the IERS Reference Meridian as the currently used prime meridian) such as UT1 and is not adjusted for daylight saving time. It is effectively a successor to Greenwich Mean Time (GMT).

### Unix Timestamps

From https://www.unixtimestamp.com/ - The unix time stamp is a way to track time as a running total of seconds. This count starts at the Unix Epoch on January 1st, 1970 at UTC. Therefore, the unix time stamp is merely the number of seconds between a particular date and the Unix Epoch. It should also be pointed out (thanks to the comments from visitors to this site) that this point in time technically does not change no matter where you are located on the globe. This is very useful to computer systems for tracking and sorting dated information in dynamic and distributed applications both online and client side.

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://selwynpolit.github.io/d9book/index.html">Drupal at your fingertips</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://www.drupal.org/u/selwynpolit">Selwyn Polit</a> is licensed under <a href="http://creativecommons.org/licenses/by/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY 4.0<img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1"><img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1"></a></p>

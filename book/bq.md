- [Batch Processing Using the Batch API](#batch-processing-using-the-batch-api)
  - [Using the Batch API with a form](#using-the-batch-api-with-a-form)
  - [Using the Batch API from a controller](#using-the-batch-api-from-a-controller)
  - [Using the Batch API with hook_update](#using-the-batch-api-with-hook_update)
    - [Static functions are required](#static-functions-are-required)
  - [Looking at the source](#looking-at-the-source)
  - [Useful links](#useful-links)
- [Queue System](#queue-system)

# Batch Processing Using the Batch API

The Batch API provides a very useful set of functions which let you do
work by breaking it into pieces to avoid php timeouts etc. Usually,
you'll do this by creating a group of node id's to be processed and use
the batch API to process those chunks of ids.

In addition, you create a function to handle things once all the chunks
are complete. You can also give the Batch API a bunch of work to do and
have it figure out for itself when it is finished.

Also it is useful that the Batch API uses the Drupal Queue system
allowing it to pick up where it left off in case of problems.

You can use the Batch API in controllers, forms, hook updates and in
Drush commands. The implementation is slightly different as you can see
in the examples.

Most often you start a batch from a form where you fill in some options
and click a button. In the case of a controller, the batch runs when the
browser is pointed at a URL. Drush commands are typed in the terminal.

## Using the Batch API with a form

This example will replace a multivalue field with some new values
processing 10 nodes at a time. The decision to process 10 at a time is
arbitrary, but be aware that the more nodes you process at a time the
more chance of a batch failing.

The form example can be accessed at
<https://d9book.ddev.site/batch-examples/batchform>

View source at at: `web/modules/custom/batch_examples/src/Form/BatchForm.php`

Here is a simple form with a button used to kick off the batch
operation.

```php
/**
 * {@inheritdoc}
 */
public function buildForm(array $form, FormStateInterface $form_state) {

  $form['message'] = [
    '#markup' => $this->t('Click the button below to kick off the batch!'),
    ];

  $form['actions'] = [
    '#type' => 'actions',
  ];
  $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => $this->t('Run Batch'),
  ];

  return $form;
}
```

The `submitForm()` method calls `updateEventPresenters()`.

```php
/**
 * {@inheritdoc}
 */
public function submitForm(array &$form, FormStateInterface $form_state) {
  $this->updateEventPresenters();
  $this->messenger()->addStatus($this->t('The message has been sent.'));
  $form_state->setRedirect('<front>');
}
```

The route is:

```
batch_examples.batch:
  path: '/batch-examples/batchform'
  defaults:
    _title: 'Batch Form'
    _form: 'Drupal\batch_examples\Form\BatchForm'
  requirements:
    _permission: 'access content'
```

Here is the `updateEventPresenters()` method. Notice the `$operations`
array which contains the function to call to do the work of each batch
as well as the list of nids to process.

```php
function updateEventPresenters() {
  $query = \Drupal::entityQuery('node')
    ->condition('status', 1)
    ->condition('type', 'event')
    ->sort('title', 'ASC')
    ->accessCheck(TRUE);
  $nids = $query->execute();

  // Create batches.
  $chunk_size = 10;
  $chunks = array_chunk($nids, $chunk_size);
  $num_chunks = count($chunks);

  // Submit batches.
  $operations = [];
  for ($batch_id = 0; $batch_id < $num_chunks; $batch_id++) {
    $operations[] = [
      '\Drupal\batch_examples\Form\BatchForm::exampleProcessBatch',
      [
        $batch_id+1,
        $chunks[$batch_id]],
    ];
  }
  $batch = [
    'title' => $this->t("Updating Presenters"),
    'init_message' => $this->t('Starting to process events.'),
    'progress_message' => $this->t('Completed @current out of @total batches.'),
    'finished' => '\Drupal\batch_examples\Form\BatchForm::batchFinished',
    'error_message' => $this->t('Event processing has encountered an error.'),
    'operations' => $operations,
  ];
  batch_set($batch);
}

```

Here is the method which actually does the work. Most of the code is for
information reporting. The actual work is in the `foreach $nids as $nid`
loop:

```php
  public static function exampleProcessBatch(int $batch_id, array $nids, array &$context) {
    if (!isset($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['current_node'] = 0;
      $context['sandbox']['max'] = 0;
    }
    if (!isset($context['results']['updated'])) {
      $context['results']['updated'] = 0;
      $context['results']['skipped'] = 0;
      $context['results']['failed'] = 0;
      $context['results']['progress'] = 0;
    }

    // Keep track of progress.
    $context['results']['progress'] += count($nids);
    $context['results']['process'] = 'Import request files';
    // Message above progress bar.
    $context['message'] = t('Processing batch #@batch_id batch size @batch_size for total @count items.',[
      '@batch_id' => number_format($batch_id),
      '@batch_size' => number_format(count($nids)),
      '@count' => number_format($context['sandbox']['max']),
    ]);

    foreach ($nids as $nid) {
      $filename = "";
      /** @var \Drupal\node\NodeInterface $event_node */
      $event_node = Node::load($nid);
      if ($event_node) {
        $array =  ["Mary Smith", "Fred Blue", "Elizabeth Queen"];
        shuffle($array);
        $event_node->field_presenter = $array;
        $event_node->save();
      }
    }
}

```

The Form API will take care of getting the batches executed. If you
aren't using a form, you use code like the line shown below. For this
method, you specify any valid alias and it will be displayed after the
batch completes.

```
return batch_process(\'node/177467\');
```

Notice also you can set up a `$batch` array with a title and a progress
message with some variables that will get displayed.

You specify a `finished` index which identifies a function to call after
the batch is finished processing as in the example below.

```
'finished' => '\Drupal\batch_examples\Form\BatchForm::batchFinished',
```

Here is the `batchFinished()` method which displays and logs the results.

```php
/**
 * Handle batch completion.
 *
 * @param bool $success
 *   TRUE if all batch API tasks were completed successfully.
 * @param array $results
 *   An array of processed node IDs.
 * @param array $operations
 *   A list of the operations that had not been completed.
 * @param string $elapsed
 *   Batch.inc kindly provides the elapsed processing time in seconds.
 */
public static function batchFinished(bool $success, array $results, array $operations, string $elapsed) {
  $messenger = \Drupal::messenger();
  if ($success) {
    $messenger->addMessage(t('@process processed @count nodes, skipped @skipped, updated @updated, failed @failed in @elapsed.', [
      '@process' => $results['process'],
      '@count' => $results['progress'],
      '@skipped' => $results['skipped'],
      '@updated' => $results['updated'],
      '@failed' => $results['failed'],
      '@elapsed' => $elapsed,
    ]));
    \Drupal::logger('d9book')->info(
      '@process processed @count nodes, skipped @skipped, updated @updated, failed @failed in @elapsed.', [
      '@process' => $results['process'],
      '@count' => $results['progress'],
      '@skipped' => $results['skipped'],
      '@updated' => $results['updated'],
      '@failed' => $results['failed'],
      '@elapsed' => $elapsed,
    ]);
  }
  else {
    // An error occurred.
    // $operations contains the operations that remained unprocessed.
    $error_operation = reset($operations);
    $message = t('An error occurred while processing %error_operation with arguments: @arguments', [
      '%error_operation' => $error_operation[0],
      '@arguments' => print_r($error_operation[1], TRUE),
    ]);
    $messenger->addError($message);
  }
  // Optionally redirect back to the form.
  return new RedirectResponse('/batch-examples/batchform');
}

```

## Using the Batch API from a controller

The Batch API is often used in connection with forms. If you\'re using a
page callback, you will need to setup all the items, submit them to the
batch API and then call `batch_process()` with a url as the argument. 

`return batch_process('node/1');`

After the batch is complete, Drupal will send you to that url. E.g.


More at
<https://api.drupal.org/api/drupal/core%21includes%21form.inc/group/batch/10.0.x>


In this example of a processing function you can see error handling,
logging and tracking while retrieving files from a remote source. This
is fairly common when moving data between systems. The rest of the code
is almost identical to the previous example.

```php
public static function fileImportProcessBatch(int $batch_id, array $nids, array &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['current_node'] = 0;
    $context['sandbox']['max'] = 0;
  }
  if (!isset($context['results']['updated'])) {
    $context['results']['updated'] = 0;
    $context['results']['skipped'] = 0;
    $context['results']['failed'] = 0;
    $context['results']['progress'] = 0;
  }
  // Total records to process for all batches.
  if (empty($context['sandbox']['max'])) {
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'opinion_request');
    $total_nids = $query->execute();
    $context['sandbox']['max'] = count($total_nids);
  }

  // Keep track of progress.
  $context['results']['progress'] += count($nids);
  $context['results']['process'] = 'Import request files';
  // Message above progress bar.
  $context['message'] = t('Processing batch #@batch_id batch size @batch_size for total @count items.',[
    '@batch_id' => number_format($batch_id),
    '@batch_size' => number_format(count($nids)),
    '@count' => number_format($context['sandbox']['max']),
  ]);

  $fileRepository = \Drupal::service('file.repository');
  foreach ($nids as $nid) {
    $filename = "";
    $request_node = Node::load($nid);
    if ($request_node) {
      $file_id = $request_node->get('field_request_file')->target_id;
      if (!empty($file_id)) {
        // confirm that the file exists.
        $file = File::load($file_id);
        if ($file) {
          $uri = $file->getFileUri();
          if (file_exists($uri)) {
            $context['results']['skipped']++;
            continue;
          }
        }
      }

      //Skip retrieving file if there is no request date.
      $request_date = $request_node->get('field_request_date')->value;
      if (empty($request_date)) {
        $context['results']['skipped']++;
        continue;
      }
      $source_url = $request_node->get('field_pdf_file_with_path')->value;
      if ($source_url == "missing") {
        $context['results']['skipped']++;
        continue;
      }
      if (!empty($source_url)) {
        $filename = basename($source_url);
        if (empty($filename)) {
          \Drupal::logger('oag_opinions')
            ->error('file_import - Error retrieving file - invalid filename in' . $source_url);
          $context['results']['skipped']++;
          continue;
        }
        $file_contents = @file_get_contents($source_url);
        if ($file_contents === FALSE) {
          \Drupal::logger('oag_opinions')
            ->error('file_import - Error retrieving file ' . $source_url);
          \Drupal::messenger()->addError(t('Error retrieving file %filename.',['%filename' => $source_url]), FALSE);
          $context['results']['failed']++;
          continue;
        }
        $destination = "public://" . $filename;
        $file = null;
        try {
          $file = $fileRepository->writeData($file_contents, $destination, FileSystemInterface::EXISTS_REPLACE);
        }
        catch (FileException $e) {
          \Drupal::logger('oag_opinions')->error('file_import - Error saving file ' . $destination);
          \Drupal::messenger()->addError(t('Error saving file %filename', ['%filename' => $destination]), FALSE);
          $context['results']['failed']++;
          continue;
        }

        if (!$file) {
          $context['results']['failed']++;
          continue;
        }
        $fid = $file->id();
        $request_node->set('field_request_file', $fid);
        //$request_node->field_request_file->target_id = $fid;
        $request_node->save();
        $context['results']['updated']++;
      }
    }
  }
}

```

Here is the code that creates the batches and submits them.

```php
public function summaryImport()
{
  $this->summaryCreateBatches();
  return batch_process('/admin/content');
}
```


## Using the Batch API with hook_update

Let's say you want to update the default value of a field for all nodes
using the Batch API and hook_update_N.

From
<https://www.thirdandgrove.com/insights/using-batch-api-and-hookupdaten-drupal-8/>

Also

<https://api.drupal.org/api/examples/batch_example%21batch_example.install/function/batch_example_update_8001/8.x-1.x>



### Static functions are required

Any batch functions must be public static functions and any
functions calling those must be explicitly namespaced like:

```php
$nid = \Drupal\dir_salesforce\Controller\DirSalesforceController::lookupCommodityItem($commodity_item_id);
```

You can't use `$this->my_function` even if they are in the same class.
Grab the namespace from the top of the php file you are using. In this
case:

```php
namespace Drupal\dir_salesforce\Controller;
```

You can however refer to the functions with `self::` e.g.

```php
$node_to_update_dir_contact_nid = self::getFirstRef($node_to_update, 'field_sf_dir_contact_ref');
```


```php
* Example:
* @code
* $batch = array(
*   'title' => t('Exporting'),
*   'operations' => array(
*     array('my_function_1', array($account->id(), 'story')),
*     array('my_function_2', array()),
*   ),
*   'finished' => 'my_finished_callback',
*   'file' => 'path_to_file_containing_myfunctions',
* );
* batch_set($batch);
* // Only needed if not inside a form _submit handler.
* // Setting redirect in batch_process.
* batch_process('node/1');
* @endcode

```





## Looking at the source
Here is the link to the source for the Batch API.  As always looking at the source is the definitive way to understand how anything works.  It is really well commented.

From
<https://git.drupalcode.org/project/drupal/-/blob/10.0.x/core/includes/form.inc>,
there is an example batch which calls two different functions:
my_function_1 and my_function_2. Note for my function 1, the arguments
are just separated by commas. Also it is interesting to note that they
call batch_process('node/1') but that could be any valid url alias e.g.
'/admin/content'.

So here are the arguments for my_function_1:

```
* function my_function_1($uid, $type, &$context) {
```

You call the batch finished function with the following arguments:

```php
/**
 * Handle batch completion.
 *
 * @param bool $success
 *   TRUE if all batch API tasks were completed successfully.
 * @param array $results
 *   An array of processed node IDs. – or whatever you put in $context['results'][]
 * @param array $operations
 *   A list of the operations that had NOT been completed.
 * @param $elapsed
 *   Elapsed time for processing in seconds.
 */
public static function batchFinished($success, array $results, array $operations, $elapsed) {

```

The results are displayed:

```php
$messenger = \Drupal::messenger();
if ($success) {
  $messenger->addMessage(t('Processed @count nodes in @elapsed.', [
    '@count' => count($results),
    '@elapsed' => $elapsed,
  ]));
}
```

You can load the `$results` array with all sorts of interesting data such
as:

```php
$context['results']['skipped'] = $skipped;
$context['results']['updated'] = $updated;
```

Batch API provides a nice way to display detailed results using code
like:

```php
$messenger->addMessage(t('Processed @count nodes, skipped @skipped, updated @updated in @elapsed.', [
  '@count' => $results['nodes'],
  '@skipped' => $results['skipped'],
  '@updated' => $results['updated'],
  '@elapsed' => $elapsed,
]));

```


Which produce the following output:

`Processed 50 nodes, skipped 45, updated 5 in 3 sec.`

You can display an informative message above the progress bar like this.

I filled in the `$context[‘sandbox’][‘max’]` with a value (but I could have used `$context[‘sandbox’][‘whole-bunch’]` or any variable here)

```php
$context['sandbox']['max'] = count($max_nids);
```


Using number_format puts commas in the number if it is over 1,000.

```php
$context['message'] = t('Processing total @count nodes',
  ['@count' => number_format($context['sandbox']['max'])]
);
```

Or

```php
$operation_details = 'Yoyoma';
$id = 9;
$context['message'] = t('Running Batch "@id" @details',
  ['@id' => $id, '@details' => $operation_details]
);
```


You do have to provide your own info for the variables.

You can also stop the batch engine yourself with something like this. If
you don't know beforehand how many records you need to process, you
might use this.

```php
// Inform the batch engine that we are not finished,
// and provide an estimation of the completion level we reached.
if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
  $context['finished'] = ($context['sandbox']['progress'] >= $context['sandbox']['max']);
}
```


## Useful links

You can read more about batch processing at these sites:

-   <https://www.weareaccess.co.uk/blog/2016/07/smack-my-batch-batch-processing-drupal-8>

-   Highly commented source code for batch operations around line 561:
    <https://git.drupalcode.org/project/drupal/-/blob/10.0.x/core/includes/form.inc>
    (or search for "batch operations")





# Queue System

Useful link: kinda incomplete example:
<https://www.alansaunders.co.uk/blog/queues-drupal-8-and-9>

This may be a good example to build on:
<http://karimboudjema.com/en/drupal/20180807/create-queue-controller-drupal8>

Quick example of submitting work to the queue from
<http://www.tothenew.com/blog/how-to-implement-queue-workerapi-in-drupal-8/>
This is smarter than using hook_cron????

`use Drupal\Core\Queue\QueueInterface;`

This defines the createItem(), createQueue(), deleteItem() etc. Not sure
where exactly they are implemented.. TODO: figure out where?

```php
/**
 * {@inheritdoc}
 */
 public function submitForm(array &$form, FormStateInterface $form_state) {
 /** @var QueueFactory $queue_factory */
 $queue_factory = \Drupal::service('queue');
 /** @var QueueInterface $queue */
 $queue = $queue_factory->get('email_processor');
 $item = new \stdClass();
 $item->username = $form_state->getValue('name');
 $item->email = $form_state->getValue('email');
 $item->query = $form_state->getValue('query');
 $queue->createItem($item);
 }
}
```

You will then have to create a Queue Worker which implements
ContainerFactoryPluginInterface and in the processItem() it processes a
single item from the queue.

See the website for the code...

Then you'll need a cronEventProcessor which in annotation tells cron how
often to run the job:

```php
<?php
/**
 *
 * PHP Version 5
 */
 
namespace Drupal\my_module\Plugin\QueueWorker;
 
/**
 *
 * @QueueWorker(
 * id = "email_processor",
 * title = "My custom Queue Worker",
 * cron = {"time" = 10}
 * )
 */
class CronEventProcessor extends EmailEventBase {
 
}
```
[home](../index.html)

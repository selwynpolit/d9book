<?php

namespace Drupal\batch_examples\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a Batch Examples form.
 */
class BatchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'batch_examples_batch';
  }

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

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->updateEventPresenters();
    $this->messenger()->addStatus($this->t('The batch has completed.'));
    //$form_state->setRedirect('<front>');
  }

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
//    // Total records to process for all batches.
//    if (empty($context['sandbox']['max'])) {
//      $query = \Drupal::entityQuery('node')
//        ->condition('status', 1)
//        ->condition('type', 'event')
//        ->accessCheck(TRUE);
//      $total_nids = $query->execute();
//      $context['sandbox']['max'] = count($total_nids);
//    }

    // Keep track of progress.
    $context['results']['progress'] += count($nids);
    $context['results']['process'] = 'Replace Presenters';
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

}

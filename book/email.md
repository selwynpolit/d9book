---
title: Email
---

# Email
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=email.md)

## Send email

It is relatively easy to send email from Drupal once you see the relationship between the calling function and `hook_mail()`, but it is a little counter-intuitive that you have to implement hook_mail() in the first place considering the call to `$mail_manager->mail()`.

So first create a mail function in your module, then add the hook_mail() function. Here is an example of how to do this.

```php
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Function to send an email in your module.
 *
 * @param string $to
 *   The recipient of the email.
 * @param array $params
 *   An array of parameters to use in the email.
 * @param string $subject
 *   The subject of the email.
 * @param string $body
 *   The body of the email.
 *
 * @return bool
 *   TRUE if the email was sent successfully, FALSE otherwise.
 */
function sendMyEmail($to, $params, $subject, $body) {
  // Get the language manager service.
  $language_manager = \Drupal::service('language_manager');

  // Get the default language.
  $default_language = $language_manager->getDefaultLanguage()->getId();

  // Get the mail manager service.
  $mail_manager = \Drupal::service('plugin.manager.mail');

  // Set up the email parameters.
  $module = 'my_module';
  $key = 'my_email_key';

  // Get the email headers.
  $headers = [
    'Content-Type' => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
    'From' => 'sender@example.com',
    'Reply-To' => 'reply-to@example.com',
  ];

  // Set up the email body.
  $body_params = [
    'message' => $params['message'],
    'name' => $params['name'],
  ];

  // Send the email.
  $result = $mail_manager->mail($module, $key, $to, $default_language, $body_params, NULL, TRUE, $headers);

  // Return the result.
  return $result['result'];
}
```

::: tip Note
Don\`t forget to add the `hook_email()` to your `.module` file or no email will ever be sent!
:::

```php
/**
 * Implements hook_mail().
 */
function my_module_mail($key, &$message, $params) {
  switch ($key) {
    case 'my_email_key':
      $message['subject'] = t('My Email Subject');
      $message['body'][] = $params['message'];
      $message['body'][] = t('From: @name', ['@name' => $params['name']]);
      break;
  }
}
```


## General send email function with logging

Here is an example of a general send email function with logging.  It is used to send an email and log the result.  It also checks to see if the email is valid before sending it.

```php
  /**
   * Send email notification.
   *
   * @param array $to_email_addresses
   *   Array of email addresses.
   * @param string|null $from
   *   From email address.
   * @param string $subject
   *   Message subject.
   * @param string $message_body
   *   Message in plain text.
   *
   * @return array
   *   The render array for screen display.
   */
  public function sendEmail(array $to_email_addresses, string|null $from, string $subject, string $message_body): array {
    /*
     * Notifications will only be sent from prod environment.
     * Configured in settings.php (or settings.local.php)
     * For development, use this in settings.local.php:
     * $config['tabcblah.status_change_notifications']
     * ['send_notifications'] = TRUE;
     */
    $send_notifications = \Drupal::config('tea_teks.status_change_notifications')->get('send_notifications');

    if (is_null($from)) {
      $from = \Drupal::config('system.site')->get('mail');
    }
    $results = [];
    $email_validator = \Drupal::getContainer()->get('email.validator');
    if (!$send_notifications) {
      $results[0]['status'] = 'success';
      return $results;
    }
    foreach ($to_email_addresses as $to_email_address) {
      // Check if it's a valid email address.
      if (!$email_validator->isValid($to_email_address)) {
        continue;
      }
      $language_code = \Drupal::config('system.site')->get('langcode');
      // Get the mail manager service.
      $mail_manager = \Drupal::service('plugin.manager.mail');
      $headers = ['Content-Type' => 'text/html; charset=UTF-8'];
      $params = [
        'headers' => $headers,
        'from' => $from,
        'subject' => $subject,
        'body' => $message_body,
      ];

      // Send the email.
      $response = $mail_manager->mail('tea_teks', 'tea_teks_public_comment_error_form_notification', $to_email_address, $language_code, $params, NULL, TRUE);
      $response_message = t('Email to %to, from: %from, subject: %subject: Send result: %result.', [
        '%result' => $response['result'] ? t('success') : t('fail'),
        '%subject' => $subject,
        '%from' => $from,
        '%to' => implode('; ', $to_email_addresses),
      ]);
      $results[] = [
        'status' => $response['result'] ? t('success') : t('fail'),
        'message' => $response_message,
      ];
      if (!$response['result']) {
        \Drupal::logger('tea_teks_srp')->error($response_message);
      }
    }
    return $results;
  }
```

::: tip Note
Don\'t forget to add the hook_mail in your `.module` file.  Here it is in the `tea_teks module` file.
:::

```php
/**
 * Implements hook_mail().
 */
function tea_teks_mail($key, &$message, $params) {

  $site_name = \Drupal::config('system.site')->get('name');
  $site_mail = \Drupal::config('system.site')->get('mail');

  switch ($key) {
    case 'tea_teks_public_comment_error_form_notification':
      $message['headers']['Reply-To'] = $site_mail;
//      $message['headers']['Content-Type'] = 'text/html';
//      $message['headers']['Content-Type']= 'text/html; charset=utf-8; format=flowed; delsp=yes';
      $message['headers']['Content-Type'] = 'text/plain; charset=utf-8';
      $message['headers']['From'] = $site_name .'<' . $site_mail . '>';
      $message['subject'] = t('@subject', array('@subject' => $params['subject']));
      $message['body'][] = Xss::filter($params['body'], ['a', 'b', 'br', 'em', 'i', 'strong', 'u', 'p']);
      break;
    case 'tea_teks_publisher_status_notification':
    default:
      $message['headers']['Reply-To'] = $site_mail;
      $message['headers']['Content-Type'] = 'text/html';
      $message['headers']['From'] = $site_name .'<' . $site_mail . '>';
      $message['subject'] = t('@subject', array('@subject' => $params['subject']));
      $message['body'][] = Xss::filter($params['body']);
      break;
  }
}
```


## Using tokens in hook_mail

Here is an example in a hook_mail call where tokens are used:

```php
/**
 * Implements hook_mail().
 */
function hello_world_mail($key, &$message, $params) {
  switch ($key) {
    case 'hello_world_log':
      $message['from'] = \Drupal::config('system.site')->get('mail');
      $message['subject'] = t('There is an error on your website');
      $message['body'][] = $params['message'];
      if (isset($params['user'])) {
        $user_message = 'The user that was logged in: [current-user:name]';
        $message['body'][] = \Drupal::token()->replace($user_message, ['current-user' => $params['user']]);
      }

      break;
  }
}
```

## Useful helper functions

See docroot/core/lib/Drupal/Core/Mail/MailFormatHelper.php for more.

```php
// Break a body field into paragraphs.
$message['body'] = implode("\n\n", $message['body']);

// Transforms an HTML string into plain text, preserving its structure.
$message['body'] = MailFormatHelper::htmlToText($message['plaintext']);

// Performs format=flowed soft wrapping for mail (RFC 3676).
$message['body'] = MailFormatHelper::wrapMail($message['plaintext']);
```

and here are a few more.

* `MailFormatHelper::htmlToText()` - Transforms an HTML string into plain text, preserving its structure.
* `MailFormatHelper::wrapMailLine()` - Wraps words on a single line.
* `MailFormatHelper::htmlToMailUrls()` - Keeps track of URLs and replaces them with placeholder tokens.
* `MailFormatHelper::htmlToTextClean()` - Replaces non-quotation markers from a piece of indentation with spaces.
* `MailFormatHelper::htmlToTextPad()` - Pads the last line with the given character.

## Troubleshooting

### Mail sends but no subject or body
This means you either forgot to create the hook_mail module or that your key doesn't match.  Try changing the key in your hook mail to be a `default` and see if that works

```php
function tea_teks_mail($key, &$message, $params) {

  $site_name = \Drupal::config('system.site')->get('name');
  $site_mail = \Drupal::config('system.site')->get('mail');
  switch ($key) {
    case 'tea_teks_public_comment_error_form_notification':
      $message['headers']['Reply-To'] = $site_mail;
      $message['headers']['Content-Type'] = 'text/html';
      $message['headers']['From'] = $site_name .'<' . $site_mail . '>';
      $message['subject'] = t('@subject', array('@subject' => $params['subject']));
      break;
    default:
      $message['headers']['Reply-To'] = $site_mail;
      $message['headers']['Content-Type'] = 'text/html';
      $message['headers']['From'] = $site_name .'<' . $site_mail . '>';
      $message['subject'] = t('@subject', array('@subject' => $params['subject']));
      $message['body'][] = Xss::filter($params['body']);
      break;
  }
}

```

## Reference
- [Sending html mails in Drupal 8/9 programmatically An example Drupal module including Twig template by Joris Snoek - August 2020](https://www.lucius.digital/en/blog/sending-html-mails-drupal-89-programmatically-example-drupal-module-including-twig-template)
- [Sending Emails Using OOP and Dependency Injection in Drupal 8, 9 By Alex Novak - November 2020.](https://www.drupalcontractors.com/blog/2020/11/09/sending-emails-using-oop-dependency-injection-drupal/)
- [How email works in Drupal - updated July 2021](https://www.drupal.org/docs/contributed-modules/mime-mail/how-email-works-in-drupal)
- [Sendgrid Integration Drupal module](https://www.drupal.org/project/sendgrid_integration)
- [How to send a mail programmatically in Drupal 8 by Jimmy Sebastian - Mar 2022](https://www.zyxware.com/articles/5504/drupal-8-how-to-send-a-mail-programmatically-in-drupal-8)
- [Sending Emails with Drupal Symfony Mailer - Oct 2023](https://jigarius.com/blog/drupal-symfony-mailer)

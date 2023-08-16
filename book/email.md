---
layout: default
title: Email
permalink: /email
last_modified_date: '2023-08-16'
---

# Email
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=email.md)

---

## Send email

Function to send an email in your module with a hook_mail() to set the parameters.

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
function send_my_email($to, $params, $subject, $body) {
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

* MailFormatHelper::htmlToText() - Transforms an HTML string into plain text, preserving its structure.
* MailFormatHelper::wrapMailLine() - Wraps words on a single line.
* MailFormatHelper::htmlToMailUrls() - Keeps track of URLs and replaces them with placeholder tokens.
* MailFormatHelper::htmlToTextClean() - Replaces non-quotation markers from a piece of indentation with spaces.
* MailFormatHelper::htmlToTextPad() - Pads the last line with the given character.

## Reference
- [Sending html mails in Drupal 8/9 programmatically An example Drupal module including Twig template by Joris Snoek - August 2020](https://www.lucius.digital/en/blog/sending-html-mails-drupal-89-programmatically-example-drupal-module-including-twig-template)
- [Sending Emails Using OOP and Dependency Injection in Drupal 8, 9 By Alex Novak - November 2020.](https://www.drupalcontractors.com/blog/2020/11/09/sending-emails-using-oop-dependency-injection-drupal/)
- [How email works in Drupal - updated July 2021](https://www.drupal.org/docs/contributed-modules/mime-mail/how-email-works-in-drupal)
- [Sendgrid Integration Drupal module](https://www.drupal.org/project/sendgrid_integration)


---

<script src="https://giscus.app/client.js"
        data-repo="selwynpolit/d9book"
        data-repo-id="MDEwOlJlcG9zaXRvcnkzMjUxNTQ1Nzg="
        data-category="Q&A"
        data-category-id="MDE4OkRpc2N1c3Npb25DYXRlZ29yeTMyMjY2NDE4"
        data-mapping="title"
        data-strict="0"
        data-reactions-enabled="1"
        data-emit-metadata="0"
        data-input-position="bottom"
        data-theme="preferred_color_scheme"
        data-lang="en"
        crossorigin="anonymous"
        async>
</script>

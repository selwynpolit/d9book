---
layout: default
title: Email
permalink: /email
last_modified_date: '2023-04-13'
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

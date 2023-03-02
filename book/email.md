# Email

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

- [Email](#email)
  - [Send email](#send-email)

![visitors](https://page-views.glitch.me/badge?page_id=selwynpolit.d9book-gh-pages-email)

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

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

<h3 style="text-align: center;">
<a href="/d9book">home</a>
</h3>

---

<p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://selwynpolit.github.io/d9book/index.html">Drupal at your fingertips</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://www.drupal.org/u/selwynpolit">Selwyn Polit</a> is licensed under <a href="http://creativecommons.org/licenses/by/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY 4.0<img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/cc.svg?ref=chooser-v1"><img style="height:22px!important;margin-left:3px;vertical-align:text-bottom;" src="https://mirrors.creativecommons.org/presskit/icons/by.svg?ref=chooser-v1"></a></p>

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
---
title: Security
---

# Security
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=security.md)

## Overview

Drupal is a highly secure platform mostly due to the tireless efforts of the [security team](https://www.drupal.org/drupal-security-team).  


## Sanitizing output to avoid Cross Site Scripting (XSS) attacks

The Twig theme engine now auto escapes everything by default. That means, every string printed from a Twig template (e.g. anything between <code v-pre>{{ }}</code>) gets automatically sanitized if no filters are used.

[See Filters - Modifying Variables In Twig Templates](https://www.drupal.org/node/2357633) for the Twig filters available in Drupal. Notably, watch out for the "raw" filter, which does not escape output. Only use this when you are certain the data is trusted.

When rendering attributes in Twig, make sure that you wrap them with double or single quotes. For example this is safe: 
```twig
class="{{ class }}"
```
This is not safe.
```twig
class={{ class }}
```

In order to take advantage of Twig’s automatic escaping (and avoid safe markup being escaped) ideally all HTML should be outputted from Twig templates.

## .htaccess magic

### Disallowing access to users coming from a domain

Requests from specific domains can be blocked by adding the following to `.htaccess`:
```
RewriteCond %{HTTP_REFERER} domain-name\.com [NC] 
RewriteRule .* - [F]
```

For multiple domains, use something similar to the following:
```
RewriteCond %{HTTP_REFERER} domain-one\.com [NC,OR] 
RewriteCond %{HTTP_REFERER} domain-two\.com RewriteRule .* - [F]
```

### Blocking core Drupal pages
Files such as CHANGELOG.txt can be used to quickly identify security vulnerabilities in your Drupal installation to a malicious script or user. While there are a number of ways to identify the version of Drupal that you are running, one quick addition to your .htaccess file can make it slightly le obvious.

```
# Various alias rules 
Redirect 404 /CHANGELOG.txt 
Redirect 404 /COPYRIGHT.txt 
Redirect 404 /cron.php 
Redirect 404 /INSTALL.mysql.txt 
Redirect 404 /INSTALL.pgsql.txt 
Redirect 404 /INSTALL.sqlite.txt 
Redirect 404 /INSTALL.txt 
Redirect 404 /install.php 
Redirect 404 /LICENSE.txt 
Redirect 404 /MAINTAINERS.txt 
Redirect 404 /PATCHES.txt 
Redirect 404 /README.txt 
Redirect 404 /update.php 
Redirect 404 /UPGRADE.txt 
Redirect 404 /web.config
```


### Blocking file resources from all but a handful of sites

You may want to keep a specific directory from being accessed by the general public, unless it's being pulled by a particular website. This example shows blocks requests to the /sites/default/files directory unless the request comes from www?, prod-kb, or the kb subdomains of example.com.

```
RewriteCond %{REQUEST_URI} ^/sites/default/files 
RewriteCond %{HTTP_REFERER} !^http://prod-kb.example.com [NC] 
RewriteCond %{HTTP_REFERER} !^http://kb.example.com [NC] 
RewriteCond %{HTTP_REFERER} !^http://(www.)?example.com [NC] RewriteRule .* - [F]
```


### Time-based blocks
If you are only allowed to expose your website for a specific time period, you can do that. This condition and rule blocks access until 4 PM.

```
RewriteCond %{TIME_HOUR} ^16$
RewriteRule ^.*$ - [F,L]
```

### Blocking HTTP commands
You may not want to allow certain types of commands to be proceed by your site.

This blocks any HTTP request that is not a GET or a POST request.

```
RewriteCond %{REQUEST_METHOD} !^(GET|POST) 
RewriteRule .* - [F]
```

### Block specific user agents
If your website is the victim of a DDoS attack, and you want to block a group of IP addresses using the same User Agent, the following code may be helpful. Replace the UserAgent with the name of the agent you want to block:

```
RewriteCond %{HTTP_USER_AGENT} UserAgent 
RewriteRule .* - [F,L]
```

You can also block more than one User Agent at a time with the `[OR]` ('or next condition') flag, and the `[NC]` ('no case') flag renders the string case insensitive. Here are some examples of some user-agents with properly escaped regexes:

```
RewriteCond %{HTTP_USER_AGENT} Baiduspider [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} HTTrack [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} Yandex [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} Scrapy [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} Mozilla/5\.0\ \(compatible;\ Yahoo [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} AppleNewsBot [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} Googlebot [NC,OR] 
RewriteCond %{HTTP_USER_AGENT} Mozilla/5\.0\ \(compatible;\ YandexBot [NC] 
RewriteRule .* - [F,L]
Important
```

Properly escape characters inside your regex (regular expressions) to avoid website errors.

`HTTP_USER_AGENT` can use regex as an argument. As seen in the example above, many User Agents will require regex due to the complexity of their name. Rather than creating the rule manually, websites such as [https://www.regex-escape.com/regex-escaping-online.php](https://www.regex-escape.com/regex-escaping-online.php) can help construct a properly-escaped regex quickly.

**How to test that the block is working**

Test that the site is responding:

```
curl -H "host:www.url_you_are_testing.url http://localhost/
```

Test that the user-agent (Pcore as an example) is indeed blocked:

```
curl -H "host:www.url_you_are_testing.url" -H "user-agent:Pcore-HTTP/v0.25.0" http://localhost.com/
```


### Block traffic from robot crawlers

While a robot crawler may not technically be an attack, some crawlers can cause real problems. You can use this when the robots do not obey the robots.txt file, or if you need an immediate block, because robots.txt is generally not fetched immediately by crawlers.

```
RewriteCond %\{HTTP_REFERER\} ^$
RewriteCond %\{HTTP_USER_AGENT\} "<exact_name_for_the_bot>"
RewriteRule ^(.*)$ - [F,L]
```

### Blocking hotlinks

The last thing most website owners want is other websites stealing their content, or worse - hotlinking to their images and stealing their bandwidth. Here s a simple bit of code that prevents it-modify domain.com to your domain name:

```
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^http://(www\.)?domain.com/ .*$ NC
RewriteRule \.(gif|jpg|swf|flv|png)$ /feed/ R=302,L
```

## Reviewing Server logs

### Find the most frequent IP addresses

```
awk '{print $1}' access.log| sort | uniq -c | sort -nr| head
```

### Find the most frequent User Agents

```
awk -F\" '{print $6}' access.log | sort | uniq -c | sort -nr | head
```

### Find the most frequent URLs visited on your site

```
awk -F\" '{print $2}' access.log| awk -F? '{print $1}' | sort | uniq -c | sort -nr | head
```

This query strips the variables (anything after a question mark in your URL).

[Read more on Acquia.com](https://acquia.my.site.com/s/article/360013350193-Analyzing-Your-Traffic)

## General overview of harding your Drupal site

[from Acquia.com](https://acquia.my.site.com/s/article/360041130414-Harden-Drupal-sites-against-security-threats)

**Ensure up-to-date backups are safe and secure**
- Initiate a production database backup
- Download a copy of recent database backups, and keep updated copies offsite
- If possible, also take backups of the file system

**Ensure Drupal Core and Installed Modules are up to date** 
Drupal Core updates often contain security patches. Outdated, unmaintained modules often contain known security vulnerabilities. 
- Look for projects and modules covered by the Drupal Security Advisories
- Remove obsolete and unused modules
- Check for available updates under the Drupal admin console, or by using drush or composer. 

**Perform a user audit**
- Ensure permissions are restricted and implemented correctly
- Remove any old or unneeded admin or privileged accounts

If a breach has occurred or internal threat, an attacker or internal threat may have added user(s) to retain access.

- Check for any new or unexpected user accounts

**Password Checks**

Bad passwords are the most common cause of site compromise. 

- Ensure strong password requirements are enforced. A community contributed module that offers this functionality is Password Policy.
- Perform a check for bad passwords. A community contributed module that offers this functionality is [Drop the Ripper](https://www.drupal.org/project/drop_the_ripper)

**2-Factor Authentication**

- Enforce 2-factor authentication (especially for admin and/or privileged accounts) to mitigate the threat of compromised passwords.
- 
**Review Site Functionality**

- Check that file uploads are restricted to intended file extension type (e.g. Do not allow .html uploads for an image)
- Ensure any sensitive data files are uploaded to secure directories only
(e.g. Do not place personal data ( PII ) such as CVs or job applications in public 'files' directories)
- Review controls on web forms

Attackers will often target forms that generate outbound emails ( e.g. "refer a friend" or "contact-us" )

Try to keep messages generated from forms generic
Ensure CAPTCHA controls are used to prevent abuse

**Web Application Firewall ( WAF )**

If a WAF is not already in place, Acquia strongly recommend implementing one.

[Acquia Cloud Edge Protect](https://docs.acquia.com/guide/edge/) is Acquia's WAF offering.

Edge Protect provides advanced security controls to restrict and block attacker traffic before it reaches the application stack. Common attack methods are identified and blocked automatically. WAFs are extremely effective in mitigating (D)DOS attacks.

## API functions best practices

From [Writing secure code for Drupal](https://www.drupal.org/docs/8/security/drupal-8-sanitizing-output)

- Use [t()](https://api.drupal.org/api/function/t) and [\Drupal::translation()->formatPlural()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21StringTranslation%21TranslationInterface.php/function/TranslationInterface%3A%3AformatPlural/8.2.x) with placeholders to construct safe, translatable strings. (See [Translation API overview](https://www.drupal.org/docs/8/api/translation-api/overview) for more details.)
- Use [Html::escape()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21Html.php/function/Html%3A%3Aescape/8.2.x) for plain text.
- Use [Xss::filter()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21Xss.php/function/Xss%3A%3Afilter/8) for text that should allow some HTML tags.
- Use [Xss::filterAdmin()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21Xss.php/function/Xss%3A%3AfilterAdmin/10) for text entered by admin users that should allow most HTML.
- Use [UrlHelper::stripDangerousProtocols()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21UrlHelper.php/function/UrlHelper%3A%3AstripDangerousProtocols/9) or [UrlHelper::filterBadProtocol()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21UrlHelper.php/function/UrlHelper%3A%3AfilterBadProtocol/9) for checking URLs - the former can be used in conjunction with [SafeMarkup::format()](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21SafeMarkup.php/function/SafeMarkup%3A%3Aformat/8.9.x) - Oops, SafeMarkup was removed from Drupal 9.  Rather use [FormattableMarkup](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Render%21FormattableMarkup.php/class/FormattableMarkup/10)

Strings sanitized by `t()`, `Html::escape()`, `Xss::filter()` or `Xss::filterAdmin()` are automatically marked safe, as are markup strings created from render arrays via [Renderer](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Render%21Renderer.php/class/Renderer/8.4.x).

While it can also sanitize text, it's almost never correct to use [check_markup](https://api.drupal.org/api/drupal/core%21modules%21filter%21filter.module/function/check_markup/8) in a theme or module except in the context of something like a text area with an associated text format.

## Sanitizing data from text fields

Copilot suggested the following

In Drupal, you can sanitize data coming from text fields using the following methods:

1. **CheckPlain**: This function is used to sanitize a string that is meant to be output to an HTML page. It replaces special characters with their HTML entities.

```php
$sanitized_text = \Drupal\Component\Utility\Html::escape($text);
```

2. **Xss::filterAdmin**: This function is used to sanitize a string that is meant to be output to an HTML page as a part of the admin section. It allows some HTML tags that are generally used in the admin section.

```php
$sanitized_text = \Drupal\Component\Utility\Xss::filterAdmin($text);
```

3. **Xss::filter**: This function is used to sanitize a string that is meant to be output to an HTML page. It allows some basic HTML tags.

```php
$sanitized_text = \Drupal\Component\Utility\Xss::filter($text);
```

4. **Html::cleanCssIdentifier**: This function is used to sanitize a string that is meant to be used as a CSS identifier.

```php
$sanitized_text = \Drupal\Component\Utility\Html::cleanCssIdentifier($text);
```

5. **Html::getId**: This function is used to sanitize a string that is meant to be used as an HTML ID.

```php
$sanitized_text = \Drupal\Component\Utility\Html::getId($text);
```

Remember to always sanitize user input before outputting it to prevent Cross-Site Scripting (XSS) attacks.

## Html::escape
If you have html like this: 
`<script>alert(2)</script>` which will be output as a mess with these sorts of characters: `&amp;, &lt;` etc. use Html::escape to avoid this.

```php
$rows_array[] = array_map('Drupal\Component\Utility\Html::escape', $row);
```

## checkPlain

This is from the file web/modules/custom/rsvp/src/Controller/ReportController.php

Here we pass `SafeMarkup::checkPlain`` to array_map to call it on each entry in the array. The entry array looks like:

```php
$entry[‘name’]=”fred”, 
$entry[‘email’]="fred@bloggs.com"

// Calling array_map on all the entries to checkPlain them.
$rows = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $entry);
```

Here is the whole function.

```php
$rows = $this->getAllRSVPs();
foreach ($rows as $row) {
  // Sanitize each entry.
  $rows_array[] = array_map('Drupal\Component\Utility\SafeMarkup::checkPlain', $row);
}
```


::: tip Note
`check_plain()`, `filter_xss()` and such functions do change the data. When you save data into the database, you are only escaping specific characters, but you do not alter the original text. If you use `check_plain()` in database insert/updates, saving data a few times can mess up your data with a lot of `&amp;`, `&lt;`, etc. character replacements and some HTML tags stripped out.
:::




## Use the database abstraction layer to avoid SQL injection attacks

Bad practice:
Never concatenate data directly into SQL queries.

```php
// Bad practice.
\Database::getConnection()->query('SELECT foo FROM {table} t WHERE t.name = '. $_GET['user']);
```

Good Practice:

Use proper argument substitution. The database layer works on top of PHP PDO, and uses an array of named placeholders:

```php
\Database::getConnection()->query('SELECT foo FROM {table} t WHERE t.name = :name', [':name' => $_GET['user']]);
```

For a variable number of argument, use an array of arguments or use the select() method.  See examples of each below:

```php
$users = ['joe', 'poe', $_GET['user']];
\Database::getConnection()->query('SELECT f.bar FROM {foo} f WHERE f.bar IN (:users[])',  [':users[]' => $users]);
```

```php
$users = ['joe', 'poe', $_GET['user']];
$result = \Database::getConnection()->select('foo', 'f')
  ->fields('f', ['bar'])
  ->condition('f.bar', $users)
  ->execute();
```

When forming a LIKE query, make sure that you escape condition values to ensure they don't contain wildcard characters like `"%"``:

```php
db_select('table', 't')
  ->condition('t.field', '%_' . db_like($user), 'LIKE')
  ->execute();
```

Make sure that users cannot provide any operator to a query's condition. For example, this is unsafe:

```php
db_select('table', 't')
  ->condition('t.field', $user, $user_input)
  ->execute();
```
Instead, set a list of allowed operators and only allow users to use those.

`db_query`, `db_select`, and `db_like` were deprecated and removed from Drupal 9 - instead you should use a database connection object and call the query, select, and [escapeLike](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Database%21Connection.php/function/Connection%3A%3AescapeLike/9) methods on it (the parameters are the same).


## CSRF access checking

CSRF (Cross-Site Request Forgery) protection is now integrated into the routing access system and should be used for any URLs that perform actions or operations that do not use a form callback. In previous versions of Drupal, it was necessary to add a generated token as a query parameter to a URL and check this token manually in either the callback or the access callback. Now you can simply use the '_csrf_token' requirement on a route definition. Doing so will automatically add a token to the query string, and this token will be checked for you.


example:
```yaml
# example.routing.yml
  path: '/example'
  defaults:
    _controller: '\Drupal\example\Controller\ExampleController::content'
  requirements:
    _csrf_token: 'TRUE'
```
Note that, in order for the token to be added, the link must be generated using the `url_generator` service via route name rather than as a manually constructed path.

```php
$url = Url::fromRoute(
  'node_test.report',
  ['node' => $entity->id()],
  ['query' => [
    'token' => \Drupal::getContainer()->get('csrf_token')->get("node/{$entity->id()}/report")
  ]]);
```
[See API reference: CsrfTokenGenerator::get](https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Access%21CsrfTokenGenerator.php/function/CsrfTokenGenerator%3A%3Aget/9.0.x)


To validate token manually (e.g. without adding `_csrf_token: 'TRUE'` to your `mymodule.routing.yml` file) at the route destination you can use the token and value used for generating it. 

```php
// Validate $token from GET parameter.
\Drupal::getContainer()->get('csrf_token')->validate($token, "node/{$entity->id()}/report");
```

Note. regarding anonymous users. Currently the `_csrf_token` check fails for users without an active session, which includes most anonymous users. See: [#2730351: CSRF check always fails for users without a session](https://www.drupal.org/project/drupal/issues/2730351)


## Anti-Spam
The combination of the modules: [Antibot](https://www.drupal.org/project/antibot) and [Honeypot](https://www.drupal.org/project/honeypot)  make a good combination for combating site spam especially when handling anonymous facing forms or webforms.  More [at this DrupalEasy post from July 2023 ](https://www.drupaleasy.com/quicktips/honeypot-and-antibot-contrib-modules-make-great-anti-spam-team-drupal-sites)

Here is an example of a call to [Honeypot](https://www.drupal.org/project/honeypot) to protect a form:

```php
// Put this in the buildForm() function.
\Drupal::service('honeypot')->addFormProtection($form, $form_state, ['honeypot', 'time_restriction']);

```


## Modules
- [Security Kit](https://www.drupal.org/project/seckit)
- [Security Review](https://www.drupal.org/project/security_review)
- [Antibot](https://www.drupal.org/project/antibot)
- [Honeypot](https://www.drupal.org/project/honeypot)
- [Anti-Spam by Clean Talk (paid SAAS service)](https://www.drupal.org/project/cleantalk)

## Resources
- [Security on Drupal.org](https://www.drupal.org/docs/develop/security)
- [Blocking access using rewrites (Acquia.com)](https://acquia.my.site.com/s/article/360005210634-Blocking-access-using-rewrites)
- [.htaccess documentation (Acquia.com)](https://docs.acquia.com/cloud-platform/manage/htaccess/)
- [Ban module in Drupal core overview for blocking IP addresses (Acquia.com)](https://www.drupal.org/docs/8/core/modules/ban/overview)
- [Advanced Ban module](https://www.drupal.org/project/advban)
- [Restricting website access (Acquia.com)](https://docs.acquia.com/cloud-platform/arch/security/restrict/)
- [Writing secure code for Drupal - August 2022](https://www.drupal.org/docs/security-in-drupal/writing-secure-code-for-drupal)
- [Twig Filters - Modifying Variables In Twig Templates](https://www.drupal.org/node/2357633)
- [Translation API overview on Drupal.org updated August 2022](https://www.drupal.org/docs/8/api/translation-api/overview)
- [CSRF access checking on Drupal.org updated March 2023](https://www.drupal.org/docs/8/api/routing-system/access-checking-on-routes/csrf-access-checking)
- [Drupal security — a complete Drupal self-help guide to ensuring your website’s security by Kristen Pol Sep 2023](https://salsa.digital/insights/drupal-security-a-complete-drupal-self-help-guide-to-ensuring-your-websites-security)
- [Drupal defense in depth — securing Drupal at the people layer - May 2023](https://salsa.digital/insights/drupal-defense-in-depth-securing-drupal-at-the-people-layer)
- [Drupal defense in depth — securing Drupal at the process layer May 2023](https://salsa.digital/insights/Drupal-defense-in-depth-securing-drupal-at-the-process-layer)
- [Drupal defense in depth — securing Drupal at the content layer with a CDN - May 2023](https://salsa.digital/insights/Drupal-defense-in-depth-securing-drupal-at-the-content-layer-with-a-cdn)
- [Drupal defense in depth — securing Drupal at edge layer via a Web Application Firewall (WAF) May 2023](https://salsa.digital/insights/Drupal-defense-in-depth-securing-drupal-at-edge-layer-via-a-web-application-firewall-waf)
- [Drupal defense in depth — securing Drupal at the application layer - May 2023](https://salsa.digital/insights/Drupal-defense-in-depth-securing-drupal-at-the-application-layer)

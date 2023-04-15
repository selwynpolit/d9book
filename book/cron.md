---
layout: default
title: Cron
permalink: /cron
last_modified_date: '2023-04-13'
---

# CRON
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![visitors](https://page-views.glitch.me/badge?page_id=selwynpolit.d9book-gh-pages-cron)

---

[Drupal hook_cron API](https://api.drupal.org/api/drupal/core%21core.api.php/function/hook_cron/9.4.x)

## Hook_cron

TODO: needs content.
TODO: Needs info about the Drupal Queue system.

## When did the cron job last run?

We can use this in `.module` files (which don't allow dependency injection) in this way. Call `\Drupal::state()-\>get()` to get the last run time.

```php
// Ensure that our cron job runs only once each day
$last_run = \Drupal::state()->get('aquifer_update.last_run') ?: 0;
if ((REQUEST_TIME - $last_run) < ( 24 * 60 * 60) ) {
  return;
}
```

[Drupal class API reference](https://api.drupal.org/api/drupal/core%21lib%21Drupal.php/class/Drupal/8.3.x)

## How to stop cron from continuously executing things

To stop cron from endlessly executing pending cron tasks truncate the queue table e.g. if you have queue'd up work such as in the salesforce module.

## Setting up cron

In order to get Drupal to take care of it's maintenance you should have the server execute Drupal's cron periocally. This is done by logging in to the server directly and executing the command:

```
sudo crontab -e
```

This loads up vim editor with the cron jobs.

Find the command in Drupal at /admin/config/system/cron (Reports, status report, Last cron run - click on more information) For example:

```
Last run: *1 minute 37 seconds* ago.

To run cron from outside the site, go to https://ddev93.ddev.site/cron/3WpH0y_siTFCrP59LLNRD5s_dGFPpLWbhPS4BCht1b7w1Z_K4CnL46PVZ-6zd74wj6uXXO4K7w

```

You can use wget, curl or lynx (or others) commands to execute the cron also.

From [Drupal cron documentation](https://www.drupal.org/docs/administering-a-drupal-site/cron-automated-tasks/cron-automated-tasks-overview)

Check your Drupal status report which shows cron run time. If this works for you and you want to try editing your Linux crontab file, here's a quick example of hourly cron.

1. At Linux command prompt, type `crontab -e`.
2. Go to end then press Insert key. Then type or paste below.

```
1  *   *   *   *   wget -O - -q -t 1 https://yourdrupalsite.tld/cron/Fe0lip-huaTyeUBYlCXbsc-QI-dw >/dev/null
```

3. ESC to exit inserting. Shift-z shift-z (twice) to save and exit or Ctrl-z to exit without saving.
4. Then, to make sure this is working, check your Drupal status report which shows cron run time.

You can use crontab guru (below) to come up with the appropriate values before the wget.  E.g. If you want cron to run every 5 minutes you could use:

- `*/5 * * * * wget...` - Every 5 minutes.

- `0 22 * * 1-5 wget...` - At 10pm Monday through Friday.

### Samples from crontabs file

```
0 1 \* \* \* /var/www/ddd.test.gov/vendor/drush/drush/drush feeds:import 6 -y

0 2 \* \* \* /var/www/ddd.test.gov/vendor/drush/drush/drush sfproc
dir_contracts

0 3 \* \* \* /var/www/ddd.test.gov/vendor/drush/drush/drush sfproc
enterprise_contracts

0 3 \* \* \* /var/www/ddd.test.gov/vendor/drush/drush/drush sfproc
contract_commodities
```

The \*/15 runs every 15 minutes. The 0 2 runs at 2am, 0 3 runs at 3am etc.  The drush lines are executing custom drush commands.

## Resolving the ip and name for cron

Here is a Drupal cron job on a prod server where it uses a `---resolve` param to resolve the ip and the name. This task runs every 15 minutes.

```
*/15 * * * * /usr/bin/curl -svo /dev/null http://prod.ddd.test.gov:8080/cron/86O435grdgfFFg7bOPT6AGEICKGd7Hf9v02pqXDwi3tnTbsbMFfaSaSPdARNEHNg --resolve prod.ddd.test.gov:8080:201.86.28.12
```

## crontab guru

This is a quick and simple editor for cron schedule expressions [crontab guru](https://crontab.guru/)

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
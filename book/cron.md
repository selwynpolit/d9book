

# CRON

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=cron.md)

## Overview

Cron is a time-based task scheduler that executes commands at specified intervals, called **_cron jobs_**. Cron is available on Unix, Linux, and Mac servers, and Windows servers use a Scheduled Task to execute commands. Cron jobs are used in Drupal to handle maintenance tasks such as cleaning up log files and checking for updates.

## How does it work?

Drupal provides an automated cron system that works with all operating systems because it does not involve the operating system's cron daemon. Instead, it works by checking at the end of each Drupal request to see when the cron last ran. If it has been too long, cron tasks are processed as part of that request.

The Drupal core module [automated_cron](https://git.drupalcode.org/project/drupal/-/tree/11.x/core/modules/automated_cron) subscribes to the [kernel.terminate](https://symfony.com/doc/current/reference/events.html#kernel-terminate) event for request. 

You can read more about [Symfony's: the workflow of a request](https://symfony.com/doc/current/components/http_kernel.html#the-workflow-of-a-request) and specifically about how [the kernel.terminate event fits into that workflow.](https://symfony.com/doc/current/components/http_kernel.html#8-the-kernel-terminate-event)

From [module's AutomatedCron.php](https://git.drupalcode.org/project/drupal/-/blob/11.x/core/modules/automated_cron/src/EventSubscriber/AutomatedCron.php) Drupal watches for an `onTerminate` event:

```php
/**
 * Registers the methods in this class that should be listeners.
 *
 * @return array
 *   An array of event listener definitions.
 */
public static function getSubscribedEvents(): array {
  return [KernelEvents::TERMINATE => [['onTerminate', 100]]];
}
```

Drupal then checks when the cron last ran and ensures that the next time it runs is only after the configured amount of time has elapsed. Note that it uses the State API to keep track of the last cron run. 

```php
/**
 * Run the automated cron if enabled.
 *
 * @param \Symfony\Component\HttpKernel\Event\TerminateEvent $event
 *   The Event to process.
 */
public function onTerminate(TerminateEvent $event): void {
  $interval = $this->config->get('interval');
  if ($interval > 0) {
    $cron_next = $this->state->get('system.cron_last', 0) + $interval;
    if ((int) $event->getRequest()->server->get('REQUEST_TIME') > $cron_next) {
      $this->cron->run();
    }
  }
}
```

The TERMINATE constant is defined in KernelEvents.php: 
```php
/**
 * The TERMINATE event occurs once a response was sent.
 *
 * This event allows you to run expensive post-response jobs.
 *
 * @Event("Symfony\Component\HttpKernel\Event\TerminateEvent")
 */
public const TERMINATE = 'kernel.terminate';
```

So, in essence, if the cron is set to run every hour but the next visitor only comes in three hours, it will only run then.


## Enable Drupal Cron

- One way to enable cron is through the administration page. By default, Drupal has a built-in core `automated cron` system that manages cron. You can access this system by navigating to **_Configuration > System > Cron_**  (`/admin/config/system/cron`). If you have just installed Drupal, this option should be enabled by default. You can confirm this by checking the status of the Automated Cron module at `/admin/modules`.

- Another way to enable cron is to run it manually from the **_Reports > Status report page_**. By default, cron runs every 3 hours, but you can change this to run every hour or every 6 hours. You can also use contributed modules for additional cron functions.

- To run cron using Drush, open a terminal or command prompt and navigate to your Drupal site's root directory. Then, enter the command `drush cron`. This will run cron for your site.

## The cron command

To get Drupal to take care of its maintenance you should have the server execute Drupal’s cron periodically. This is done by logging in to the server directly and settings the crontab file.

**Crontab** (CRON TABle) - is a text file that contains the schedule of cron entries to be run at specified times This file can be created and edited either through the command line interface.

In the following example, the crontab command shown below will activate the cron tasks automatically on the hour:

```
0 * * * * wget -O - -q -t 1 http://www.example.com/cron/<key>
```

In the above sample, the `0 * * * *` represents when the task should happen. The first figure represents minutes – in this case, on the 'zero' minute, or top of the hour. The other figures represent the hour, day, month, and day of the week. A `*` is a wildcard, meaning 'every time'. The minimum is every minute `* * * * *`.

The rest of the line wget `-O - -q -t` 1 tells the server to request a URL, so the server executes the cron script.

Here is a diagram of the general crontab syntax, for illustration:

```
# +-------------- minute (0 - 59)
# |  +----------- hour (0 - 23)
# |  |  +-------- day of the month (1 - 31)
# |  |  |  +----- month (1 - 12)
# |  |  |  |  +-- day of the week (0 - 6) (Sunday=0)
# |  |  |  |  |
  *  *  *  *  *   command to be executed
```

Thus, the cron command example above means `ping http://www.example.com/cron/<key>` at the zero minutes on every hour of every day of every month of every day of the week.


## Setting up cron

To edit a crontab through the command line, type:

1. At the Linux command prompt, type: `sudo crontab -e`

2. Add ONE of the following lines:

```
45 * * * * wget -O - -q -t 1 http://www.example.com/cron/<key>
```
or
```
45 * * * * curl -s http://example.com/cron/<key>
```

This would have a `wget` or `curl` visit your cron page 45 minutes after every hour.

3. Save and exit the file. Check the Drupal status report, which shows the time of the cron execution.

{: .note}
Use [crontab guru](https://crontab.guru/) - it's a quick and easy editor for cron schedule expressions.

## Disable Drupal cron

For performance reasons, or if you want to ensure that cron can only ever run from an external trigger (not from Drupal), it may be desirable to disable Drupal's automated cron system, in one of three ways:

1. The preferred way to disable Drupal's core `automated cron` module is by    unchecking it at `/admin/modules`.

2. To temporarily disable cron, set the 'Run cron every' value to 'Never' at **_Administration > Configuration > System > Cron_** (`/admin/config/system/cron`).

3. For advanced reasons, another way to disable cron in Drupal is to add the following line to your settings.php. Note that this fixes the setting at `/admin/config/system/cron` to 'Never', and administrative users cannot override it.

```php
$config['automated_cron.settings']['interval'] = 0;
```

## hook_cron()

Gets fired every time the cron runs, so basically, Drupal’s cron is a collection of function calls to various modules. For this reason, we must avoid overloading the request with heavy processing; otherwise, the request might crash.

Here is an example of a [hook_cron](https://api.drupal.org/api/drupal/core%21core.api.php/function/hook_cron/10) call:

```php
function announcements_feed_cron() {
  $config = \Drupal::config('announcements_feed.settings');
  $interval = $config->get('cron_interval');
  $last_check = \Drupal::state()->get('announcements_feed.last_fetch', 0);
  $time = \Drupal::time()->getRequestTime();
  if ($time - $last_check > $interval) {
    \Drupal::service('announcements_feed.fetcher')->fetch(TRUE);
    \Drupal::state()->set('announcements_feed.last_fetch', $time);
  }
}
```

And from the [hook_cron API page](https://api.drupal.org/api/drupal/core%21core.api.php/function/hook_cron/10)


```php
// Short-running operation example, not using a queue:
// Delete all expired records since the last cron run.
$expires = \Drupal::state()->get('mymodule.last_check', 0);
$request_time = \Drupal::time()->getRequestTime();
\Drupal::database()
  ->delete('mymodule_table')
  ->condition('expires', $expires, '>=')
  ->execute();
\Drupal::state()
  ->set('mymodule.last_check', $request_time);
```

```php
// Long-running operation example, leveraging a queue:
// Queue news feeds for updates once their refresh interval has elapsed.
$queue = \Drupal::queue('mymodule.feeds');
$ids = \Drupal::entityTypeManager()
  ->getStorage('mymodule_feed')
  ->getFeedIdsToRefresh();
foreach (Feed::loadMultiple($ids) as $feed) {
  if ($queue->createItem($feed)) {
    // Add timestamp to avoid queueing item more than once.
    $feed
      ->setQueuedTime($request_time);
    $feed
      ->save();
  }
}
$ids = \Drupal::entityQuery('mymodule_feed')
  ->accessCheck(FALSE)
  ->condition('queued', $request_time - 3600 * 6, '<')
  ->execute();
if ($ids) {
  $feeds = Feed::loadMultiple($ids);
  foreach ($feeds as $feed) {
    $feed
      ->setQueuedTime(0);
    $feed
      ->save();
  }
}
```




## Common inquiries regarding cron jobs

### When did the cron job last run?

We can use this in .module files (which don’t allow dependency injection) in this way.

```php
// Find out when cron was last run; the key is 'system.cron_last'.
$cron_last = \Drupal::state()->get('system.cron_last');
```

Or using dependency injection:

```php
$cron_last = $this->state->get('system.cron_last')
```

### How to stop Cron from continuously executing things?

To stop cron from endlessly executing pending cron tasks truncate the queue table e.g. if you have queued up work such as in the salesforce module.


### Resolving the ip and name for cron

Here is a Drupal cron job on a prod server where it uses a `--resolve` param to resolve the IP and the name. This task runs every 15 minutes.

```
*/15 * * * * curl -svo /dev/null http://prod.ddd.test.gov:8080/cron/<key> --resolve prod.ddd.test.gov:8080:201.86.28.12
```

## Resources:
- [onTerminate](https://symfony.com/doc/current/components/http_kernel.html#8-the-kernel-terminate-event)
- [Symfony http kernel component docs](https://symfony.com/doc/current/components/http_kernel.html)
- [crontab guru](https://crontab.guru)
- [Drupal hook_cron() API](https://api.drupal.org/api/drupal/core%21core.api.php/function/hook_cron/10)
- [Cron automated tasks overview](https://www.drupal.org/docs/administering-a-drupal-site/cron-automated-tasks/cron-automated-tasks-overview)
- [Configuring cron jobs using the cron command](https://www.drupal.org/node/23714)
- [Crontab – Quick Reference Running](https://www.adminschoice.com/crontab-quick-reference)
- [Drupal cron tasks from Drush](https://www.drush.org/12.x/cron)

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


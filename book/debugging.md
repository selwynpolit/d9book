---
layout: default
title: Debugging
permalink: /debugging
last_modified_date: '2023-05-14'
---

# Debugging
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![visitors](https://page-views.glitch.me/badge?page_id=selwynpolit.d9book-gh-pages-debug)

---

## Overview

Using a combination of PhpStorm, DDEV and Xdebug makes debugging a pleasure. PhpStorm is not essential. Xdebug works fine with other IDE\'s also. In my experience, many Drupal developers have not experienced using a true debugger, but once they do, they wonder how they ever delivered any code without it.

## Enable error reporting
If you experience a WSOD (White Screen Of Death), enabling verbose error messages can often give you some useful clue:

```php
/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';
```
For more, check out <https://drupal.stackexchange.com/questions/127182/how-do-i-enable-developer-debug-mode#:~:text=%24config%5B'system.,can%20always%20comment%20them%20out>.
Also in <https://github.com/drupal/drupal/blob/10.1.x/core/includes/bootstrap.inc> Error reporting levels are defined:

```php
/**
 * Error reporting level: display no errors.
 */
const ERROR_REPORTING_HIDE = 'hide';

/**
 * Error reporting level: display errors and warnings.
 */
const ERROR_REPORTING_DISPLAY_SOME = 'some';

/**
 * Error reporting level: display all messages.
 */
const ERROR_REPORTING_DISPLAY_ALL = 'all';

/**
 * Error reporting level: display all messages, plus backtrace information.
 */
const ERROR_REPORTING_DISPLAY_VERBOSE = 'verbose';
```


## Disable caches and enable Twig debugging

This will cause twig debugging information to be displayed in the HTML code like the following:

```html
<!-- THEME DEBUG -->
<!-- THEME HOOK: 'toolbar' -->
<!-- BEGIN OUTPUT from 'core/themes/stable/templates/navigation/toolbar.html.twig' -->

```
and

```html
<!-- THEME DEBUG -->
<!-- THEME HOOK: 'page' -->
<!-- FILE NAME SUGGESTIONS:
   * page--teks--admin--srp--program--expectation--correlation--vote-all.html.twig
   * page--teks--admin--srp--program--expectation--correlation--852136.html.twig
   * page--teks--admin--srp--program--expectation--correlation--%.html.twig
   * page--teks--admin--srp--program--expectation--correlation.html.twig
   * page--teks--admin--srp--program--expectation--852131.html.twig
   * page--teks--admin--srp--program--expectation--%.html.twig
   * page--teks--admin--srp--program--expectation.html.twig
   * page--teks--admin--srp--program--852061.html.twig
   * page--teks--admin--srp--program--%.html.twig
   * page--teks--admin--srp--program.html.twig
   * page--teks--admin--srp.html.twig
   x page--teks--admin.html.twig
   * page--teks.html.twig
   * page.html.twig
-->
```

In `sites/default/development.services.yml` in the `parameters`, `twig.config`, set `debug:true`. See `core.services.yml` for lots of other items to change for development.

```yaml
# Local development services.
#
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    debug: true
    auto_reload: true
    cache: false

# To disable caching, you need this and a few other items
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

You also need this in `settings.local.php`:

```php
/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
```

Disable caches in `settings.local.php`:

```php
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
```



## Enable/Disable Xdebug

To enable or disable Xdebug when using DDEV use:

```bash
$ ddev xdebug on

$ ddev xdebug off
```
Note. Enabling Xdebug will slow down your app because xdebug has a
significant performance impact so be sure to disable it when you are
finished debugging.

Add this to your `.zshrc` or `.bash` file for \`xon\` and \`xoff\` shortcut

```bash
alias xon='ddev xdebug on'
alias xoff='ddev xdebug off'
```
## Xdebug Port

DDEV now sets the default port to 9003. If you want to change it to another port, use the steps below. From <https://ddev.readthedocs.io/en/stable/users/debugging-profiling/step-debugging/#using-xdebug-on-a-port-other-than-the-default-9003>:

To override the port, add an override file in the project's `.ddev/php` directory. For example, add the file `.ddev/php/xdebug_client_port.ini` like this to specify the legacy port 9000:

```ini
[PHP]

xdebug.client_port=9000
```


## Drupal code debugging

Phpstorm and DDEV make this process as painless as possible. Once you enable Xdebug in DDEV, simply click the \"start listening for PHP Debug Connections\" button. 

To start debugging, open the index.php file and set a breakpoint by clicking on a line number.

![Click listen for PHP Debug Connections button in PhpStorm](assets/images/listen_button.png)

Select a breakpoint:

![Set a breakpoint in PhpStorm](assets/images/breakpoint.png)

Next refresh the Drupal home page in a browser

You should immediately see a dialog pop up in PhpStorm asking you to to configure your local path. Be sure to click the `site/web/index php` and click `Accept`:

Note. If you select one of the other lines you will see a different php file pop up and you won\'t be debugging Drupal, but probably some Symfony file.

![PhpStorm incoming connection dialog](assets/images/incoming_connection.png)

If you accidentally selected the wrong local path, in PhpStorm, go to Settings, PHP, Servers and delete all servers that are displayed. The correct one will be recreated after you retry the operation above.

![Breakpoint reached in debugging in PhpStorm](assets/images/breakpoint_reached.png)

Once you accept the local path, you should see a highlighted line
indicating the current line. The debug window will appear below showing the call stack

![PhpStorm debug call stack](assets/images/debug_call_stack.png)

You will also see the variables and watch pane:

![PhpStorm debug variables and watch pane](assets/images/debug_variables_watch.png)

## Command line or drush debugging

For command line or drush debugging you must run your code from within the DDEV container. This means you must \'ssh\' into the DDEV (Docker) container and execute the command you want to debug this way:

```bash
$ ddev ssh

$ vendor/bin/drush status
```
To setup command line debugging, follow the steps above to setup for [Drupal Code debugging](#drupal-code-debugging) to confirm that you have debugging working. Then look in PhpStorm's: settings, PHP, Servers, and select the server you set up from the previous steps. Specify the top level path as shown below. Usually it will be `/var/www/html`

![PhpStorm debug servers options](assets/images/php_debug_servers.png)

Next open vendor/drush/drush/src/Drush.php and specify a breakpoint like this:

![Set a breakpoint in PhpStorm in the drush source code](assets/images/drush_breakpoint.png)

Now in the terminal ssh into the DDEV container and execute drush:

```bash
$ ddev ssh

$ vendor/bin/drush status
```
PhpStorm will pop up and display the current line and you can debug to your heart\'s content:

![Debugging drush in PhpStorm](assets/images/phpstorm_debugging_drush.png)

More at <https://ddev.readthedocs.io/en/stable/users/debugging-profiling/step-debugging/>

## Add a breakpoint in code

To add a breakpoint in code, you can use: xdebug_break()

more at <https://xdebug.org/docs/all_functions>

## Troubleshooting Xdebug with DDEV

### Could not connect to debugging client

When debugging command line e.g. drush commands etc. if you see:

```bash
$ vendor/bin/drush status

Xdebug: \[Step Debug\] Could not connect to debugging client. Tried: host.docker.internal:9003 (through xdebug.client_host/xdebug.client_port).
```
This means you have not clicked the PhpStorm button:  \"Start listening for PHP Debug Connections\". Just click it and try again

![Click listen for PHP Debug Connections button in PhpStorm](assets/images/listen_button.png)

### PhpStorm refuses to debug

Here are some steps to try:

#### Curl

Use curl or a browser to create a web request. For example:

```bash
$ curl https://d9.ddev.site
```
Replace d9.ddev.site with the correct URL for your site.

#### Logs

If the IDE doesn\'t respond, take a look at ddev logs. Use ddev logs to display current logs for the project\'s web server. See
<https://ddev.readthedocs.io/en/stable/users/basics/commands/#logs> for more.

If you see a message like

```bash
"PHP message: Xdebug: [Step Debug] Could not connect to debugging client. Tried: host.docker.internal:9000 (through xdebug.client_host/xdebug client_port)" 
```
then php/xdebug (inside the container) is not able to make a connection to port 9000. 

This means you have not clicked the "Start listening for PHP Debug Connections" button in PhpStorm.  Just click it and try again.

Note. Port 9003 is more current.


#### Telnet

With PhpStorm NOT listening for a PHP Debug connection, try to telnet to see what is listening to port 9003 with this:

```bash
$ telnet d9book2.ddev.site 9003
```
You should get connection refused. If you get a connection (see below) something is listening on port 9003 and you should either disable it or use a different port. Note. You must set that in both PhpStorm as well as DDEV.

This shows a connection succeeding. You can try it with PhpStorm listening for a debug connection:

```bash
$ telnet d9book2.ddev.site 9003

Trying 127.0.0.1\...

Connected to d9book2.ddev.site.

Escape character is \'\^\]\'.
```
Use Control \] to exit and then type quit to return to exit telnet.

On a mac, use `sudo lsof -i :9003 -sTCP:LISTEN` to find out what is listening on that port and stop it, or change the xdebug port and configure both DDEV and PhpStorm to use the new one . 

More about changing ports at <https://ddev.readthedocs.io/en/stable/users/debugging-profiling/step-debugging/#using-xdebug-on-a-port-other-than-the-default-9003>

Note. In the past, php-fpm was likely to be one of the apps using port 9000.


#### Is Xdebug enabled?

• To check to make sure that Xdebug is enabled, you can use `php -i | grep xdebug` inside the container. You can also use other techniques to view the output of phpinfo(), including Drupal\'s `admin/reports/status/php`. Below you can see the expected output when Xdebug is enabled assuming you have Xdebug v3.2.0 or later.


```
`$ php -i | grep "xdebug.remote_enable"`

xdebug.remote_enable => (setting renamed in Xdebug 3) => (setting renamed in Xdebug 3)
```

See <https://ddev.readthedocs.io/en/stable/users/step-debugging>

## What is listening on the debug port?

To check if something is listening on port 9003, it's best to use `lsof` as it will actually list the name of the process listening.

i.e. Here we see phpstorm listening:
```
lsof -i TCP:9003
COMMAND    PID   USER   FD   TYPE             DEVICE SIZE/OFF NODE NAME
phpstorm 78897 selwyn  209u  IPv4 0x4d118c80f54422d7      0t0  TCP *:9003 (LISTEN)

```

You can also use `nc`  and `netstat` but they is not quite as informative:

```bash
$ nc -z localhost 9003
Connection to localhost port 9003 [tcp/*] succeeded!
```
The message `Connection to localhost port 9003 [tcp/*] succeeded!` means that there is a program listening on port 9003. If you get nothing when you type the command, then nothing is listening.

Here `netstat` reports that something is listening on port 9003. Again, if you get nothing when you type the command, then nothing is listening.

```bash
$ netstat -an | grep 9003
tcp4       0      0  *.9003                 *.*                    LISTEN
```





Here is an example running `lsof` and finding php-fpm listening on port 9000

```bash
$ lsof -i TCP:9000

COMMAND PID USER FD TYPE DEVICE SIZE/OFF NODE NAME

php-fpm 732 selwyn 7u IPv4 0x4120ed57a07e871f 0t0 TCP
localhost:cslistener (LISTEN)

php-fpm 764 selwyn 8u IPv4 0x4120ed57a07e871f 0t0 TCP
localhost:cslistener (LISTEN)

php-fpm 765 selwyn 8u IPv4 0x4120ed57a07e871f 0t0 TCP
localhost:cslistener (LISTEN)
```

## Enable twig debugging output in source

In `sites/default/development.services.yml` in the `parameters`, `twig.config`, set `debug:true`. See `core.services.yml` for lots of other items to change for development.

```yaml
# Local development services.
#
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    debug: true
    auto_reload: true
    cache: false

# To disable caching, you need this and a few other items
services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```

You also need this in settings.local.php:

```php
/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
```
You also need to disable the render cache in `settings.local.php` with:

```php
$settings['cache']['bins']['render'] = 'cache.backend.null';
```


## Devel and Devel Kint Extras

From
<https://www.webwash.net/how-to-print-variables-using-devel-and-kint-in-drupal/>

### Setup

We need both the Devel and Devel Kint Extras module. [Devel Kint
Extras](https://www.drupal.org/project/devel_kint_extras) ships with the kint-php library so installing this via Composer will take care of it automatically.

Install using Composer:

```
composer require drupal/devel drupal/devel_kint_extras
```
Enable both with the following Drush command:

```bash
$ ddev drush en devel_kint_extras -y
```
Finally, enable `Kint Extended` as the `Variables Dumper`. To do this go to:

`admin/config/development/devel`

and select `Kint Extender` and Save the configuration 

### Add kint to a custom module

```php
function custom_kint_preprocess_page(&$variables) { 
  kint($variables['page']);
 }
```

### Dump variables in a TWIG template

\{\{ kint(attributes) \}\}

### Kint::dump

From [Migrate Devel contrib module](https://www.drupal.org/project/migrate_devel), in `docroot/modules/contrib/migrate_devel/src/EventSubscriber/MigrationEventSubscriber.php`

This is used in migrate to dump the source and destination values.

```php
// We use kint directly here since we want to support variable naming.
kint_require();
\Kint::dump($Source, $Destination, $DestinationIDValues);
```

### Set max levels to avoid running out of memory

Kint can run really slowly so you may have to set maxLevels this way:

Add this to settings.local.php

```php
// Change kint maxLevels setting:
include_once(DRUPAL_ROOT . '/modules/contrib/devel/kint/kint/Kint.class.php');
if(class_exists('Kint')){
  // Set the maxlevels to prevent out-of-memory. Currently there doesn't seem to be a cleaner way to set this:
  Kint::$maxLevels = 4;
}
```

## Resources


- [DDEV Documentation](https://ddev.readthedocs.io/en/stable/)

- [Debugging with Xdebug in DDEV docs](https://ddev.readthedocs.io/en/stable/users/debugging-profiling/step-debugging)

- [Debug Drush commands with PhpStorm at](https://www.jetbrains.com/help/phpstorm/drupal-support.html#view_drupal_api_documentation)

- [Configuring PhpStorm Drupal.org docs updated September 2022](https://www.drupal.org/docs/develop/development-tools/configuring-phpstorm)

- [Debugging Drush commands](https://www.jetbrains.com/help/phpstorm/drupal-support.html#debugging-drush-commands)

- [DDEV docs on using a different port for debugging](https://ddev.readthedocs.io/en/stable/users/debugging-profiling/step-debugging/#using-xdebug-on-a-port-other-than-the-default-9003)

- [How to setup Devel and Kint on Drupal 9 by Alex Aug 2021](https://www.altagrade.com/blog/how-install-devel-and-kint-drupal-9)

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

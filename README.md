# Drupal 9 Book

by Selwyn Polit


## settings.php

Note ddev v1.15 has some new idiosyncracies to be aware of

when using a global drush command from the host.  e.g. `drush en token` like you used to in Drupal 8, you will notice errors and the command will fail.

The fix is to tweak the settings.php by adding this line before the #ddev-generated code

putenv("IS_DDEV_PROJECT=true");

// #ddev-generated: Automatically generated Drupal settings file.
if (file_exists($app_root . '/' . $site_path . '/settings.ddev.php') && getenv('IS_DDEV_PROJECT') == 'true') {
include $app_root . '/' . $site_path . '/settings.ddev.php';
}


## Enable Twig debugging

* Copy `sites/example.settings.local.php` to `sites/default/settings.local.php`
* Add this to `sites/default/settings.php`:

```
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
```

NOTE: `sites/default/settings.php` references `sites/default/settings.local.php`, which references `sites/development.services.yml`

* Create a `sites/development.services.yml`, with the following contents:
* toggle the debug: false line to view templates being used for each element on site
* drush cr after modifying the file to have changes take effect
```
# Local development services.
#
# To activate this feature, follow the instructions at the top of the
# 'example.settings.local.php' file, which sits next to this file.
parameters:
  http.response.debug_cacheability_headers: true
  twig.config:
    # Twig debugging:
    #
    # When debugging is enabled:
    # - The markup of each Twig template is surrounded by HTML comments that
    #   contain theming information, such as template file name suggestions.
    # - Note that this debugging markup will cause automated tests that directly
    #   check rendered HTML to fail. When running automated tests, 'debug'
    #   should be set to FALSE.
    # - The dump() function can be used in Twig templates to output information
    #   about template variables.
    # - Twig templates are automatically recompiled whenever the source code
    #   changes (see auto_reload below).
    #
    # For more information about debugging Twig templates, see
    # https://www.drupal.org/node/1906392.
    #
    # Not recommended in production environments
    # @default false
    debug: false
    # Twig auto-reload:
    #
    # Automatically recompile Twig templates whenever the source code changes.
    # If you don't provide a value for auto_reload, it will be determined
    # based on the value of debug.
    #
    # Not recommended in production environments
    # @default null
#    auto_reload: null
    auto_reload: true
    # Twig cache:
    #
    # By default, Twig templates will be compiled and stored in the filesystem
    # to increase performance. Disabling the Twig cache will recompile the
    # templates from source each time they are used. In most cases the
    # auto_reload setting above should be enabled rather than disabling the
    # Twig cache.
    #
    # Not recommended in production environments
    # @default true
    cache: false

services:
  cache.backend.null:
    class: Drupal\Core\Cache\NullBackendFactory
```
## DDEV usage

### Everyday ddev commands
`ddev start` - start containers

`ddev stop` - stop containers, delete them and **keep db intact**

`ddev restart` - rebuild containers if changing configuration of ddev

`ddev ssh` - ssh into container

`ddev sequelpro` - fire up sql pro already connected!

`ddev composer <blah>` - run composer in container e.g. `composer install`

`ddev describe` - show the sql connection ports etc. for connecting sequel pro

`ddev list` - list sites and urls

`ddev stop --unlist <projectname>` -- remove project from list until ddev start is run for project

`ddev export-db --f dbdump1.sql.gz`  - Backup the db often so you can recover in a pinch.  I use dbdump1 and dbdump2 files so I have a way back to my previous db too.

`ddev import-db --src=dbdump1.sql.gz` - Restore the db quickly and painlessly

### To Enable/disable Xdebug in ddev

Use these commands.  be sure to disable after debugging because xdebug has a significant performance impact.

`ddev exec enable_xdebug`
and
`ddev exec disable_xdebug`


### More ddev commands

`ddev` - list all commands

`ddev <command> --help` - shows help about a command e.g. `ddev list --help`


`ddev import-db --src=dbdev.sql.gz` - import gzipped db file from host

`ddev export-db -f dbdump2.sql.gz` - export db file to host file dbdump1.sql.gz

`ddev pull` - special command to pull db from pantheon environment

### drush
Although it is not a sanctioned use, if you install drush globally, you can issue drush commands like `drush cr` as long as you are in the directory ~/Sites/dir.
I find it very convenient to use it this way.
If you see errors when trying this, edit your settings.php by adding the putenv command below.
```

putenv("IS_DDEV_PROJECT=true");
// #ddev-generated: Automatically generated Drupal settings file.
if (file_exists($app_root . '/' . $site_path . '/settings.ddev.php') && getenv('IS_DDEV_PROJECT') == 'true') {
  if (file_exists($app_root . '/' . $site_path . '/settings.ddev.php')) {
  include $app_root . '/' . $site_path . '/settings.ddev.php';
```
e.g.


drush st

drush cr (clear local)

when it doesn't just use `ddev exec drush <drush command>`  e.g.

`ddev exec drush uli`

`ddev composer require drupal/admin_toolbar`

`ddev exec drush sql-dump >dbdump2.sql`

ddev exec drush updb

ddev exec drush cim sync

ddev exec drush cex sync

ddev exec drush cget system.site uuid



## Troubleshooting

### Containers won't start

ddev stop --remove-data --omit-snapshot. - if db corrupt and containers won’t start.  Follow this up with ddev start.  You may also have to restart docker.

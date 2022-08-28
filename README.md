# Welcome to Drupal 9 at your fingertips
## A Drupal developers quick code reference
### by Selwyn Polit


## [Read it here](https://selwynpolit.github.io/d9book/index.html)

This repository contains the markup for the [book](https://selwynpolit.github.io/d9book/index.html) as well as a Drupal installation complete with config files and database dumps.
It also has some code that was used to check the accuracy of the book.
If you want to contribute, select the gh-pages branch from the branches button near the top of this page.
You can then directly click the pencil button to edit (and automatically fork the repo).

Thanks for visiting and contributing.



## Some tips

### Running drush on the host
The approved way to use drush on a ddev project is to use `ddev drush cr` etc.

For my own convenience, I prefer to just use `drush cr` while in the directory for my project.
If you see errors when trying this, edit your web/sites/default/settings.php (or web/sites/default/settings.local.php) by adding the putenv command below.
```
putenv("IS_DDEV_PROJECT=true");
```




### Enable Twig debugging
When you want to do some debugging, this can be a really useful setting to enable.
It will display the template names in the HTML source.

* Copy `sites/example.settings.local.php` to `sites/default/settings.local.php`
* Add this to the bottom of `sites/default/settings.php`:

```
if (file_exists($app_root . '/' . $site_path . '/settings.local.php')) {
  include $app_root . '/' . $site_path . '/settings.local.php';
}
```

NOTE: `sites/default/settings.php` references `sites/default/settings.local.php`, which references `sites/development.services.yml`

* Create a `sites/development.services.yml`, with the following contents:
* toggle the debug: false line to view templates being used for each element on site
* Run `drush cr` after modifying the file to have changes take effect
* Warning. Make sure your site is actually running before you make this change otherwise it will not run and make you very frustrated.

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

### Frequently used ddev commands to get you started
If you haven't used [DDEV](https://ddev.com), you should be using it!
It makes development soooo much nicer.

`ddev start` - start containers

`ddev stop` - stop containers, delete them and **keep db intact**

`ddev restart` - rebuild containers if changing configuration of ddev

`ddev ssh` - ssh into container

`ddev composer <blah>` - run composer in container e.g. `composer install`

`ddev describe` - show the sql connection ports etc. for connecting sequel pro

`ddev list -A` - list sites and urls

`ddev export-db --f dbdump1.sql.gz`  - Backup the db often so you can recover in a pinch.  I use dbdump1 and dbdump2 files so I have a way back to my previous db too.

`ddev import-db --src=dbdump1.sql.gz` - Restore the db quickly and painlessly

### To Enable/disable Xdebug in ddev

Use these commands.  be sure to disable after debugging because xdebug has a significant performance impact.

`ddev exec enable_xdebug`
and
`ddev exec disable_xdebug`


## Troubleshooting

### Containers won't start

ddev stop --remove-data --omit-snapshot. - if db corrupt and containers won’t start.  Follow this up with ddev start.  You may also have to restart docker.

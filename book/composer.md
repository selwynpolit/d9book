---
title: Composer
---

# Composer, Updates and Patches
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=composer.md)

## Creating a local patch to a contrib module

In this case, I had the file_entity module installed and wanted to hide the `files` tab. That (menu) tab is provided by a task in `web/modules/contrib/file_entity/file_entity.links.task.yml`

```yaml
entity.file.collection:
  route_name: entity.file.collection
  base_route: system.admin_content
  title: 'Files'
  description: 'Manage files for your site.'
```

For my patch, I wanted to remove this section of the `file_entity.links.task.yml` file.

First I get the repo/git version of the module:

```sh
$ composer update drupal/file_entity --prefer-source
```

::: tip Note
You can also just go retrieve the module using the git clone from the module\'s project page on drupal.org.  This allows you to more quickly create your patch without impacting your current project.
:::


Then change the file in the text editor and run git diff to see the changes:

```sh
$ git diff
```

The output shows:

```diff
diff --git a/file_entity.links.task.yml b/file_entity.links.task.yml
index 3ea93fc..039f7f9 100644
--- a/file_entity.links.task.yml
+++ b/file_entity.links.task.yml
@@ -15,12 +15,6 @@ entity.file.edit_form:
   base_route: entity.file.canonical
   weight: 0
 
-entity.file.collection:
-  route_name: entity.file.collection
-  base_route: system.admin_content
-  title: 'Files'
-  description: 'Manage files for your site.'
-
 entity.file.add_form:
   route_name: entity.file.add_form
   base_route: entity.file.add_form
```

Create the patch:

```sh
git diff >file_entity_disable_file_menu_tab.patch
```

Add the patch to the `patches` section of `composer.json` under the `extra` section. Notice below the line starting with `"drupal/file_entity\"` is the local file patch:

```json
"extra": {
  "patches": {
      "drupal/file_entity": {
          "Temporarily disable the files menu tab": "./patches/file_entity_disable_file_menu_tab.patch"
      }
      "drupal/commerce": {
          "Allow order types to have no carts": "https://www.drupal.org/files/issues/2018-03-16/commerce-direct-checkout-50.patch"
      },
      "drupal/views_load_more": {
          "Template change to keep up with core": "https://www.drupal.org/files/issues/views-load-more-pager-class-2543714-02.patch" ,
          "Problems with exposed filters": "https://www.drupal.org/files/issues/views_load_more-problems-with-exposed-filters-2630306-4.patch"
      },
      "drupal/easy_breadcrumb": {
          "Titles in breadcrumbs are double-escaped": "https://www.drupal.org/files/issues/2018-06-21/2979389-7-easy-breadcrumb--double-escaped-titles.patch"
      },
  }
```

Revert the file in git and then try to apply the patch.

Here is the patch command way to un-apply or revert a patch (-R means revert)

```
patch -p1 -R < ./patches/fix_scary_module.patch
```
To apply the patch:

```
patch -p1 < ./patches/fix_scary_module.patch
```

For more, see [Making a patch](https://www.drupal.org/node/707484).


## Patch modules using patches on Drupal.org

Patches can be applied by referencing them in the composer.json file, in the following format. [cweagans/composer-patches](https://github.com/cweagans/composer-patches) can then be used to apply the patches on any subsequent website builds.

In order to install and manage patches using composer we need to require the "composer-patches" module: 

```
composer require cweagans/composer-patches
```


Examples of patches to core look like:

```json
  "extra": {
    "patches": {
      "drupal/core": {
        "Add startup configuration for PHP server": "https://www.drupal.org/files/issues/add_a_startup-1543858-30.patch"
      }
    }
  },
```


```json
  "extra": {
    "patches": {
      "drupal/core": {
        "Ignore front end vendor folders to improve directory search performance": "https://www.drupal.org/files/issues/ignore_front_end_vendor-2329453-116.patch"",
        "My custom local patch": "./patches/drupal/some_patch-1234-1.patch"
      }
    }
  },
```

Some developers like adding the actual link to the issue in the description like this:

```json
"extra": {
  "patches": {
      "drupal/core": {
          "Views Exposed Filter Block not inheriting the display handlers cache tags, causing filter options not to appear, https://www.drupal.org/project/drupal/issues/3067937": "https://www.drupal.org/files/issues/2019-07-15/drupal-exposed_filter_block_cache_tags-3067937-4.patch",
          "Cannot use relationship for rendered entity on Views https://www.drupal.org/project/drupal/issues/2457999": "https://www.drupal.org/files/issues/2021-05-13/9.1.x-2457999-267-views-relationship-rendered-entity.patch"
      },
```

See [Drupal 9 and Composer Patches](https://vazcell.com/blog/how-apply-patch-drupal-9-composer)
also [Managing patches with Composer](https://acquia.my.site.com/s/article/360048081193-Managing-patches-with-Composer)

### Step by step 

1. Find the issue and patch in the issue queue on Drupal.org
2. Use the title and ID of the issue to be able to locate this post in the future. E.g. [Using an issue for the Gin admin theme](https://www.drupal.org/project/gin/issues/3188521) "Improve content form detection - 3188521" 
3. Scroll down the issue to find the specific patch you want to apply e.g. for comment #8 grab the file link for `3188521-8.patch`.  It is [https://www.drupal.org/files/issues/2021-05-19/3188521-8.patch](https://www.drupal.org/files/issues/2021-05-19/3188521-8.patch)
4. Add the module name, description and URL for the patch into the extra patches section of json:

```json
  "extra": {
    "patches": {
      "drupal/core": {
        "Add startup configuration for PHP server": "https://www.drupal.org/files/issues/add_a_startup-1543858-30.patch"
      },
      "drupal/gin": {
        "Improve content form detection - 3188521": "https://www.drupal.org/files/issues/2021-05-19/3188521-8.patch"
      }
    }
  }

```
5. use `composer update --lock` to apply the patch and watch the output.

If the patch was not applied or throws an error which is quite common (because they are no longer compatible), try using `-vvv` (verbose mode) flag with composer to see the reason: 

```
composer update -vvv
```

## Patches from a Gitlab merge request

Use this technique **at your peril!** 

A better process is to download the Merge request patch, and after reviewing carefully, apply it with `cweagans/composer-patches` with a local reference like: `patches/core-1234567-33.patch`.

Using the URL of the merge request, add `.patch` at the end of the URL and that will be the path to the latest patch.

e.g. for a merge request at [https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2](https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2) or [https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2/diffs?view=parallel](https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2/diffs?view=parallel)

The patch is at [https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2.patch](https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2.patch)

Note. The patch file itself may look a little different from what you may be accustomed to. In the example below, you may notice that there is a header with the commit message and author details before the patch itself actually begins. This is normal and the patch should still apply correctly.:

```diff
From a4edb6adc09abb1ca52e92d80111173bfa206132 Mon Sep 17 00:00:00 2001
From: Derek Wright <git@dwwright.net>
Date: Wed, 6 Dec 2023 16:42:51 -1000
Subject: [PATCH 1/6] Bug #2418369: Initial Functional tests to show bugs in
 URL aliases + language handling

---
 .../PathologicLanguageAliasTest.php           | 158 ++++++++++++++++++
 1 file changed, 158 insertions(+)
 create mode 100644 tests/src/Functional/PathologicLanguageAliasTest.php

diff --git a/tests/src/Functional/PathologicLanguageAliasTest.php b/tests/src/Functional/PathologicLanguageAliasTest.php
new file mode 100644
index 0000000..a4f7c84
--- /dev/null
+++ b/tests/src/Functional/PathologicLanguageAliasTest.php
@@ -0,0 +1,158 @@
```


::: danger
You should **not apply patches directly from Gitlab merge requests** for several reasons. 
1. When the new drupal.org is released, issues will be moved to gitlab. These file urls will stop working at some point, and if they do your project won't build. Be future proof, use local patches.
2. New patches may be added to the merge request, and you won't know about them. They could be flawed or malicious which could break your site.
3. Your composer install (or deployment) now depends on drupal.org. If you are building your site and drupal.org has a temporary outage, your deployment will fail.
:::


## composer.json patches in separate file

To separate patches into a different file other than composer json add `"patches-file"` section under `"extra"`. See example below:

```json
"extra": {
    "installer-paths": {
        "web/core": ["type:drupal-core"],
        "web/libraries/{$name}": ["type:drupal-library"],
        "web/modules/contrib/{$name}": ["type:drupal-module"],
        "web/profiles/contrib/{$name}": ["type:drupal-profile"],
        "web/themes/contrib/{$name}": ["type:drupal-theme"],
        "drush/Commands/contrib/{$name}": ["type:drupal-drush"],
        "web/modules/custom/{$name}": ["type:drupal-custom-module"],
        "web/themes/custom/{$name}": ["type:drupal-custom-theme"]
    },
    "drupal-scaffold": {
        "locations": {
            "web-root": "web/"
        },
        "excludes": [
            "robots.txt",
            ".htaccess"
        ]
    },
    "patches-file": "patches/composer.patches.json"
}
```


### Troubleshoot Composer Patches

If composer install fails, try `composer -vvv` for verbose output

If the issue is that it can't find the file for example if it displays the following:

```sh
  - Applying patches for drupal/addtocalendar
    ./patches/add_to_calendar_smart_date_handling.patch (Add support for smart_date fields)
patch '-p1' --no-backup-if-mismatch -d 'web/modules/contrib/addtocalendar' < '/Users/selwyn/Sites/txglobal/patches/add_to_calendar_smart_date_handling.patch'
Executing command (CWD): patch '-p1' --no-backup-if-mismatch -d 'web/modules/contrib/addtocalendar' < '/Users/selwyn/Sites/txglobal/patches/add_to_calendar_smart_date_handling.patch'
can't find file to patch at input line 5
Perhaps you used the wrong -p or --strip option?
```

This means the patch is trying to run the patch in the directory `web/modules/contrib/addtocalendar` (notice the `-d web/modules/contrib/addtocalendar` above

In this case, recreate the patch with the `--no-prefix` option i.e.

```sh
git diff --no-prefix >./patches/patch2.patch
```

Then composer install will apply the patch correctly

More at <https://github.com/cweagans/composer-patches/issues/146>

## Stop files being overwritten during composer operations

Depending on your composer.json, files like development.services.yml may be overwritten from during scaffolding. To prevent certain scaffold files from being overwritten every time you run a Composer command you can specify them in the "extra" section of your project's composer.json. See the docs on Excluding scaffold files.

The following snippet prevents the development.services.yml from being regularly overwritten:
```json
"drupal-scaffold": {
    "locations": {
        "web-root": "web/"
    },
    "file-mapping": {
        "[web-root]/sites/development.services.yml": false
    }
},
```
The code above is from <https://www.drupal.org/docs/develop/development-tools/disable-caching#s-beware-of-scaffolding>

and from <https://www.drupal.org/docs/develop/using-composer/using-drupals-composer-scaffold#toc_6>: Sometimes, a project might prefer to entirely replace a scaffold file provided by a dependency, and receive no further updates for it. This can be done by setting the value for the scaffold file to exclude to false.  In the example below, three files are excluded from being overwritten:

```json
  "name": "my/project",
  ...
  "extra": {
    "drupal-scaffold": {
      "locations": {
        "web-root": "web/"
      },
      "file-mapping": {
        "[web-root]/robots.txt": false
        "[web-root]/.htaccess": false,
        "[web-root]/sites/development.services.yml": false
      },
      ...
    }
  }
```
More at <https://drupal.stackexchange.com/questions/290989/composer-keeps-overwriting-htaccess-and-other-files-every-time-i-do-anything>


## Modify files that are usually included in scaffolding

Here the `composer.json` has a little tweak that inserts (prepends?) the contents of the `scaffold/htaccess_prepend.txt` file into the `.htaccess` file.  This is useful for adding custom rules to the `.htaccess` file.  It also prevents several other files from being overwritten during composer install or update.

```yaml
"extra": {
    "drupal-scaffold": {
        "locations": {
            "web-root": "docroot/"
        },
        "file-mapping": {
            "[web-root]/.ht.router.php": false,
            "[web-root]/example.gitignore": false,
            "[web-root]/INSTALL.txt": false,
            "[web-root]/README.md": false,
            "[web-root]/web.config": false,
            "[web-root]/modules/README.txt": false,
            "[web-root]/profiles/README.txt": false,
            "[web-root]/sites/default/default.services.yml": false,
            "[web-root]/sites/default/default.settings.php": false,
            "[web-root]/sites/example.settings.local.php": false,
            "[web-root]/sites/example.sites.php": false,
            "[web-root]/sites/README.txt": false,
            "[web-root]/themes/README.txt": false,
            "[web-root]/.htaccess": {
                "prepend": "scaffold/htaccess_prepend.txt"
            }
        }
    },
```

Here are the contents of the `scaffold/htaccess_prepend.txt` file.  Note that this adds a redirect rule to force HTTPS and also adds .htaccess password control for the dev, test and prod environments.:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine on

  # Redirect HTTP requests to HTTPS.
  RewriteCond %{HTTPS} off
  RewriteCond %{HTTP:X-Forwarded-Proto} !https
  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

<IfModule mod_authn_file.c>
  # Require authentication for the development environment.
  <If "%{ENV:AH_SITE_ENVIRONMENT} == 'dev'">
    AuthType Basic
    AuthName "Authentication required"
    AuthUserFile /mnt/files/abc.dev/nobackup/.htpasswd
    Require valid-user
  </If>

  # Require authentication for the test/stage environment.
  <If "%{ENV:AH_SITE_ENVIRONMENT} == 'test'">
    AuthType Basic
    AuthName "Authentication required"
    AuthUserFile /mnt/files/abc.test/nobackup/.htpasswd
    Require valid-user
  </If>

  # Require authentication for the production environment.
  <If "%{ENV:AH_SITE_ENVIRONMENT} == 'prod'">
    AuthType Basic
    AuthName "Authentication required"
    AuthUserFile /mnt/files/abc.prod/nobackup/.htpasswd
    Require valid-user
  </If>
</IfModule>
```



## Updating Drupal Core

if there is `drupal/core-recommended` in your `composer.json` use:

```sh
composer update drupal/core-recommended -W
```

if there is no `drupal/core-recommended` in your `composer.json` use:

```sh
composer update drupal/core -W
```

Note `composer update -W` is the same as `composer update --with-dependencies`


Recently, when upgrading from Drupal 10.1.6 to 10.2.3, I used the following command:

```sh
composer update "drupal/core-*" --with-all-dependencies
```
While this updated the core to 10.1.8, it didn't upgrade to 10.2.3.  I then used the following command:


```sh
composer require drupal/core-recommended:10.2.3 drupal/core-composer-scaffold:10.2.3 drupal/core-project-message:10.2.3 drush/drush --update-with-all-dependencies
```

::: tip Note
I added the `drush/drush` part because the first attempt using
`composer require drupal/core-recommended:10.2.3 drupal/core-composer-scaffold:10.2.3 drupal/core-project-message:10.2.3 --update-with-all-dependencies` failed with:
```sh
Problem 1
- Root composer.json requires drupal/core-recommended 10.2.3 -> satisfiable by drupal/core-recommended[10.2.3].
- drupal/core 10.2.3 conflicts with drush/drush <12.4.3.
- drupal/core-recommended 10.2.3 requires drupal/core 10.2.3 -> satisfiable by drupal/core[10.2.3].
- drush/drush is locked to version 12.3.0 and an update of this package was not requested.
```
:::

More at [https://www.drupal.org/project/drupal/releases/10.2.3](https://www.drupal.org/project/drupal/releases/10.2.3) and [Updating Drupal core via composer updated Dec 2023. ](https://www.drupal.org/docs/updating-drupal/updating-drupal-core-via-composer)


## What are the dependencies?

To check why a project is included use `composer why` or `composer depends`.

```sh
composer why enyo/dropzone
drupal/recommended-project dev-master requires enyo/dropzone (^5.9)
```

Or
```sh
composer why drupal/tamper
drupal/recommended-project dev-master  requires drupal/tamper (^1.0@alpha)
drupal/feeds_tamper        2.0.0-beta3 requires drupal/tamper (^1.0-alpha3)
```

You can also see a recursive tree of why the package is depended upon:

```sh
composer depends drupal/tamper -t
drupal/tamper 1.0.0-alpha4 Generic plugin to modify data.
├──drupal/recommended-project dev-master (requires drupal/tamper ^1.0@alpha)
└──drupal/feeds_tamper 2.0.0-beta3 (requires drupal/tamper ^1.0-alpha3)
   └──drupal/recommended-project dev-master (requires drupal/feeds_tamper ^2.0@beta)
```

See [more about composer why/depends](https://getcomposer.org/doc/03-cli.md#depends-why)
See [also this explanation of why-not](https://getcomposer.org/doc/03-cli.md#prohibits-why-not)

## Test composer (dry run)

If you want to run through an installation without actually installing a package, you can use --dry-run. This will simulate the installation and show you what would happen.

```sh
composer update --dry-run "drupal/*"
```
produces something like:

```sh
Package operations: 0 installs, 4 updates, 0 removals
  - Updating drupal/core (8.8.2) to drupal/core (8.8.4)
  - Updating drupal/config_direct_save (1.0.0) to drupal/config_direct_save (1.1.0)
  - Updating drupal/core-recommended (8.8.2) to drupal/core-recommended (8.8.4)
  - Updating drupal/crop (1.5.0) to drupal/crop (2.0.0)
```

## Add a module that isn't currently supported in your version of drupal

### For Drupal 9
Install the lenient endpoint:


`composer config repositories.lenient composer https://packages.drupal.org/lenient `

Your `composer.json` file will get this. notice the `lenient` key below:

```json
    "repositories": {
        "lenient": {
            "type": "composer",
            "url": "https://packages.drupal.org/lenient"
        },
        "0": {
            "type": "composer",
            "url": "https://packages.drupal.org/8"
        }
    },
```

Specify which Drupal module that composer should be lenient with: 

`composer config --merge --json extra.drupal-lenient.allowed-list '["drupal/node_access_rebuild_progressive"]'`

And `composer.json` gets this added:

```json
    "drupal-lenient": {
        "allowed-list": ["drupal/node_access_rebuild_progressive"]
    }
```

If you haven't already installed the [cweagans composer patches plugin](https://github.com/cweagans/composer-patches) use: 

```
composer require cweagans/composer-patches
```

Create the patch file `patches/node_access_rebuild_progressive_d10.patch` with the following contents.  It is on [drupal.org](https://www.drupal.org/project/node_access_rebuild_progressive/issues/3288770#comment-15227586).

```diff
diff --git docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml
index 1a0e13eec..f322ff847 100644
--- docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml
+++ docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml
@@ -1,7 +1,7 @@
 name: 'Node Access Rebuild Progressive'
 description: 'Rebuild node access grants in chunks'
 type: module
-core_version_requirement: ^8 || ^9
+core_version_requirement: ^9.4 || ^10
 
 # Information added by Drupal.org packaging script on 2020-06-23
 version: '2.0.0'
diff --git docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module
index 45f7c8a41..d2fc50637 100644
--- docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module
+++ docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module
@@ -36,7 +36,7 @@ function node_access_rebuild_progressive_trigger() {
   node_access_needs_rebuild(FALSE);
   // Add default grants in the unlikely case
   // no modules implement node_grants anymore.
-  if (!count(\Drupal::moduleHandler()->getImplementations('node_grants'))) {
+  if (!count(\Drupal::moduleHandler()->hasImplementations('node_grants'))) {
     node_access_rebuild_progressive_set_default();
     return node_access_rebuild_progressive_finished();
   }

```

In composer.json add your patch as in below.  It is on [drupal.org](https://www.drupal.org/project/node_access_rebuild_progressive/issues/3288770#comment-15227586).

```json
    "extra": {
        "drupal-scaffold": {
            "locations": {
                "web-root": "docroot/"
            },
            "file-mapping": {
                "[web-root]/sites/development.services.yml": false
            }
        },
        "installer-paths": {
            "docroot/core": [
                "type:drupal-core"
            ],
            "docroot/libraries/{$name}": [
                "type:drupal-library"
            ],
            "docroot/modules/contrib/{$name}": [
                "type:drupal-module"
            ],
            "docroot/profiles/contrib/{$name}": [
                "type:drupal-profile"
            ],
            "docroot/themes/contrib/{$name}": [
                "type:drupal-theme"
            ],
            "drush/Commands/contrib/{$name}": [
                "type:drupal-drush"
            ],
            "docroot/modules/custom/{$name}": [
                "type:drupal-custom-module"
            ],
            "docroot/profiles/custom/{$name}": [
                "type:drupal-custom-profile"
            ],
            "docroot/themes/custom/{$name}": [
                "type:drupal-custom-theme"
            ]
        },
        "patches": {
            "drupal/node_access_rebuild_progressive": {
                "Automated Drupal 10 compatibility fixes - 3288770": "patches/node_access_rebuild_progressive_d10.patch"
            }
        },

```

Install the module with:

`composer require drupal/node_access_rebuild_progressive`

The module will be installed and the patch applied!

More at
- [Using Drupal's Lenient Composer Endpoint - Sep 2023](https://www.drupal.org/docs/develop/using-composer/using-drupals-lenient-composer-endpoint)
- [Install a Contributed Module with No Drupal 9 Release - Feb 2023](https://drupalize.me/tutorial/install-contributed-module-no-drupal-9-release)


### For Drupal 10

Install the composer lenient plugin
`composer require mglaman/composer-drupal-lenient`

This makes `composer.json` look like this:

Notice the `require` key and the `config` key below
```json
    "require": {
        "acquia/memcache-settings": "^1.2",
        ...
        "mglaman/composer-drupal-lenient": "^1.0"
    },
    "conflict": {
        "drupal/drupal": "*"
    },
    "minimum-stability": "dev",
    "prefer-stable": true,
    "config": {
        "allow-plugins": {
            "composer/installers": true,
            "drupal/core-composer-scaffold": true,
            "drupal/core-project-message": true,
            "phpstan/extension-installer": true,
            "dealerdirect/phpcodesniffer-composer-installer": true,
            "php-http/discovery": true,
            "cweagans/composer-patches": true,
            "mglaman/composer-drupal-lenient": true
        },
    },
```

Specify which Drupal module that composer should be lenient with: 

```
composer config --merge --json extra.drupal-lenient.allowed-list '["drupal/node_access_rebuild_progressive"]'
```
And `composer.json` gets this added:

```json
        "drupal-lenient": {
            "allowed-list": ["drupal/node_access_rebuild_progressive"]
        }
```

If you haven't already installed the [cweagans composer patches plugin](https://github.com/cweagans/composer-patches) use: 

```
composer require cweagans/composer-patches
```

Create the patch file `patches/node_access_rebuild_progressive_d10.patch` with the following contents.  It is on [drupal.org](https://www.drupal.org/project/node_access_rebuild_progressive/issues/3288770#comment-15227586).

```diff
diff --git docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml
index 1a0e13eec..f322ff847 100644
--- docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml
+++ docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.info.yml
@@ -1,7 +1,7 @@
 name: 'Node Access Rebuild Progressive'
 description: 'Rebuild node access grants in chunks'
 type: module
-core_version_requirement: ^8 || ^9
+core_version_requirement: ^9.4 || ^10
 
 # Information added by Drupal.org packaging script on 2020-06-23
 version: '2.0.0'
diff --git docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module
index 45f7c8a41..d2fc50637 100644
--- docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module
+++ docroot/modules/contrib/node_access_rebuild_progressive/node_access_rebuild_progressive.module
@@ -36,7 +36,7 @@ function node_access_rebuild_progressive_trigger() {
   node_access_needs_rebuild(FALSE);
   // Add default grants in the unlikely case
   // no modules implement node_grants anymore.
-  if (!count(\Drupal::moduleHandler()->getImplementations('node_grants'))) {
+  if (!count(\Drupal::moduleHandler()->hasImplementations('node_grants'))) {
     node_access_rebuild_progressive_set_default();
     return node_access_rebuild_progressive_finished();
   }

```

In composer.json add your patch as shown below.

```json
    "extra": {
        "drupal-scaffold": {
            "locations": {
                "web-root": "docroot/"
            },
            "file-mapping": {
                "[web-root]/sites/development.services.yml": false
            }
        },
        "installer-paths": {
            "docroot/core": [
                "type:drupal-core"
            ],
            "docroot/libraries/{$name}": [
                "type:drupal-library"
            ],
            "docroot/modules/contrib/{$name}": [
                "type:drupal-module"
            ],
            "docroot/profiles/contrib/{$name}": [
                "type:drupal-profile"
            ],
            "docroot/themes/contrib/{$name}": [
                "type:drupal-theme"
            ],
            "drush/Commands/contrib/{$name}": [
                "type:drupal-drush"
            ],
            "docroot/modules/custom/{$name}": [
                "type:drupal-custom-module"
            ],
            "docroot/profiles/custom/{$name}": [
                "type:drupal-custom-profile"
            ],
            "docroot/themes/custom/{$name}": [
                "type:drupal-custom-theme"
            ]
        },
        "patches": {
            "drupal/node_access_rebuild_progressive": {
                "Automated Drupal 10 compatibility fixes - 3288770": "patches/node_access_rebuild_progressive_d10.patch"
            }
        },

```

Install the module with:

`composer require drupal/node_access_rebuild_progressive`

The module will be installed and the patch applied!

More at
- [How to incorporate drupal 9-compatible modules into your drupal 10 project - Aug 2023](https://www.specbee.com/blogs/how-incorporate-drupal-9-compatible-modules-your-drupal-10-project)
- [https://github.com/mglaman/composer-drupal-lenient](https://github.com/mglaman/composer-drupal-lenient)
- [Using Drupal's Lenient Composer Endpoint - Sep 2023](https://www.drupal.org/docs/develop/using-composer/using-drupals-lenient-composer-endpoint)
- [Install a Contributed Module with No Drupal 9 Release - Feb 2023](https://drupalize.me/tutorial/install-contributed-module-no-drupal-9-release)


## Version constraints

1\. The caret constraint (`^`): this will allow any new versions
except BREAKING ones---in other words, the first number in the version
cannot increase, but the others can. `drupal/foo:^1.0` would allow
anything greater than or equal to 1.0 but less than 2.0.x. If you need
to specify a version, this is the recommended method.

2\. The tilde constraint (\~): this is a bit more restrictive than the
caret constraint. It means composer can download a higher version of the
last digit specified only. For example, drupal/foo:\~1.2 will allow
anything greater than or equal to version 1.2 (i.e., 1.2.0, 1.3.0,
1.4.0,...,1.999.999), but it won't allow that first 1 to increment to a
2.x release. Likewise, drupal/foo:\~1.2.3 will allow anything from 1.2.3
to 1.2.999, but not 1.3.0.

3\. The other constraints are a little more self-explanatory. You can
specify a version range with operators, a specific stability level
(e.g., -stable or -dev ), or even specify wildcards with \*.

Version range: By using comparison operators you can specify ranges of
valid versions. Valid operators are \>, \>=, \<, \<=, !=.

You can define multiple ranges. Ranges separated by a space ( ) or comma
(,) will be treated as a logical AND. A double pipe (\|\|) will be
treated as a logical OR. AND has higher precedence than OR.

Note: Be careful when using unbounded ranges as you might end up
unexpectedly installing versions that break backwards compatibility.
Consider using the caret operator instead for safety.


Examples:

- `>=1.0`

- `>=1.0 <2.0`

- `>=1.0 <1.1 || >=1.2`

[More in composer docs](https://getcomposer.org/doc/articles/versions.md)

## Allowing multiple versions

You can use double pipe (`||`) to specify multiple version. 

For the [CSV serialization](https://www.drupal.org/project/csv_serialization) module the author recommends using the following to install the module:
```
composer require drupal/csv_serialization:^2.0 || ^3.0
```

They say: \"It is not possible to support both Drupal 9.x and 10.x in a single release of this module due to a breaking change in `EncoderInterface::encode()` between Symfony 4.4 (D9) and Symfony 6.2 (D10). When preparing for an upgrade to Drupal 10 we recommend that you widen your Composer version constraints to allow either 2.x or 3.x: `composer require drupal/csv_serialization:^2.0 || ^3.0.` This will allow the module to be automatically upgraded when you upgrade Drupal core.\"

::: tip Note
TODO: I couldn't make this work.  Anyone want to weigh in on this?
:::

## Specify a particular version of PHP

You can specify the version of PHP in `composer.json` as shown below:

```json
"config": {

    "platform": {
        "php": "8.1"
    }

},
```

Here is more of the config section of a composer.json for clarity:

```json
    "config": {
        "platform": {
            "php": "8.1"
        },
        "allow-plugins": {
            "composer/installers": true,
            "drupal/core-composer-scaffold": true,
            "drupal/core-project-message": true,
            "phpstan/extension-installer": true,
            "dealerdirect/phpcodesniffer-composer-installer": true,
            "php-http/discovery": true
        },
        "sort-packages": true
    },
```

Levels of stability

- dev: This is the least stable version, and should never be used in production. It often includes packages under active development that may contain bugs or breaking changes and may still undergo significant modifications. It is only recommended for local development purposes.
- alpha: It's a version also undergoing development but in a more stable state. It usually contains fewer breaking changes and features nearing final completion or awaiting a beta release. However, it is also not highly recommended for production environments.
- beta: This version is currently being tested, and minor bugs, when noticed, will need to be fixed. However, it is more stable than the alpha and dev versions, but it's still not recommended for production purposes.
- RC: The RC (Release Candidate) is a version pending official release. It's the closest to being stable, but the version requires community testing and feedback prior to the final release. Undiscovered bugs can also be identified during this phase, so it's best practice not to use it for production purposes.
- stable: This is the required level for production purposes. It includes all packages that have gone through significant changes, bug fixes, community testing, feedback, and is now ready to use.

In your composer.json, you can specify the minimum stability by doing the following:

```json
{
    "minimum-stability": "stable"
}
```
For more:
- [freecodecamp.org](https://www.freecodecamp.org/news/what-is-minimum-stability-in-composer/)
- [getcomposer.org](https://getcomposer.org/doc/04-schema.md#minimum-stability)



## Composer bump

Composer 2.4 adds a new command called bump, that updates the requirements listed in the `composer.json` file with the currently installed version numbers. When the version numbers are bumped in the `composer.json` file, it effectively prevents Composer from installing a lower version of the required packages.  This can be useful when you have multiple people on a team who are updating composer and have to deal with version conflicts of the composer.lock file. See [php.watch article](https://php.watch/articles/composer-bump): 

To use the bump command with ddev, just run:

```sh
ddev composer bump
./composer.json has been updated (46 changes).
```





## Troubleshooting

### Composer won\'t update Drupal core

The `composer prohibits` (alias `why-not`) command tells you which packages are blocking a given package from being installed. Specify a version constraint to verify whether upgrades can be performed in your project, and if not why not.

Here we ask why won\'t composer install Drupal version 10.2.3?

```
composer why-not drupal/core 10.2.3
```

The output looks like:

```sh
drupal/core-recommended    10.1.8   requires         drupal/core (10.1.8)
drupal/core                10.2.3   conflicts        drush/drush (<12.4.3)
drupal/core                10.2.3   requires         symfony/console (^6.4)
drupal/recommended-project dev-main does not require symfony/console (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/dependency-injection (^6.4)
drupal/recommended-project dev-main does not require symfony/dependency-injection (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/event-dispatcher (^6.4)
drupal/recommended-project dev-main does not require symfony/event-dispatcher (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/http-foundation (^6.4)
drupal/recommended-project dev-main does not require symfony/http-foundation (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/http-kernel (^6.4)
drupal/recommended-project dev-main does not require symfony/http-kernel (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/mime (^6.4)
drupal/recommended-project dev-main does not require symfony/mime (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/routing (^6.4)
drupal/recommended-project dev-main does not require symfony/routing (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/serializer (^6.4)
drupal/recommended-project dev-main does not require symfony/serializer (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/validator (^6.4)
drupal/recommended-project dev-main does not require symfony/validator (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/process (^6.4)
drupal/recommended-project dev-main does not require symfony/process (but v6.3.12 is installed)
drupal/core                10.2.3   requires         symfony/yaml (^6.4)
drupal/recommended-project dev-main does not require symfony/yaml (but v6.3.12 is installed)
Not finding what you were looking for? Try calling `composer update "drupal/core:10.2.3" --dry-run` to get another view on the problem.
```

To solve this, I removed drush with `composer remove drush/drush` and then ran `composer update "drupal/core-*" -W`

Then I reinstalled the correct version of drush with `composer require drush/drush` which installed version 12.5.1 of drush.



### Composer won\'t install a module

In this case I am trying to install the `csv_serialization` module.  I get the following error:

```sh
composer require 'drupal/csv_serialization:^4.0'
./composer.json has been updated
Running composer update drupal/csv_serialization
Gathering patches for root package.
Loading composer repositories with package information
Updating dependencies
Your requirements could not be resolved to an installable set of packages.

  Problem 1
    - drupal/views_data_export is locked to version 1.3.0 and an update of this package was not requested.
    - drupal/views_data_export 1.3.0 requires drupal/csv_serialization ~1.4 || ~2.0 || ~3 -> found drupal/csv_serialization[dev-1.x, dev-2.x, dev-3.x, 1.4.0, 1.5.0, 1.x-dev (alias of dev-1.x), 2.0.0-beta1, ..., 2.x-dev (alias of dev-2.x), 3.0.0-beta1, ..., 3.x-dev (alias of dev-3.x)] but it conflicts with your root composer.json require (^4.0).

Use the option --with-all-dependencies (-W) to allow upgrades, downgrades and removals for packages currently locked to specific versions.

Installation failed, reverting ./composer.json and ./composer.lock to their original content.
```

So I can try the `why-not` command to see why it won't install:

```sh
composer why-not drupal/csv_serialization ^4.0
drupal/recommended-project dev-master requires drupal/csv_serialization (^3.0)
drupal/views_data_export   1.3.0      requires drupal/csv_serialization (~1.4 || ~2.0 || ~3)
Not finding what you were looking for? Try calling `composer update "drupal/csv_serialization:^4.0" --dry-run` to get another view on the problem.
```

So it looks like the `drupal/recommended-project` requires `drupal/csv_serialization ^3.0` which should not be a problem. Also `drupal/views_data_export` requires `~1.4 || ~2.0 || ~3`.  

I can try the `--dry-run` option to see what happens:

```sh
composer update drupal/csv_serialization:^4.0 --dry-run

In UpdateCommand.php line 163:

  The temporary constraint "^4.0" for "drupal/csv_serialization" must be a subset of the constraint in your composer.js
  on (^3.0)
```

Well, that's not very helpful.  I try updating drupal/views_data_export which succeeds:

```sh
composer update drupal/views_data_export
Gathering patches for root package.
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 1 update, 0 removals
  - Upgrading drupal/views_data_export (1.3.0 => 1.4.0)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 1 update, 0 removals
  - Downloading drupal/views_data_export (1.4.0)
Gathering patches for root package.
Gathering patches for dependencies. This might take a minute.
  - Upgrading drupal/views_data_export (1.3.0 => 1.4.0): Extracting archive
  - Applying patches for drupal/views_data_export
    https://www.drupal.org/files/issues/2021-02-17/2887450-40.patch (Add drush command views-data-export)
...
```

Now I try to install the module again:

```sh
composer require 'drupal/csv_serialization:^4.0'
./composer.json has been updated
Running composer update drupal/csv_serialization
Gathering patches for root package.
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 1 update, 0 removals
  - Upgrading drupal/csv_serialization (3.0.0 => 4.0.0)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 1 update, 0 removals
  - Downloading drupal/csv_serialization (4.0.0)
Gathering patches for root package.
Gathering patches for dependencies. This might take a minute.
  - Upgrading drupal/csv_serialization (3.0.0 => 4.0.0): Extracting archive
Package webmozart/path-util is abandoned, you should avoid using it. Use symfony/filesystem instead.
Generating autoload files
99 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
phpstan/extension-installer: Extensions installed
Found 1 security vulnerability advisory affecting 1 package.
Run "composer audit" for a full list of advisories.
```


### Composer won\'t update a module
In this instance I want to update a Drupal 10 site with the [metatag](https://www.drupal.org/project/metatag) module. This site has the `"drupal/metatag": "^1.26",` version and there is a `2.02` version available I try to update the module with:

```sh
ddev composer update drupal/metatag

Gathering patches from patch file.
Loading composer repositories with package information
Updating dependencies
Nothing to modify in lock file
Installing dependencies from lock file (including require-dev)
Nothing to install, update or remove
```

Checking to see what is blocking the update:

```sh
 ddev composer prohibits drupal/metatag 2.0
udda/udda_rd          -     requires drupal/metatag (^1.26)
drupal/schema_metatag 2.6.0 requires drupal/metatag (^1.0)
Not finding what you were looking for? Try calling `composer require "drupal/metatag:2.0" --dry-run` to get another view on the problem.
Composer [prohibits drupal/metatag 2.0] failed, composer command failed: exit status 1. stderr=
```

Note. the `udda/udda_rd` represents the entire project and so it i listed as requiring `drupal\metatag`.



Poking around on Drupal.org, I find that there is also an update to the `schema_metatag` module to version `3.0.3` . Trying to update the `schema_metatag` module fails as they are dependent on each other:

```sh
ddev composer update drupal/metatag
Gathering patches from patch file.
Loading composer repositories with package information
Updating dependencies
Nothing to modify in lock file
Installing dependencies from lock file (including require-dev)
Nothing to install, update or remove
```


Checking to see what is blocking either update is not very useful. Composer sees that the project requires the old version of `metatag`:

```sh
ddev composer prohibits drupal/metatag 2.0.2 -t
drupal/metatag 2.0.2 Manage meta tags for all entities.
└──udda/udda_rd (requires drupal/metatag ^1.26)
Not finding what you were looking for? Try calling `composer require "drupal/metatag:2.0.2" --dry-run` to get another view on the problem.
```
and the same for `schema_metatag`.
  
```sh
ddev composer prohibits drupal/schema_metatag 3.0.3 -t
drupal/schema_metatag 3.0.3 Metatag implementation of Schema.org structured data (JSON-LD)
└──udda/udda_rd (requires drupal/schema_metatag ^2.6)
Not finding what you were looking for? Try calling `composer require "drupal/schema_metatag:3.0.3" --dry-run` to get another view on the problem.
```


To update both at the same time use the following:
  
```sh
  ddev composer require drupal/schema_metatag:^3.0 drupal/metatag:^2.0
./composer.json has been updated
Running composer update drupal/schema_metatag drupal/metatag
Gathering patches from patch file.
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 2 updates, 0 removals
  - Upgrading drupal/metatag (1.26.0 => 2.0.2)
  - Upgrading drupal/schema_metatag (2.6.0 => 3.0.3)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 2 updates, 0 removals
Gathering patches from patch file.
Gathering patches for dependencies. This might take a minute.
  - Upgrading drupal/metatag (1.26.0 => 2.0.2): Extracting archive
  - Upgrading drupal/schema_metatag (2.6.0 => 3.0.3): Extracting archive
Generating autoload files
109 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
phpstan/extension-installer: Extensions installed
Scaffolding files for drupal/core:
  - Copy [web-root]/.eslintrc.json from assets/scaffold/files/eslintrc.json
  - Skip [web-root]/sites/development.services.yml: overridden in udda/udda_rd
Scaffolding files for udda/udda_rd:
  - Skip [web-root]/sites/development.services.yml because it already exists and overwrite is false.
> cd web && patch -p1 <../patches/eslint-additional-rules.patch
patching file .eslintrc.json
Found 3 security vulnerability advisories affecting 3 packages.
Run "composer audit" for a full list of advisories.
```



Another solution is to update both modules at the same time with dependencies (`-W`) which updates several other items including some Symfony components. I'm a fan of this approach as it keeps everything up to date:

```sh
ddev composer require drupal/schema_metatag:^3.0 drupal/metatag:^2.0 -W

./composer.json has been updated
Running composer update drupal/schema_metatag drupal/metatag --with-all-dependencies
Gathering patches from patch file.
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 24 updates, 0 removals
  - Upgrading composer/semver (3.4.2 => 3.4.3)
  - Upgrading doctrine/annotations (1.14.3 => 1.14.4)
  - Upgrading drupal/metatag (1.26.0 => 2.0.2)
  - Upgrading drupal/schema_metatag (2.6.0 => 3.0.3)
  - Upgrading guzzlehttp/promises (2.0.3 => 2.0.4)
  - Upgrading phpstan/phpdoc-parser (1.30.0 => 1.33.0)
  - Upgrading psr/log (3.0.1 => 3.0.2)
  - Upgrading symfony/console (v6.4.11 => v6.4.12)
  - Upgrading symfony/dependency-injection (v6.4.11 => v6.4.12)
  - Upgrading symfony/filesystem (v6.4.9 => v6.4.12)
  - Upgrading symfony/http-foundation (v6.4.10 => v6.4.12)
  - Upgrading symfony/http-kernel (v6.4.11 => v6.4.12)
  - Upgrading symfony/mailer (v6.4.9 => v6.4.12)
  - Upgrading symfony/mime (v6.4.11 => v6.4.12)
  - Upgrading symfony/polyfill-php72 (v1.30.0 => v1.31.0)
  - Upgrading symfony/polyfill-php73 (v1.30.0 => v1.31.0)
  - Upgrading symfony/polyfill-php80 (v1.30.0 => v1.31.0)
  - Upgrading symfony/polyfill-php81 (v1.30.0 => v1.31.0)
  - Upgrading symfony/process (v6.4.8 => v6.4.12)
  - Upgrading symfony/routing (v6.4.11 => v6.4.12)
  - Upgrading symfony/serializer (v6.4.11 => v6.4.12)
  - Upgrading symfony/string (v6.4.11 => v6.4.12)
  - Upgrading symfony/validator (v6.4.11 => v6.4.12)
  - Upgrading symfony/yaml (v6.4.11 => v6.4.12)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 24 updates, 0 removals
Gathering patches from patch file.
Gathering patches for dependencies. This might take a minute.
  - Upgrading symfony/string (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/console (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading psr/log (3.0.1 => 3.0.2): Extracting archive
  - Upgrading symfony/filesystem (v6.4.9 => v6.4.12): Extracting archive
  - Upgrading composer/semver (3.4.2 => 3.4.3): Extracting archive
  - Upgrading symfony/polyfill-php80 (v1.30.0 => v1.31.0): Extracting archive
  - Upgrading symfony/yaml (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/validator (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/serializer (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/routing (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/http-foundation (v6.4.10 => v6.4.12): Extracting archive
  - Upgrading symfony/process (v6.4.8 => v6.4.12): Extracting archive
  - Removing symfony/polyfill-php72 (v1.30.0)
  - Upgrading symfony/mime (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/mailer (v6.4.9 => v6.4.12): Extracting archive
  - Upgrading symfony/http-kernel (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading symfony/dependency-injection (v6.4.11 => v6.4.12): Extracting archive
  - Upgrading guzzlehttp/promises (2.0.3 => 2.0.4): Extracting archive
  - Upgrading doctrine/annotations (1.14.3 => 1.14.4): Extracting archive
  - Upgrading phpstan/phpdoc-parser (1.30.0 => 1.33.0): Extracting archive
  - Upgrading symfony/polyfill-php81 (v1.30.0 => v1.31.0): Extracting archive
  - Upgrading symfony/polyfill-php73 (v1.30.0 => v1.31.0): Extracting archive
  - Upgrading drupal/metatag (1.26.0 => 2.0.2): Extracting archive
  - Upgrading drupal/schema_metatag (2.6.0 => 3.0.3): Extracting archive
 10/24 [===========>----------------]  41%  - Installing symfony/polyfill-php72 (v1.31.0)
Generating autoload files
109 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
phpstan/extension-installer: Extensions installed
Scaffolding files for drupal/core:
  - Copy [web-root]/.eslintrc.json from assets/scaffold/files/eslintrc.json
  - Skip [web-root]/sites/development.services.yml: overridden in udda/udda_rd
Scaffolding files for udda/udda_rd:
  - Skip [web-root]/sites/development.services.yml because it already exists and overwrite is false.
> cd web && patch -p1 <../patches/eslint-additional-rules.patch
patching file .eslintrc.json
Found 3 security vulnerability advisories affecting 3 packages.
Run "composer audit" for a full list of advisories.
```





### The big reset button

If composer barfs with a bunch of errors, try removing vendor, /core,
modules/contrib (and optionally composer.lock using:

```sh
$ rm -fr core/ modules/contrib/ vendor/
```
Then try run composer install again to see how it does:

```sh
$ composer install --ignore-platform-reqs
```
Note `--ignore-platform-reqs` is only necessary if your php on your host
computer is different to the version in your DDEV containers.

You could always use this for DDEV:

```sh
$ ddev composer install
```

## Reference

- [Drupal 8 composer best practices - Jan 2018](https://www.lullabot.com/articles/drupal-8-composer-best-practices)
- [Making a patch - Dec 2022](https://www.drupal.org/node/707484)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Composer documentation article on versions and constraints](https://getcomposer.org/doc/articles/versions.md)
- [Using Drupal's Composer Scaffold updated Dec 2022](https://www.drupal.org/docs/develop/using-composer/using-drupals-composer-scaffold#toc_6)
- [Drupal 9 and Composer Patches by Adrian Vazquez Peligero June 2021](https://vazcell.com/blog/how-apply-patch-drupal-9-composer)
- [Managing patches with Composer March 2022](https://acquia.my.site.com/s/article/360048081193-Managing-patches-with-Composer)
- [Utilizing incompatible Drupal 9 modules with Drupal 10 - Aug 2023 ](https://www.specbee.com/blogs/how-incorporate-drupal-9-compatible-modules-your-drupal-10-project)
- [Install a Contributed Module with No Drupal 9 Release - Feb 2023](https://drupalize.me/tutorial/install-contributed-module-no-drupal-9-release)
- [Using Drupal's Lenient Composer Endpoint - Sep 2023](https://www.drupal.org/docs/develop/using-composer/using-drupals-lenient-composer-endpoint)
- [Updating Drupal core via composer updated Dec 2023](https://www.drupal.org/docs/updating-drupal/updating-drupal-core-via-composer)
- [Updating Drupal](https://www.drupal.org/docs/updating-drupal)

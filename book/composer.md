---
layout: default
title: Composer
permalink: /composer
last_modified_date: '2023-09-12'
---

# Composer, Updates and Patches
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}


![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=composer.md)

---

## Creating a local patch to a contrib module

See Making a patch at <https://www.drupal.org/node/707484>

In this case, I had the file_entity module installed and wanted to hide
the tab "[files.]{.underline}" The tab item is provided by a task (read
"menu tab") in the
web/modules/contrib/file_entity/file_entity.links.task.yml

```yaml
entity.file.collection:
  route_name: entity.file.collection
  base_route: system.admin_content
  title: 'Files'
  description: 'Manage files for your site.'
```

For my patch, I want to remove this section of the `file_entity.links.task.yml` file.

First get the repo/git version of the module

```sh
$ composer update drupal/file_entity --prefer-source
```

Change the file in the text editor

Run git diff to see the changes:

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

Create the patch

```sh
git diff >file_entity_disable_file_menu_tab.patch
```

Add the patch to the patches section of composer.json. Notice below the line starting with \"drupal/file_entity\" is the local file patch:

```json
"patches": {
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
    "drupal/file_entity": {
        "Temporarily disable the files menu tab": "./patches/file_entity_disable_file_menu_tab.patch"
    }
}
```

Revert the file in git and then try to apply the patch.

Here is the patch command way to un-apply or revert a patch (-R means
revert)

```
patch -p1 -R < ./patches/fix_scary_module.patch
```
To apply the patch:

```
patch -p1 < ./patches/fix_scary_module.patch
```

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

Using the URL of the merge request, add .patch at the end of the URL and that will be the path to the latest patch.

e.g. for a merge request at [https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2](https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2) or [https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2/diffs?view=parallel](https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2/diffs?view=parallel)

The patch is at [https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2.patch](https://git.drupalcode.org/project/alt_stream_wrappers/-/merge_requests/2.patch)



## composer.json patches in separate file

To separate patches into a different file other than composer json add
`"patches-file"` section under `"extra"`. See example below:

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
```
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

```
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



## Updating Drupal Core

if there is `drupal/core-recommended` in your `composer.json` use:

```sh
$ composer update drupal/core-recommended -W
```

if there is no `drupal/core-recommended` in your `composer.json` use:

```sh
$ composer update drupal/core -W
```

Note `composer update -W` is the same as `composer update --with-dependencies`

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

## Version constraints

1\. The caret constraint (`^`): this will allow any new versions
except BREAKING ones---in other words, the first number in the version
cannot increase, but the others can. `drupal/foo:^1.0` would allow
anything greater than or equal to 1.0 but less than 2.0.x . If you need
to specify a version, this is the recommended method.

2\. The tilde constraint (\~): this is a bit more restrictive than the
caret constraint. It means composer can download a higher version of the
last digit specified only. For example, drupal/foo:\~1.2 will allow
anything greater than or equal to version 1.2 (i.e., 1.2.0, 1.3.0,
1.4.0,...,1.999.999), but it won't allow that first 1 to increment to a
2.x release. Likewise, drupal/foo:\~1.2.3 will allow anything from 1.2.3
to 1.2.999, but not 1.3.0.

3\. The other constraints are a little more self-explanatory. You can
specify a version range with operators, a specific stability level
(e.g., -stable or -dev ), or even specify wildcards with \*.

Version range: By using comparison operators you can specify ranges of
valid versions. Valid operators are \>, \>=, \<, \<=, !=.

You can define multiple ranges. Ranges separated by a space ( ) or comma
(,) will be treated as a logical AND. A double pipe (\|\|) will be
treated as a logical OR. AND has higher precedence than OR.

Note: Be careful when using unbounded ranges as you might end up
unexpectedly installing versions that break backwards compatibility.
Consider using the caret operator instead for safety.

Examples:

• \>=1.0

• \>=1.0 \<2.0

• \>=1.0 \<1.1 \|\| \>=1.2

More at <https://getcomposer.org/doc/articles/versions.md>

## Allowing multiple versions

You can use double pipe (`||`) to specify multiple version. 

For the [CSV serialization](https://www.drupal.org/project/csv_serialization) module the author recommends using the following to install the module:
```
composer require drupal/csv_serialization:^2.0 || ^3.0
```

They say: \"It is not possible to support both Drupal 9.x and 10.x in a single release of this module due to a breaking change in EncoderInterface::encode() between Symfony 4.4 (D9) and Symfony 6.2 (D10). When preparing for an upgrade to Drupal 10 we recommend that you widen your Composer version constraints to allow either 2.x or 3.x: `composer require drupal/csv_serialization:^2.0 || ^3.0.` This will allow the module to be automatically upgraded when you upgrade Drupal core.\"





## Troubleshooting

### Composer won\'t update Drupal core

The `prohibits` command tells you which packages are blocking a given package from being installed. Specify a version constraint to verify whether upgrades can be performed in your project, and if not why not.

Why won\'t composer install Drupal version 8.9.1.

composer why-not drupal/core:8.9.1

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

---
title: Updates
---

# Upgrading and patching Drupal and contrib modules
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=upgrade.md)

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


## Upgrading Drupal 9 to Drupal 10

Much of this is from [Drupalize.me - March 2023](https://drupalize.me/tutorial/upgrade-drupal-10)

### CKEditor
- Install the latest and greatest Drupal 9.x version
- If you are using CKEditor, move to CKEditor 5
  - make sure your CKEditor plugins have CKEditor 5 versions (or remove those that don't)
  - Convert text formats to use CKEditor 5. Text formats must be updated one at a time, but switching the editor to CKEditor 5 will automatically migrate your text format configuration to CKEditor 5.
  - Using the Manage administrative menu, navigate to Configuration > Content authoring > Text formats and editors. In the column labeled Text editor, you can tell which text formats are using CKEditor.
  - For each text format that uses CKEditor, under the Operations column, select Configure. and under Text editor, select CKEditor 5.
  - On the configuration page for the text format, under Text editor, select CKEditor 5. Status messages will appear letting you know what changed.
  - After you have updated all CKEditor text formats to CKEditor 5, on the Text formats administrative page, you should see CKEditor 5 listed next to each text format you updated.
  - Check that CKEditor 5 is working ok and uninstall CKEditor.

### Contrib Modules
- Update all your contributed modules and themes to Drupal 10 compatible versions while you're still on Drupal 9.
- Finally use composer to make sure you have the latest of everything with `ddev composer update -W`
- If Drush version 10.x is installed, use composer to remove it with `composer remove drush/drush`.  if you don't, composer will be unable to upgrade your site as Drupal 10 requires Drush version 12.



### The Upgrade Status Module
- Install the [Upgrade Status](https://www.drupal.org/project/upgrade_status) module to give you all the recommendations required for the upgrade.
- Review the report at `https://tea2.ddev.site/admin/reports/upgrade-status`
  - Make sure you are at the required version of Drupal 9. i.e. 9.4.x
  - Follow the recommendations to remove projects in the `remove` section
  - Be sure to uninstall Drupal 9 core modules like: Color, RDF, and themes like: Bartik, Seven and Stable.
  - If Drush version 10.x is installed, use composer to remove it with `composer remove drush/drush`.  if you don't, composer will be unable to upgrade your site as Drupal 10 requires Drush version 12. (sorry to repeat but it will bit you!)
  - Update code in modules under the `scan` section
  - Install updated versions of the modules in the `Collaborate with maintainers` section
  - Uninstall and remove the Upgrade Status module before upgrading or else upgrading to D10 will fail. 

::: tip Note
Using the --no-update flag updates the composer.json entries, without attempting to resolve and download any files. This allows us to batch updates to projects and avoid a "chicken-or-egg first"-type of issues with shared dependencies. Alternatively, you can edit the version constraints in composer.json manually.
:::


Uninstall the modules listed in the `Drupal core and hosting environment section` via the Drupal user interface 
![Drupal core and hosting environment section](/images/upgrade_status_drupal_core_and_hosting.png)

If you don't remove them, you will see something like this when you try to run `drush updb`:

```sh
$ drush updb
 [error]   (Currently using Removed core modules You must add the following contributed modules and reload this page.
 * CKEditor [1]
 * Color [2]
 * Quick Edit [3]
 * RDF [4]

These modules are installed on your site but are no longer provided by Core.
For more information read the documentation on deprecated modules. [5]

[1] https://www.drupal.org/project/ckeditor
[2] https://www.drupal.org/project/color
[3] https://www.drupal.org/project/quickedit
[4] https://www.drupal.org/project/rdf
[5] https://www.drupal.org/node/3223395#s-recommendations-for-deprecated-modules
)
```

this is what that section of Upgrade status looks like when it is completely ready to go:

![Upgrade status core hosting section all green](/images/upgrade_status_core_hosting_all_green.png)


Modules that need to be removed as they are not installed:

![Uninstalled modules to be removed](/images/upgrade_status_remove_section.png)

Deprecated or obsolete core extensions installed.  Upgrade these to Drupal 10 compatible versions.

![Deprecated or obsolete core extensions](/images/upgrade_status_deprecated_core.png)

Modules to update:
![Modules to update](/images/upgrade_status_update_section.png)

Compatible section:
![Compatible section](/images/upgrade_status_compatible_section.png)

::: tip Note
In the compatible section, you may notice that some items show `unchecked` in the `Drupal.org 10-ready column`.  No action is required for these items as they are submodules that are not installed.
![unchecked items](/images/upgrade_status_uninstalled.png)
:::



As you finish each section you can confirm that everything is complete by clicking the `Check available updates` on the upgrade status module.  If you get WSOD, you may have to run `ddev drush updb` periodically and clear caches with `ddev drush cr`

![Recheck available updates](/images/upgrade_status_recheck.png)





### Update core

  - Update drupal/core-dev
If you have the drupal/core-dev dependencies in your composer.json, update those with:
```
composer require drupal/core-dev:^10.0 --dev --no-update --update-with-dependencies
```
  - Update drupal/core-* projects: i.e. the drupal/core-recommended, drupal/core-composer-scaffold, and drupal/core-project-message projects

```
composer require drupal/core-recommended:^10.0 drupal/core-composer-scaffold:^10.0 drupal/core-project-message:^10.0 --no-update --update-with-all-dependencies
```

  - Then tell Composer to try and resolve and download all the new code:

```
composer update -W
```
If composer fails to install, confirm that you removed drush previously.  Otherwise follow standard procedures for troubleshooting Composer problems.

If all goes smoothly, you can now reinstall drush with `composer require drush/drush`.  

### Finish up
- clear caches and run database updates
  - `drush cr`
  - `drush updb -y`

The output from updating the database with `drush updb` will look something like this:

```
$ drush updb
 --------------- ------------------ --------------- ---------------------------
  Module          Update ID          Type            Description
 --------------- ------------------ --------------- ---------------------------
  system          10100              hook_update_n   10100 - Remove the year
                                                     2038 date limitation.
  system          10101              hook_update_n   10101 - Change the
                                                     {batch} table [bid] field
                                                     to serial.
  system          10201              hook_update_n   10201 - Clear left over
                                                     entries in the revision
                                                     data table.
  block_content   10100              hook_update_n   10100 - Update entity
                                                     definition to handle
                                                     revision routes.
  block_content   10200              hook_update_n   10200 - Remove the unique
                                                     values constraint from
                                                     block content info
                                                     fields.
  comment         10100              hook_update_n   10100 - Remove the year
                                                     2038 date limitation.
  dblog           10100              hook_update_n   10100 - Remove the year
                                                     2038 date limitation.
  dblog           10101              hook_update_n   10101 - Converts the
                                                     'wid' of the 'watchdog'
                                                     table to a big integer.
  help            10200              hook_update_n   10200 - Install search
                                                     index table for help
                                                     topics.
  history         10100              hook_update_n   10100 - Remove the year
                                                     2038 date limitation.
  user            10000              hook_update_n   10000 - Remove
                                                     non-existent permissions
                                                     created by migrations.
  big_pipe        html5_placeholde   post-update     Clear the render cache.
                  rs
  block_content   block_library_vi   post-update     Update block_content
                  ew_permission                      'block library' view
                                                     permission.
  block_content   move_custom_bloc   post-update     Moves the custom block
                  k_library                          library to Content.
  block_content   sort_permissions   post-update     Update permissions for
                                                     users with "administer
                                                     blocks" permission.
  ckeditor5       code_block         post-update     Updates Text Editors
                                                     using CKEditor 5 Code
                                                     Block.
  ckeditor5       list_multiblock    post-update     Updates Text Editors
                                                     using CKEditor 5.
  ckeditor5       list_start_rever   post-update     Updates Text Editors
                  sed                                using CKEditor 5 to
                                                     native List "start"
                                                     functionality.
  ckeditor5       plugins_settings   post-update     Updates Text Editors
                  _export_order                      using CKEditor 5 to sort
                                                     plugin settings by plugin
                                                     key.
  editor          image_lazy_load    post-update     Enable
                                                     filter_image_lazy_load if
                                                     editor_file_reference is
                                                     enabled.
  file            add_default_file   post-update     Add default filename
                  name_sanitizatio                   sanitization
                  n_configuration                    configuration.
  file            add_permissions_   post-update     Grant all non-anonymous
                  to_roles                           roles the 'delete own
                                                     files' permission.
  filter          sort_filters       post-update     Sorts filter format
                                                     filter configuration.
  help            add_permissions_   post-update     Grant all admin roles the
                  to_roles                           'access help pages'
                                                     permission.
  help            help_topics_sear   post-update     Install or update config
                  ch                                 for help topics if the
                                                     search module installed.
  help            help_topics_unin   post-update     Uninstall the help_topics
                  stall                              module if installed.
  media           oembed_loading_a   post-update     Add the oEmbed loading
                  ttribute                           attribute setting to
                                                     field formatter
                                                     instances.
  media           set_blank_iframe   post-update     Updates
                  _domain_to_null                    media.settings:iframe_dom
                                                     ain config if it's still
                                                     at the default.
  olivero         add_olivero_prim   post-update     Sets the default
                  ary_color                          `base_primary_color`
                                                     value of Olivero's theme
                                                     settings.
  path_alias      drop_path_alias_   post-update     Remove the
                  status_index                       path_alias__status index.
  system          add_description_   post-update     Update description for
                  to_entity_form_m                   form modes.
                  ode
  system          add_description_   post-update     Update description for
                  to_entity_view_m                   view modes.
                  ode
  system          enable_password_   post-update     Enable the password
                  compatibility                      compatibility module.
  system          linkset_settings   post-update     Add new menu linkset
                                                     endpoint setting.
  system          mailer_dsn_setti   post-update     Add new default mail
                  ngs                                transport dsn.
  system          mailer_structure   post-update     Add new default mail
                  d_dsn_settings                     transport dsn.
  system          remove_asset_ent   post-update     Remove redundant asset
                  ries                               state and config.
  system          remove_asset_que   post-update     Remove redundant asset
                  ry_string                          query string state.
  system          set_blank_log_ur   post-update     Updates
                  l_to_null                          system.theme.global:logo.
                                                     url config if it's still
                                                     at the default.
  system          timestamp_format   post-update     Update timestamp
                  ter                                formatter settings for
                                                     entity view displays.
  text            allowed_formats    post-update     Add allowed_formats
                                                     setting to existing text
                                                     fields.
  update          set_blank_fetch_   post-update     Updates
                  url_to_null                        update.settings:fetch.url
                                                     config if it's still at
                                                     the default.
  views           add_missing_labe   post-update     Add labels to views which
                  ls                                 don't have one.
  views           boolean_custom_t   post-update     Update Views config
                  itles                              schema to make boolean
                                                     custom titles
                                                     translatable.
  views           fix_revision_id_   post-update     Fix '-revision_id'
                  part                               replacement token syntax.
  views           oembed_eager_loa   post-update     Add eager load option to
                  d                                  all oembed type field
                                                     configurations.
  views           remove_default_a   post-update     Remove
                  rgument_skip_url                   default_argument_skip_url
                                                     setting.
  views           remove_skip_cach   post-update     Remove the skip_cache
                  e_setting                          settings.
  views           responsive_image   post-update     Add lazy load options to
                  _lazy_load                         all responsive image type
                                                     field configurations.
  views           taxonomy_filter_   post-update     Removes User context from
                  user_context                       views with taxonomy
                                                     filters.
  views           timestamp_format   post-update     Update timestamp
                  ter                                formatter settings for
                                                     views.
 --------------- ------------------ --------------- ---------------------------


 Do you wish to run the specified pending updates? (yes/no) [yes]:
>  [notice] Update started: system_update_10100
>  [notice] Update completed: system_update_10100
>  [notice] Update started: system_update_10101
>  [notice] Update completed: system_update_10101
>  [notice] Update started: block_content_update_10100
>  [notice] Added revision routes to Content block entity type.
>  [notice] Update completed: block_content_update_10100
>  [notice] Update started: dblog_update_10100
>  [notice] Update completed: dblog_update_10100
>  [notice] Update started: system_update_10201
>  [notice] Update completed: system_update_10201
>  [notice] Update started: block_content_update_10200
>  [notice] Update completed: block_content_update_10200
>  [notice] Update started: comment_update_10100
>  [notice] Update completed: comment_update_10100
>  [notice] Update started: dblog_update_10101
>  [notice] Update completed: dblog_update_10101
>  [notice] Update started: help_update_10200
>  [notice] Update completed: help_update_10200
>  [notice] Update started: history_update_10100
>  [notice] Update completed: history_update_10100
>  [notice] Update started: user_update_10000
>  [notice] Update completed: user_update_10000
>  [notice] Update started: big_pipe_post_update_html5_placeholders
>  [notice] Update completed: big_pipe_post_update_html5_placeholders
>  [notice] Update started: block_content_post_update_block_library_view_permission
>  [notice] Update completed: block_content_post_update_block_library_view_permission
>  [notice] Update started: block_content_post_update_move_custom_block_library
>  [notice] Update completed: block_content_post_update_move_custom_block_library
>  [notice] Update started: block_content_post_update_sort_permissions
>  [notice] Update completed: block_content_post_update_sort_permissions
>  [notice] Update started: ckeditor5_post_update_code_block
>  [notice] Update completed: ckeditor5_post_update_code_block
>  [notice] Update started: ckeditor5_post_update_list_multiblock
>  [notice] Update completed: ckeditor5_post_update_list_multiblock
>  [notice] Update started: ckeditor5_post_update_list_start_reversed
>  [notice] Update completed: ckeditor5_post_update_list_start_reversed
>  [notice] Update started: ckeditor5_post_update_plugins_settings_export_order
>  [notice] Update completed: ckeditor5_post_update_plugins_settings_export_order
>  [notice] Update started: editor_post_update_image_lazy_load
>  [notice] Update completed: editor_post_update_image_lazy_load
>  [notice] Update started: file_post_update_add_default_filename_sanitization_configuration
>  [notice] Update completed: file_post_update_add_default_filename_sanitization_configuration
>  [notice] Update started: file_post_update_add_permissions_to_roles
>  [notice] Update completed: file_post_update_add_permissions_to_roles
>  [notice] Update started: filter_post_update_sort_filters
>  [notice] Update completed: filter_post_update_sort_filters
>  [notice] Update started: help_post_update_add_permissions_to_roles
>  [notice] Update completed: help_post_update_add_permissions_to_roles
>  [notice] Update started: help_post_update_help_topics_search
>  [notice] Update completed: help_post_update_help_topics_search
>  [notice] Update started: help_post_update_help_topics_uninstall
>  [notice] Update completed: help_post_update_help_topics_uninstall
>  [notice] Update started: media_post_update_oembed_loading_attribute
>  [notice] Update completed: media_post_update_oembed_loading_attribute
>  [notice] Update started: media_post_update_set_blank_iframe_domain_to_null
>  [notice] Update completed: media_post_update_set_blank_iframe_domain_to_null
>  [notice] Update started: olivero_post_update_add_olivero_primary_color
>  [notice] Update completed: olivero_post_update_add_olivero_primary_color
>  [notice] Update started: path_alias_post_update_drop_path_alias_status_index
>  [notice] Update completed: path_alias_post_update_drop_path_alias_status_index
>  [notice] Update started: system_post_update_add_description_to_entity_form_mode
>  [notice] Update completed: system_post_update_add_description_to_entity_form_mode
>  [notice] Update started: system_post_update_add_description_to_entity_view_mode
>  [notice] Update completed: system_post_update_add_description_to_entity_view_mode
>  [notice] Update started: system_post_update_enable_password_compatibility
>  [notice] Update completed: system_post_update_enable_password_compatibility
>  [notice] Update started: system_post_update_linkset_settings
>  [notice] Update completed: system_post_update_linkset_settings
>  [notice] Update started: system_post_update_mailer_dsn_settings
>  [notice] Update completed: system_post_update_mailer_dsn_settings
>  [notice] Update started: system_post_update_mailer_structured_dsn_settings
>  [notice] Update completed: system_post_update_mailer_structured_dsn_settings
>  [notice] Update started: system_post_update_remove_asset_entries
>  [notice] Update completed: system_post_update_remove_asset_entries
>  [notice] Update started: system_post_update_remove_asset_query_string
>  [notice] Update completed: system_post_update_remove_asset_query_string
>  [notice] Update started: system_post_update_set_blank_log_url_to_null
>  [notice] Update completed: system_post_update_set_blank_log_url_to_null
>  [notice] Update started: system_post_update_timestamp_formatter
>  [notice] Update completed: system_post_update_timestamp_formatter
>  [notice] Update started: text_post_update_allowed_formats
>  [notice] Update completed: text_post_update_allowed_formats
>  [notice] Update started: update_post_update_set_blank_fetch_url_to_null
>  [notice] Update completed: update_post_update_set_blank_fetch_url_to_null
>  [notice] Update started: views_post_update_add_missing_labels
>  [notice] Update completed: views_post_update_add_missing_labels
>  [notice] Update started: views_post_update_boolean_custom_titles
>  [notice] Update completed: views_post_update_boolean_custom_titles
>  [notice] Update started: views_post_update_fix_revision_id_part
>  [notice] Update completed: views_post_update_fix_revision_id_part
>  [notice] Update started: views_post_update_oembed_eager_load
>  [notice] Update completed: views_post_update_oembed_eager_load
>  [notice] Update started: views_post_update_remove_default_argument_skip_url
>  [notice] Update completed: views_post_update_remove_default_argument_skip_url
>  [notice] Update started: views_post_update_remove_skip_cache_setting
>  [notice] Update completed: views_post_update_remove_skip_cache_setting
>  [notice] Update started: views_post_update_responsive_image_lazy_load
>  [notice] Update completed: views_post_update_responsive_image_lazy_load
>  [notice] Update started: views_post_update_taxonomy_filter_user_context
>  [notice] Update completed: views_post_update_taxonomy_filter_user_context
>  [notice] Update started: views_post_update_timestamp_formatter
>  [notice] Update completed: views_post_update_timestamp_formatter
 [success] Finished performing updates.
```



::: tip See also
- [Drupal 9 to Drupal 10 Upgrades: Complete Technical Guide and Upgrade Steps - Jan 2023](https://www.easternstandard.com/blog/drupal-9-to-drupal-10-upgrades-complete-technical-guide-and-upgrade-steps/)
- [Drupal 9 to 10 Upgrade by Andrey Rudenko - October 2023](https://www.adcisolutions.com/knowledge/drupal-9-to-10-upgrade)
- [Migration from Drupal 7 Simplified as Acquia’s Innovative Tool Goes FOSS - Sep 2023. Drupal 7 to 10 Acquia tool](https://www.thedroptimes.com/34727/migration-drupal-7-simplified-acquias-innovative-tool-goes-foss)
:::

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


## Solving problems with drush updb
When `drush updb` reports errors like those shown below.  Try `drush cr` and repeat first. 

```sh
 [error]   (Currently using Missing or invalid modules The following modules are marked as installed in the core.extension configuration, but they are missing:
 * tracer
 * webprofiler

Review the  suggestions for resolving this incompatibility [1] to repair your installation, and then re-run update.php.

[1] https://www.drupal.org/docs/updating-drupal/troubleshooting-database-updates

```

If you have access to Drush, the quickest solution is to run `drush cedit core.extension` - you can then delete the line containing the unwanted module.  

::: tip Note
Run `drush cr` first to try to get things sort of sane.
This opens the config in vim so you can use `/tracer` to search for tracer, `dd` to delete a line, `:wq` to save
Also if this fails, just try it again.  Sometimes, it fails with a message like:
```
  The command "${VISUAL-${EDITOR-vi}} /tmp/drush_tmp_1711122194_65fda712e42d6/core.extension.yml" failed.
  Exit Code: 1(General error)
  Working directory: /Users/selwyn/Sites/ddev101/web

  Output:
  ================
  Error Output:
  ================
```
:::


[Troubleshooting Database Updates on Drupal.org](https://www.drupal.org/docs/updating-drupal/troubleshooting-database-updates)




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


## Add a module that isn't currently supported in your version of drupal

Use these steps when upgrading from Drupal 9 to Drupal 10:

Install the composer lenient plugin
`composer require mglaman/composer-drupal-lenient`



This makes `composer.json` look like this:


```
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

```
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
- [HOW TO INCORPORATE DRUPAL 9-COMPATIBLE MODULES INTO YOUR DRUPAL 10 PROJECT - Aug 2023](https://www.specbee.com/blogs/how-incorporate-drupal-9-compatible-modules-your-drupal-10-project)
- [https://github.com/mglaman/composer-drupal-lenient](https://github.com/mglaman/composer-drupal-lenient)
- [Using Drupal's Lenient Composer Endpoint - Sep 2023](https://www.drupal.org/docs/develop/using-composer/using-drupals-lenient-composer-endpoint)
- [Install a Contributed Module with No Drupal 9 Release - Feb 2023](https://drupalize.me/tutorial/install-contributed-module-no-drupal-9-release)




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

- \>=1.0

- \>=1.0 \<2.0

- \>=1.0 \<1.1 \|\| \>=1.2

More at <https://getcomposer.org/doc/articles/versions.md>

## Allowing multiple versions

You can use double pipe (`||`) to specify multiple version. 

For the [CSV serialization](https://www.drupal.org/project/csv_serialization) module the author recommends using the following to install the module:
```
composer require drupal/csv_serialization:^2.0 || ^3.0
```

They say: \"It is not possible to support both Drupal 9.x and 10.x in a single release of this module due to a breaking change in EncoderInterface::encode() between Symfony 4.4 (D9) and Symfony 6.2 (D10). When preparing for an upgrade to Drupal 10 we recommend that you widen your Composer version constraints to allow either 2.x or 3.x: `composer require drupal/csv_serialization:^2.0 || ^3.0.` This will allow the module to be automatically upgraded when you upgrade Drupal core.\"

## Reference
- [Drupalize.me: Upgrade to Drupal 10 - March 2023](https://drupalize.me/tutorial/upgrade-drupal-10)
- [Drupal 9 to Drupal 10 Upgrades: Complete Technical Guide and Upgrade Steps - Jan 2023](https://www.easternstandard.com/blog/drupal-9-to-drupal-10-upgrades-complete-technical-guide-and-upgrade-steps/)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Composer documentation article on versions and constraints](https://getcomposer.org/doc/articles/versions.md)
- [Using Drupal's Composer Scaffold updated Dec 2022](https://www.drupal.org/docs/develop/using-composer/using-drupals-composer-scaffold#toc_6)
- [Drupal 9 and Composer Patches by Adrian Vazquez Peligero June 2021](https://vazcell.com/blog/how-apply-patch-drupal-9-composer)
- [Managing patches with Composer March 2022](https://acquia.my.site.com/s/article/360048081193-Managing-patches-with-Composer)
- [Palantir's drupal-rector repo](https://github.com/palantirnet/drupal-rector)
- [Palantir.net: Adding Drupal Rector to a site](https://www.palantir.net/rector/adding-drupal-rector-site)
- [Drupal rector module](https://www.drupal.org/project/rector)

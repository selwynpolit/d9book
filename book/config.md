---
title: Config
---

# Configuration and Settings

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=config.md)

## Overview
Config is stored in yml files so it can be checked into git. It is loaded into the config table of the database for performance. Use `drush config-import` (or `drush cim`) for this purpose. Config includes database table definitions, views definitions and lots more. You can even use config to store a little setting indicating your site is in a `test` mode which can trigger displaying some useful information that only you can see.

Config files should be stored in a non-web accessible directory and specified in `settings.php` or `settings.local.php` like:

```php
$settings['config_sync_directory'] = '../config/sync';
```
You can override config items in a `settings.php` or `settings.local.php` using the `$config` global variable.

[More on configuration Management on Drupal.org.](https://www.drupal.org/docs/configuration-management)


## Reading config values in code

This example shows how to load a rest endpoint from config. This is very similar to Drupal 7 `variable_get()`.

Use the Configuration API main entry point `\Drupal::config()` to load the config item and then use `get()` to retrieve the value you want. Config can have multiple values in a single yml file.

```php
$pizzaEndpoint = \Drupal::config('pizza_academy_core.pbx.rest.endpoint');
$pizza_service_url = $pizzaEndpoint->get('pizza_rest_endpoint').$reg_id;
```

When you export the config, this information is stored in a file called `pizza_academy_core.pbx.rest.endpoint.yml` with a key `pizza_rest_endpoint`.

The contents of the file are simply:

```
pizza_rest_endpoint: 'https://pbx.pizza.com/pbx-profile-service/'
```

You'll find this in a file in the config sync directory specified in
`settings.php` e.g.

```
config/sync/pizza_academy_core.pbx.rest.endpoint.yml
```

The config sync directory location is specified in `settings.php` or `settings.local.php` like this

```php
$settings['config_sync_directory'] = '../config/sync';
```

[More on creating custom modules: Using your own configuration](https://www.drupal.org/docs/creating-custom-modules/defining-and-using-your-own-configuration-in-drupal)

For testing, you can override config items in a `settings.php` or `settings.local.php` using the `$config` global variable.


## Writing config values in code

Here are some examples of writing config values in code. This is similar to Drupal 7 `variable_set()`.

```php
$config = \Drupal::configFactory()->getEditable('pizza_academy_core.pbx.rest.endpoint');
$config->set('pizza_rest_endpoint', $pizza_service_url);
$config->save();
```

```php
// Disable auto refresh if it is enabled.
$auto_refresh_enable = \Drupal::config('tea_teks_voting.settings')
  ->get('conditional_refresh_refresh_enable');
if ($auto_refresh_enable) {
  \Drupal::configFactory()->getEditable('tea_teks_voting.settings')
    ->set('conditional_refresh_refresh_enable', 0)
    ->save();
}
```


## Add config to an existing module

Usually you create a yml file in the module's `/config/install` directory. See [more about config directories below](#config-directories-install-optional-schema).

The config file should start with the module name then a period and the thing you want to store the config about. So `modulename.something.yml` e.g.`dir_salesforce.cron.yml` for cron information, `dir.funnelback.yml` for funnelback information or `tea_teks_spr.testing.yml` for testing information.

If the module name is pizza_academy_core and the thing you want to store config about is the pbxpath, first create a file called: `pizza_academy_core.pbxpath.yml`.

The yml filename is passed as a parameter into calls like `\Drupal::config('...')` without the `.yml` extension. e.g. if the filename is `danamod.header_footer_settings.yml` then use:

```php
$config = \Drupal::config('danamod.header_footer_settings');
```

Here we add some configuration to a module called pizza_academy_core:

In docroot/modules/custom/pizza_academy_core/config/install/pizza_academy_core.pbxpath.yml

We have a file with the contents:

```yml
url: 'https://pbx.pizza.com/'
langcode: 'en'
```

To deploy your config, you can: 
1. Copy it to the `config/sync` directory
2. Paste the contents into the config Drupal u/i or
3. Import it into the db with drush. 

The drush way is the easiest in my opinion.

```
drush config-import --source=modules/custom/pizza_academy_core/config/install/ --partial -y
```

Then you can access it from a controller at `docroot/modules/custom/pizza_academy_core/src/Controller/VerifyCertificationPage.php` using the following code:

```php
$pbx_path_config = \Drupal::config('pizza_academy_core.pbxpath');
$pbx_path = $pbx_path_config->get('url');
$pbx_achievements_url = $pbx_path . "achievements?regid=".$reg_id;
```

Once you grab the url, you can use it later in your code.

:::tip Note
You can add config into any of the 3 directories: `config/install`, `config/optional` or `config/schema`. Config that is added to the `config/install` directory has a special superpower: If config in that directory fails to import into Drupal, the module **is NOT installed**.
:::


## Config directories: install, optional, schema

You can add config to any of the 3 directories for a custom module: `config/install`, `config/optional` or `config/schema`
These can contain configs, like a view or any other config.

- `schema`: This folder is used for schema related config. This is most often used to tell Drupal how custom configurations and configuration entities will be saved.
- `install`: All configurations will be installed. If any configuration fails, **the module won't be installed**.
- `optional`: All configurations will be installed if possible. If a configuration has missing dependencies, it won't be installed but the module **will** be installed.


See also [Include default configuration in your Drupal 8 module - Updated Jan 2024](https://www.drupal.org/docs/develop/creating-modules/include-default-configuration-in-your-drupal-module)
[More at Stack Exchange](https://drupal.stackexchange.com/questions/197897/what-is-the-difference-between-the-config-and-the-settings-directories#197903)



## Add a config form to a custom module

To create a config form, see the [config forms in the forms chapter](forms#config-forms).



## Import config changes in your module via drush

During module development, you might find you want to add some configuration. This is very useful as part of that workflow as you can repeat it as you continue making changes.

```
drush @dev2 config-import --source=modules/migrate/test1/config/install/ --partial -y
```

Note. the @dev2 is a site alias. See [Drush alias docs for more info](https://www.drush.org/latest/site-aliases/). These are sooo useful.


## Add config for another module to your custom module

You can add config for another module to your custom module. This is useful if you want to add some config to a module that you don't want to modify directly. For example, to add a contact form called \"Contact Us\" to the contact module, you can add the following yml file. In the module `config_play`  

```php 

In `modules/custom/config_play/config/install/contact.form.contactus.yml` this would define a new contact form called "Contact Us" with the email address `webmaster@example.com` as the recipient.  Enable the module to get this new contact form to show up in the UI at `/admin/structure/contact`:

```yml
langcode: en
status: true
dependencies: { }
id: contactus
label: 'Contact Us'
recipients:
  - webmaster@example.com
reply: ''
weight: 0

```


## Modify config in a post_update hook

To modify a config item in a post_update hook, you can use the following code. This example changes the site name to "My New Site Name".  The filename would be `mymodule.post_update.php` in the `mymodule` module directory.

```php
/**
 * Update the site name.
 */
function mymodule_post_update_change_site_name() {
  $config = \Drupal::configFactory()->getEditable('system.site');
  $config->set('name', 'My New Site Name');
  $config->save();
```

This example updates the contact us form and sets a reply message. The filename would be `config_play.post_update.php` in the `config_play` module directory:

```php
/**
 * Update the contact us form.
 */
function config_play_post_update_change_contactus_reply() {
  $config = \Drupal::configFactory()->getEditable('contact.form.contactus');
  $config->set('reply', 'Thanks for contacting us. We will get back to you soon.');
  $config->save();
```

:::tip Note
The function name must start with the module name and end with `_post_update` followed by a description of what the update does.  Although any unique name will work for the description.
:::

## Config Read Only

Many sites can benefit from the use of the [Config Read Only module.](https://www.drupal.org/project/config_readonly) This module allows you to set some config items to be read only. This is useful for things like the site name, email address, etc. which should not be changed by the user.  It is also useful for things like the site uuid which should not be changed on a production site.

To set a site in read-only mode, add the following to your `settings.php` or `settings.local.php` file:

```php
$settings['config_readonly'] = TRUE;
```

When you try to change a config item, you will see a message indicating that: `This form will not be saved because the configuration active store is read-only.`

![Config read only message](/images/config_readonly.png)

You can permit changes to specific forms (and their config) by adding something like the following to your `settings.php` or `settings.local.php` file (more below):
```php
$settings['config_readonly_whitelist_patterns'] = [
  'system.menu.main*',
  'system.menu.utility*',
  'system.menu.footer*',
  'system.menu.learn-more*',
  'system.menu.for-publishers*',
  'system.file',
  'webform.webform.*',
  ...
];
```
Once you clear caches and have configured the whitelist, you will no longer see the message and you can change the config items you specified above.

To lock production and not other environments, your code in settings.php might be a conditional on an environment variable like (for [Acquia](https://www.acquia.com) hosted sites):

```php
if (isset($_ENV['AH_SITE_ENVIRONMENT']) && $_ENV['AH_SITE_ENVIRONMENT'] === 'prod') {
  $settings['config_readonly'] = TRUE;
}```

The following approaches are somewhat discouraged since they may allow anyone with Drush or shell access to bypass or disable the protection and change configuration in production.

To allow all changes via the command line and enable readonly mode for the UI only:

```php
if (PHP_SAPI !== 'cli') {
  $settings['config_readonly'] = TRUE;
}
```
You could similarly toggle read-only mode based on the presence or absence of a file on the webserver (e.g. in a location outside the docroot).

```php
if (!file_exists('/home/myuser/disable-readonly.txt')) {
  $settings['config_readonly'] = TRUE;
}
```
### Whitelist settings for custom modules

In the following example, we use this code to load config for our custom module:

```php
$refresh_enabled = \Drupal::config('tea_teks_voting.settings')->get('conditional_refresh_refresh_enable');
```
This means the settings.php needs to have the following to allow site admins to change the value of `conditional_refresh_refresh_enable` in the UI:

```php
$settings['config_readonly_whitelist_patterns'] = [
  'tea_teks_voting.*',
];
```

[See the following documentation for more information on whitelisting.](https://git.drupalcode.org/project/config_readonly/-/blob/HEAD/README.md#configuration)


## Config Storage in the database

Config is kept in the config table of the database.

The `name` field stores the config id e.g. `views.view.infofeeds` (the definition of a view called `infofeeds`)

The `data` field (type LONGBLOB) stores the stuff in the config serialized into a blob.

![Config table in the database](/images/view-in-db.png)



## Add some config to site config form

Here we modify the site config form and add a phone number. This code is in a `.module` file.

```php
use Drupal\Core\Form\FormStateInterface;
/**
 * Implements hook_form_FORM_ID_alter().
 */
function mymodule_form_system_site_information_settings_alter(&$form, FormStateInterface $form_state) {

  $form['site_phone'] = [
    '#type' => 'tel',
    '#title' => t('Site phone'),
    '#default_value' =>
      Drupal::config('system.site')->get('phone'),
  ];

  $form['#submit'][] = 'mymodule_system_site_information_phone_submit';
}
```

The `$form['#submit']` modification adds our callback to the form\'s
submit handlers. This allows our module to interact with the form once
it has been submitted. The `mymodule_system_site_information_phone_submit`
callback is passed the form array and form state. We load the current
configuration factory to receive the configuration that can be edited.
We then `load system.site` and save phone based on the value from the form
state.

```php
function mymodule_system_site_information_phone_submit(array &$form,  FormStateInterface $form_state) {
  $config = Drupal::configFactory()->getEditable('system.site');
  $config->set('phone', $form_state->getValue('site_phone'))
    ->save();
}
```

Don't forget there is a [module called config pages](https://www.drupal.org/project/config_pages) which might save you some coding if you need to add some config to a site.

## Override config in settings.php

This can be useful for local development environment (where you might
put these changes into settings.local.php) or on each one of your servers where you might
need some configuration to be slightly different. e.g. dev/test/prod.

Drupal allows global `$config` overrides (similar to drupal 7) The configuration system integrates these override values via the `Drupal\Core\Config\ConfigFactory::get()` implementation. When you retrieve a value from configuration, the global \$config variable gets a chance to change the returned value:

```php
// Get system site maintenance message text. This value may be overriden by
// default from global $config (as well as translations).
$message = \Drupal::config('system.maintenance')->get('message');
```

To override configuration values in global `$config` in settings.php, use a line like this (which references the configuration keys:

```php
$config['system.maintenance']['message'] = 'Sorry, our site is down now.';
```

For nested values, use nested array keys

```php
$config['system.performance']['css']['preprocess'] = 0;
```

If you have a configuration change, for example, you have enabled google tag manager. When you export the config `drush cex -y` and `git diff` to see what changed in config, you'll see (in the last 2 lines) that status is changed from true to false.

```diff
$ git diff

diff --git a/config/sync/google_tag.container.default.yml b/config/sync/google_tag.container.default.yml
index 39e498c99..375bfb8af 100644
--- a/config/sync/google_tag.container.default.yml
+++ b/config/sync/google_tag.container.default.yml
@@ -1,6 +1,6 @@
 uuid: 5919bbb9-95e3-4d8b-88c8-030e6a58ec6c
 langcode: en
-status: false
+status: true
```

To put this in `settings.php` or `settings.local.php`, add a line and set the value to true or false:

```php
$config['google_tag.container.default']['status'] = false;
```

## Using a testing variable in config for a project

First create the yml file in your `module/config/install` e.g. `tea_teks_srp.testing.yml` with this as the  contents:

```yml
test_mode: FALSE
```

This will be the default state of the app

In the Drupal U/I under config, devel, configuration synchronization, import, single item i.e. at `/admin/config/development/configuration/single/import` select `simple configuration`. In the configuration name field, put `tea_teks_srp.testing`.

Paste in the text of the file

```yml
test_mode: FALSE
```

and import. This will load the new value into the database.

Then in your `docroot/sites/default/settings.local.php` (to enable testing features) add

```php
$config['tea_teks_srp.testing']['test_mode'] = TRUE;
```

This will override your config you added above so test_mode is true.

Then to use the test_mode, you can load it into a controller class (or form class) from the config (and the value in the `settings.local.php` will override the default) with the following:

```php
$test_mode = \Drupal::config('tea_teks_srp.testing')->get('test_mode');
```

And then just use the `$test_mode` variable as needed e.g.

```php
if ($this->test_mode) {
  $value = $this->t("Reject Citation $citation_nid");
}
```

## Getting and setting configuration with drush

Here we are fiddling with the shield module settings

In config, synchronize, we see an item: `shield.settings`

So we can load it with drush:

```yml
$ drush cget shield.settings
credential_provider: shield
credentials:
  shield:
    user: nistor
    pass: blahblah
print: 'Please provide credentials for access.'
allow_cli: true
_core:
  default_config_hash: c1dcnGFTXFeMq2-Z8e7H6Qxp6TTJe-ZhSA126E3bQJ4
```

Drilling down deeper, let's say we want to view the credentials section. Notice that drush requires a space instead of a colon:

```yml
$ drush cget shield.settings credentials
'shield.settings:credentials':
  shield:
    user: nistor
    pass: blahblah
```

Now to get down to the user name and password. And we are adding period back in. Huh?

```sh
$ drush cget shield.settings credentials.shield
'shield.settings:credentials.shield':
    user: nistor
    pass: blahblah
```

and finally:

```sh
$ drush cget shield.settings credentials.shield.pass
'shield.settings:credentials.shield.pass': blahblah
```

So if you want to **set** these:

```sh
drush cset shield.settings credentials.shield.pass yomama

Do you want to update credentials.shield.pass key in shield.settings
config? (y/n): y
```

And

```sh
drush cset shield.settings credentials.shield.user fred

Do you want to update credentials.shield.pass key in shield.settings
config? (y/n): y
```

And there is that message

```sh
drush cget shield.settings print
'shield.settings:print': 'Please provide credentials for access.'
```

And so

```sh
drush cset -y shield.settings print "Credentials or I won't let you in"
```

and while we're here, we could always put these into the `$config` object
via `settings.php` (or `settings.local.php` :

```php
$config['shield.settings']['credentials']['shield']['user'] = "nistor";
$config['shield.settings']['credentials']['shield']['pass'] = "blahblah";
```

Similarly, for setting stage_file_proxy origin:

```sh
drush config-set stage_file_proxy.settings origin https://www.mudslinger.com
```


## Config Pages module

[Config Pages](https://www.drupal.org/project/config_pages) is a really useful module which allows you to quickly create some `config` along with forms to control them.

Here is a screenshot of 4 `bool` fields defined in the Config Pages user interface.
![Fields defined for a config in Config Pages](/images/config_pages_fields.png)

Here is the data entry screen:
![Config pages data entry screen](/images/config_pages_entry_screen.png)

Here is the code to load the values from the config.

```php
use Drupal\config_pages\Entity\ConfigPages;

  /**
   * Load custom settings from the ConfigPages custom_settings entity.
   */
  protected function loadCustomSettings(): void {
    $custom_settings_entity = ConfigPages::config('custom_settings');
    $this->logBatchVoteTime = $custom_settings_entity->get('field_log_batch_vote_time')->value;
    $this->logSingleVoteTime = $custom_settings_entity->get('field_log_single_vote_time')->value;
    $this->displaySingleVoteTime = $custom_settings_entity->get('field_display_single_vote_time')->value;
    $this->displayBatchVoteTime = $custom_settings_entity->get('field_display_batch_vote_time')->value;
    if ($this->logBatchVoteTime ||
      $this->logSingleVoteTime ||
      $this->displaySingleVoteTime ||
      $this->displayBatchVoteTime) {
      $this->shouldComputeElapsedTime = TRUE;
    }
  }
```

{:.note }
From their [docs page](https://www.drupal.org/docs/contributed-modules/config-pages/usage-of-configpages)
There are other three ways to load the configuration values:

```php
// This example uses side-loading for simplicity.
$config_pages = \Drupal::service('config_pages.loader');
$entity = $config_pages->load($config_page_machine_name, $optional_context);

// This example uses call to a static method load:
$entity = ConfigPages::config($config_page_machine_name);

// This example uses storage manager to get a config page via storage manager:
$storage = \Drupal::entityTypeManager()->getStorage('config_pages');
$entity = $storage->load($config_page_machine_name);
```

Each of these methods, will return a loaded entity with a given active context.

## Drush config commands

Drush will provide you with all the tools you need to fiddle with config from the command line. Check out the [drush docs](https://www.drush.org/latest/commands/all/)

### Viewing config

::: tip Note
If you override config values in your settings.php, when you view them with drush cget, drush will **ignore values** overidden in settings.php. This can be confusing. More below.
:::


- `drush config:get system.site` - displays the system.site config.

- `drush config:get system.site page.front` - displays what Drupal is using for the front page of the site: e.g.

```sh
$ drush config:get system.site page.front
  'system.site:page.front': /node
```

::: tip Note
`cget` is short for `config:get`.
:::

### Viewing overridden config values

When you view the value in config, drush `confusingly` will **ignore values** overidden in settings.php.

```sh
drush cget narcs_infoconnect.imagepath basepath
```

This displays the basepath that is in the Drupal database. If you override the basepath in settings.php, you have to use the special flag `--include-overridden` to see the overridden value.

```sh
drush cget narcs_infoconnect.imagepath basepath --include-overridden
```

Also drush can execute php for a little more fun approach:

```sh
drush ev "var_dump(\Drupal::configFactory()->getEditable('system.site')->get('name'))"
```

or

```sh
drush ev "print \Drupal::config('narcs_inferconnect.imagepath')->get('basepath');"
```

### Delete from config

cdel is short for config:delete.

- `drush @dev2 cdel migrate_plus.migration.test1`

- `drush @dev2 cdel migrate_plus.migration_group.default`


### Check what has changed with config:status

`cst` is short for `config:status`

```sh
 drush cst
 --------------------------------------------- ------------
  Name                                          State
 --------------------------------------------- ------------
  admin_toolbar.settings                        Only in DB
  admin_toolbar_tools.settings                  Only in DB
  automated_cron.settings                       Only in DB
  block.block.bartik_account_menu               Only in DB
  block.block.bartik_branding                   Only in DB
  block.block.bartik_breadcrumbs                Only in DB
  block.block.bartik_content                    Only in DB
  ...
```

`Only in DB` means the config has not yet been exported. Best practice is to check the config info git for loading onto the production site. Usually you would use `drush cex` at this point to export the config and add it to git.

After exporting drush will report that everything has been exported and that there are no differences between the database and the sync folder.

```sh
drush cst
 [notice] No differences between DB and sync directory.
```

### Export entire config

`cex` is short for `config:export` This will dump the entire config, a bunch of yml files into the config sync folder which is specified in `settings.php` (or `settings.local.php`) as

```php
$settings['config_sync_directory'] = '../config/sync';
```

- `drush cex -y` - export entire config.

```sh
$ drush cex -y
 [success] Configuration successfully exported to ../config/sync.
../config/sync
```

### Import config changes

If you change the site name (for example) by mistake and want to restore it , you can re-import the values from the last export.

First check what changed with `drush cst` then use `drush cim -y` to restore the config to it's previous glory. `cim` is short for `config:import`.

Drupal cleverly notices which config items have changed and loads only those changes into the database.

```sh
$ drush cst
 ------------- -----------
  Name          State
 ------------- -----------
  system.site   Different
```

and

```sh
$ drush cim -y
+------------+-------------+-----------+
| Collection | Config      | Operation |
+------------+-------------+-----------+
|            | system.site | Update    |
+------------+-------------+-----------+

 // Import the listed configuration changes?: yes.

 [notice] Synchronized configuration: update system.site.
 [notice] Finalizing configuration synchronization.
 [success] The configuration was imported successfully.
```

## Views config.yml files

For views, the config filenames are be in the form `views.view.infofeeds` for a view called `infofeeds`.


## Troubleshooting

### Config export

Sometimes when you try to export config, it seems to randomly decide to delete a lot of config items although `drush config cst` shows just a few items have changed. In this circumstance, `drush config cim -y` will try to import those few items.

I've found that the problem is related to this setting in the `settings.local.php` (or `settings.php`)

```php
$settings['config_exclude_modules'] = ['devel', 'stage_file_proxy', 'masquerade'];
```

For some reason, an edge condition is reached which confuses the configuration engine in Drupal. Commenting out the above line resolves the issue.

I hope this one saves you countless hours of frustration. I know it has caused me plenty of frustration!


## Resources

- [Configuration Management on drupal.org - updated May 2023](https://www.drupal.org/docs/configuration-management)
- [Defining and using your own configuration in Drupal on drupal.org - updated Feb 2024](https://www.drupal.org/docs/creating-custom-modules/defining-and-using-your-own-configuration-in-drupal)
- [Drupal::config - Config API Reference](https://api.drupal.org/api/drupal/core%21lib%21Drupal.php/function/Drupal%3A%3Aconfig/9.2.x)

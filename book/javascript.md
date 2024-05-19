---
title: Javascript
---

# Using Javascript in Drupal
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=javascript.md)

## Closures

Since Drupal's implementation of jQuery uses [jQuery.noConflict()](https://api.jquery.com/jquery.noconflict/), it is also considered good practice to wrap your custom Drupal javascript inside of a closure like this:

```js
(function ($, Drupal) {
  Drupal.behaviors.myModuleBehavior = {
    ...
  };
})(jQuery, Drupal);
```
More info [here](https://github.com/WidgetsBurritos/d8-studyguide/blob/master/1-fundamentals/1.2-javascript-jquery.md#closures)
E.g.:

```js
(function ($, Drupal) {
  Drupal.behaviors.my_custom_behavior = {
    attach: function (context, settings) {
      // Code to be run on page load or refresh
      console.log('Hello, World!');
    }
  };
})(jQuery, Drupal);
```

## Add some global JS to the theme

In `booktheme.info.yml` you need to specify the key to your theme library.  Specifically the `booktheme/global` library.  This refers to a key `global` in the booktheme.libraries.yml file.  Here is the `booktheme.info.yml` file:

```yaml
name: Book Theme
type: theme
base theme: classy
description: A flexible theme with a responsive, mobile-first layout.
package: d9book
core_version_requirement: ^10 || ^11
libraries:
  - booktheme/global
regions:
  header: 'Header'
  primary_menu: 'Primary menu'
  secondary_menu: 'Secondary menu'
  page_top: 'Page top'
  page_bottom: 'Page bottom'
  featured: 'Featured'
  breadcrumb: 'Breadcrumb'
  content: 'Content'
  sidebar_first: 'Sidebar first'
  sidebar_second: 'Sidebar second'
  footer: 'Footer'
```

Then in the `booktheme.libraries.yml` file you need a key `global` and under that, refer to the file: `booktheme.js` in the `js` folder of the theme.  Here is the `booktheme.libraries.yml` file:

```yaml
# Main theme library.
global:
  js:
    js/booktheme.js: {}
  css:
    base:
      css/base/elements.css: {}
    component:
      css/components/block.css: {}
      css/components/breadcrumb.css: {}
      css/components/field.css: {}
      css/components/form.css: {}
      css/components/header.css: {}
      css/components/menu.css: {}
      css/components/messages.css: {}
      css/components/node.css: {}
      css/components/sidebar.css: {}
      css/components/table.css: {}
      css/components/tabs.css: {}
      css/components/buttons.css: {}
    layout:
      css/layouts/layout.css: {}
    theme:
      css/theme/print.css: { media: print }
```

Then in the `booktheme.js` file you can add your javascript.  Here is the `booktheme.js` file:

```js
/**
 * @file
 * Book Theme behaviors.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Behavior description.
   */
  Drupal.behaviors.booktheme = {
    attach: function (context, settings) {

      console.log('It works!');

    }
  };

} (jQuery, Drupal));

```

You can add any key to your main libraries.yml file and then add it to the theme.info.yml file.  The name is up to you.  Notice here how I added a key `global-stuff` to the `selwyn.libraries.yml` file below. I also added a dependency to jQuery as jQuery is no longer loaded automatically on every page in Drupal:

```yaml
global-stuff:
  js:
    js/selwyn.js: {}
  dependencies:
    - core/jquery

base:
  version: VERSION
  css:
    component:
      css/components/action-links.css:
        weight: -10
...
progress:
  version: VERSION
  css:
    component:
      css/components/progress.css:
        weight: -10
search-results:
  version: VERSION
  css:
    component:
      css/components/search-results.css: {  }
user:
  version: VERSION
  css:
    component:
      css/components/user.css:
        weight: -10
```
and then in my `selwyn.info.yml` file I added the key `selwyn/global-stuff` to the libraries key:

```yaml
name: selwyn
type: theme
'base theme': stable9
starterkit: true
version: VERSION
libraries:
  - selwyn/base
  - selwyn/global-stuff
  - selwyn/messages
  - core/normalize
libraries-extend:
  user/drupal.user:
    - selwyn/user
  core/drupal.dropbutton:
    - selwyn/dropbutton
  core/drupal.dialog:
    - selwyn/dialog
  file/drupal.file:
    - selwyn/file
  core/drupal.progress:
    - selwyn/progress
core_version_requirement: ^10
generator: 'starterkit_theme:10.1.5'

```

## Add JS to a module 

In your module folder, add your js file.  e.g. here in `web/modules/general/js/jsplay.js`:

```js
(function ($, Drupal) {

  'use strict';

  /**
   * Behavior description.
   */
  Drupal.behaviors.logitworks = {
    attach: function (context, settings) {

      console.log('It really works!');

    }
  };

} (jQuery, Drupal));

```

In your `general.libraries.yml` file, add the key `jsplay` and if you need jQuery, add it as a dependency

```yaml
# Custom module library for general purposes.
jsplay:
  version: 1.x
  js:
    js/jsplay.js: {}
  dependencies:
    - core/jquery
```

Then to get the js to load on any page content, add this to your module file:

```php
/**
 * Implements hook_preprocess_HOOK().
 */
function general_preprocess_page(&$variables) {
  $variables['#attached']['library'][] =  'general/jsplay';
}
```

[More at Adding Assets to a Drupal module via libraries](https://www.drupal.org/docs/develop/creating-modules/adding-assets-css-js-to-a-drupal-module-via-librariesyml)

## Standard JS IIFE (immediately invoked function expression)

```js
(function (Drupal, $) {
  "use strict";
  // Our code here.
  console.log('Yep - more stuff working.')
}) (Drupal, jQuery);
```

## Click to enable a dropdown menu
```js
(function (Drupal, $) {

  "use strict";

  Drupal.behaviors.selwynMenuTweaker = {
    attach: function (context, settings) {
      $(context).find('#df-user-account').on('click', function() {
        flipNav();

      });
      
      function flipNav() {
        $('#df-account-dropdown').toggle(400);
        console.log('selwynclickedme');
      }

  }
};

})(Drupal, jQuery);

```

## Cycle through some elements and add some text or css  to them

```js
(function (Drupal, $) {
    Drupal.behaviors.andtestthis = {
        attach: function (context, settings) {
            $('#noah').append(" and test this") ;
        }
    };
}) (Drupal, jQuery);
```

or using native js `forEach`:

```js
(function (Drupal, $) {
  Drupal.behaviors.logitworks = {
    attach: function (context, settings) {

      const noah_elements = document.querySelectorAll('#noah');
      noah_elements.forEach(element => {
        // Do something with each element
        element.append('test');
      });

      const elements = document.querySelectorAll('.yomama');
      elements.forEach(element => {
        element.style.backgroundColor = 'red';
      });
    }
  };
}) (Drupal, jQuery);
```

## Add a quick function to run when the page is ready

```js
(function($) {
    $(document).ready(function() {
        // Your code here.
        console.log('Yep - more stuff working.');
    });
})(jQuery);
```

Using `Drupal.behaviors`:

```js
(function($) {

  // Define a namespace for your JavaScript code.
  Drupal.behaviors.myModule = {

    // This function is called when the document is ready.
    attach: function(context, settings) {

      // Add a message to the page.
      $('body').append('<p>Hello, world!</p>');
    }
  };

})(jQuery);
```

## Dropdown list

```js
!function (document, Drupal, $) {
  'use strict';

  Drupal.behaviors.dropDownList = {

    attach: function attach(context) {
      $(document).on('click', '.js-header-dropdown__link', function () {
        // Close all popups but this.
        $('.js-header-dropdown__link')
          .not(this)
          .removeClass('is-dropdown')
          .next($('.js-header-dropdown'))
          .removeClass('is-open');

        // Enable popup.
        $(this)
          .toggleClass('is-dropdown')
          .next($('.js-header-dropdown'))
          .toggleClass('is-open');
      });

      // Close popup when clicking outside container.
      $(document).on('click', function (e) {
        if (!$(e.target).closest('.js-header-dropdown__link').length)
          $('.js-header-dropdown__link')
            .removeClass('is-dropdown')
            .next($('.js-header-dropdown'))
            .removeClass('is-open');
      });

      // Close popup when hitting `esc` key.
      $(document).keydown(function(e) {
        if (e.keyCode == 27) {
          $('.js-header-dropdown__link')
            .removeClass('is-dropdown')
            .next($('.js-header-dropdown'))
            .removeClass('is-open');
        }
      });
    }
  };
}(document, Drupal, jQuery);
```

## Asset library overview

These are collections of css and js files

Namespaced: theme_name/library_name

There are 3 ways to use asset libraries:

1.	Info file
2.	Preprocess function
3.	<code v-pre>{{ attach_library('classy/node') }}</code>

Here is an example of an asset library in use:

In `burger.info.yml` for the theme: 

```yml
name: "Hamburger Theme"
description: "interesting description for the theme"
type: theme
core: 9.x
base theme: classy
libraries:
  - burger/global-styling
```

So to add css in that library:

```yaml
global-styling:
  css:
    layout:
      css/custom.css: {}
    theme:
      css/custom.css: {}
```
and make a `css` folder in the theme folder with a file called custom.css:

```css
* {
  color: red;
}
```

Don't forget to flush caches.


## Attaching a library to specific nodes

To only load on nodes (skipping other entities), add the following function to your `.theme` file.

```php
function mytheme_preprocess_node(&$variables) {
  $variables['#attached']['library'][] = 'mytheme/fancy-tables';
}
```




## Add Javascript to a project using asset libraries

In this project at `web/themes/custom/txglobal/txglobal.libraries.yml` we are trying to load some js in the file `js/globe.js` only on specific pages.  The asset library is defined below (see map:) and in the template file, we specify which assets to include.  That causes `globe.js` to be loaded when that template is used.

```yml
global:
  version: VERSION
  css:
    base:
      css/txglobal.css: {}
  js:
    js/txglobal.js: {}
map:
   version: 1.x,
   js:
    js/globe.js: {}
```
In the `node--map.html.twig` file at `web/themes/custom/txglobal/templates/content/node--map.html.twig` we attach the library `txglobal.libraries.yml` from above using:

```twig
{{ attach_library('txglobal/map') }}
```

---

## Resources

- [JavaScript API overview on drupal.org updated Dec 2022](https://www.drupal.org/docs/drupal-apis/javascript-api/javascript-api-overview)
- [Adding assets (CSS, JS) to a Drupal theme via *.libraries.yml on drupal.org updated Apr 2024](https://www.drupal.org/docs/develop/theming-drupal/adding-assets-css-js-to-a-drupal-theme-via-librariesyml)
- [Cache busting javascript using VERSION value in libraries.yml - June 2022](https://chromatichq.com/insights/drupal-libraries-version/)

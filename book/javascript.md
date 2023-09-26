---
layout: default
title: Javascript
permalink: /javascript
last_modified_date: '2023-09-23'
---

# Using Javascript in Drupal
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=javascript.md)

---

## is this legit? TODO

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



## Add a quick function to run when the page is ready

Note the namespace. This is a good practice to avoid conflicts with other javascript on the page.

```js
// Our namespace:
var SelwynTest = SelwynTest || {};

// Change the #mission text to "peace out" when the page is ready
SelwynTest.modTitles = function () {
    $('#mission').append("peace out") ;
};

$(document).ready(SelwynTest.modTitles);

```

Check out `Drupal.behaviors` also.


## Cycle through some elements and do something to them:

```js
// Our namespace:
var SelwynTest = SelwynTest || {};

SelwynTest.modTitles = function () {
$('#mission').append("peace out") ;  /* append div id mission text with “peace out */

    /* loop through all the missions and add test to the end */
    var missions = $('#mission') ;

    missions.each( function () {
        $(this).append(' now');
    })    
};

$(document).ready(SelwynTest.modTitles);
```


## Cycle through blocks and change their titles
```js
// Our namespace:
var SelwynTest = SelwynTest || {};

SelwynTest.modTitles = function () {
    $('#mission').append(" and peace out") ;
    
    var blocks = $('.block') ;

    blocks.each( function () {
        /* add to the end of the block */
        $(this).append(" - text added to the end of a block");
        
        /* change the title of each block */
        var title = $(this).children(".title");
        console.log(title.text());
        title.append(" - click to close");
    
    })
        
};

```


## Asset library overview

These are collections of css and js files

Namespaced: theme_name/library_name

There are 3 ways to use asset libraries:

1.	Info file
2.	Preprocess function
3.	`{{ attach_library(‘classy/node’) }}`

Here is an example of an asset library in use:

```yaml

In `burger.info.yml` for the theme: 

```yaml
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


## Add Javascript to a project using asset libraries

In this project at `web/themes/custom/txglobal/txglobal.libraries.yml` we are trying to load some js in the file `js/globe.js` only on specific pages.  The asset library is defined below (see map:) and in the template file, we specify which assets to include.  That causes `globe.js` to be loaded when that template is used.

```js
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

```yaml
{{ attach_library('txglobal/map') }}
```


---

## Resources



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

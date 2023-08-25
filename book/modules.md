---
layout: default
title: Modules
permalink: /modules
last_modified_date: '2023-08-24'
---

# Modules for Drupal
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=modules.md)

---

## Overview

With over 50,000 modules, Drupal has the ability to do almost anything. This also means it can be a little tough to find what you need. Here is a list of modules that are important, useful and interesting.







## AI modules

### OpenAI / ChatGPT / AI Search Integration
The OpenAI module aims to provide a suite of modules and an API foundation for OpenAI integration in Drupal for generating text content, images, content analysis and more. OpenAI is the company behind artificial generational intelligence products that powers applications like ChatGPT, GPT-3, GitHub CoPilot, and more. Our goal is to find ways of augmenting and adding assistive AI tech leveraging OpenAI API services in Drupal, transforming the way you manage your content and maintenance tasks.

[https://www.drupal.org/project/openai](https://www.drupal.org/project/openai)

### ChatGPT Content Creator
This is a lightweight and simple OpenAI/ChatGPT module which can act as a content generator, act as a content translator, act as a content assistance tool like creating images from text, extracting SEO keywords from content etc. You can configure to choose either of GPT-3, GPT-3.5 or GPT-4.

[https://www.drupal.org/project/chatgpt_plugin](https://www.drupal.org/project/chatgpt_plugin)

### AWS AI Augmentor

The AWS AI Augmentor is a submodule of Augmentor. It provides an implementation of multiple Augmentor plugins to allow Augmentor to interface with AWS AI Services API.

[https://www.drupal.org/project/augmentor_aws](https://www.drupal.org/project/augmentor_aws)


### Augmentor integration
Augmentor is an AI framework for Drupal which allows for the easy integration of disparate AI systems into Drupal. It provides a plugable ecosystem for managing a variety of AI services such as GPT3, ChatGPT, NLP Cloud, Google Cloud Vision and many more. Augmentor integration module allows content augmentation e.g summarize content, generate content tags etc.

[https://www.drupal.org/project/augmentor](https://www.drupal.org/project/augmentor)





## Content Management


### Content Autogrid

This module provides a table for content administrators to quickly review all content of a specific content entity type (e.g. node, taxonomy term, etc) and bundle (content type, vocabulary, etc).

Each configured field will be shown in a column, and the data for that field shown for each row. Additionally, operations links (edit, delete, etc) will be added based on the permissions for the current user.

The idea is to emulate the grid view provided in popular database administration tools, as a quick way to review available data.

[https://www.drupal.org/project/autogrid](https://www.drupal.org/project/autogrid)

### Allow only one

The Allow Only One module was created to prevent duplicate content save, based on a combination of field values. This module provides a new field type that stores configuration and is later used during validation. Important: This only works and has been tested on Node and Taxonomy_Term

[https://www.drupal.org/project/allow_only_one](https://www.drupal.org/project/allow_only_one)





## Essential Utility Modules for Every Site

Well maybe not every site, but certainly for most sites, your life and your content editor's lives will go better with these modules.

### Module filter

The modules list page can become quite big when dealing with a fairly large site or even just a dev site meant for testing new and various modules being considered. What this module aims to accomplish is the ability to quickly find the module you are looking for without having to rely on the browsers search feature which more times than not shows you the module name in the 'Required by' or 'Depends on' sections of the various modules or even some other location on the page like a menu item.

[https://www.drupal.org/project/module_filter](https://www.drupal.org/project/module_filter)



## Forms

### Protected Forms
Successor of Protected Permissions module.  Light-weight, non-intrusive spam protection module that enables rejection of node, comment, webform, user profile, contact form, private message and revision log submissions which contain undesired language characters or preset patterns.

[https://www.drupal.org/project/protected_forms](https://www.drupal.org/project/protected_forms)






## Other Modules


### Entity Queue

The follow-on module from the original [Nodeque](https://www.drupal.org/project/nodequeue) by [Earl Miles/merlinofchaos](https://www.drupal.org/u/merlinofchaos) which allows users to collect nodes in an arbitrarily ordered list. The order in the list can be used for a any purpose, such as: A user’s favorite music albums, a block listing teasers for the five top news stories on a site or a group of favorites from which one is randomly displayed. 

The Entityqueue module allows users to create queues of any entity type. Each queue is implemented as an Entity Reference field, that can hold a single entity type. For instance, you can create a queue of: Nodes, Users, Taxonomy Terms, etc. Entityqueue provides Views integration, by adding an Entityqueue relationship to your view, and adding a sort for Entityqueue position.

[https://www.drupal.org/project/entityqueue](https://www.drupal.org/project/entityqueue)




## Security/Spam Protection

### Protected Forms
Successor of Protected Permissions module.  Light-weight, non-intrusive spam protection module that enables rejection of node, comment, webform, user profile, contact form, private message and revision log submissions which contain undesired language characters or preset patterns.

[https://www.drupal.org/project/protected_forms](https://www.drupal.org/project/protected_forms)



## Workflow

### ECA: Event - Condition - Action

ECA is a powerful, versatile, and user-friendly rules engine for Drupal 9+. The core module is a processor that validates and executes event-condition-action plugins. Integrated with graphical user interfaces like BPMN.iO, Camunda or other possible future modellers, ECA is a robust system for building conditionally triggered action sets.

[https://www.drupal.org/project/eca([]](https://www.drupal.org/project/eca))




## Resources
- [What every Drupal website should have from the start - August 2023](https://roose.digital/en/blog/drupal/what-every-drupal-website-should-have-start)
- [50 Drupal modules every Drupal professional should know about - September 2021](https://robertroose.com/blog/drupal/50-drupal-modules-every-drupal-professional-should-know-about)
- [27 more Drupal modules every Drupal professional should know about - October 2021](https://robertroose.com/blog/drupal/27-more-drupal-modules-every-drupal-professional-should-know-about)
- [5 best modules to implement Drupal Google Maps - April 2023](https://gole.ms/blog/google-maps-drupal)


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

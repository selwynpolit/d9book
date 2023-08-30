---
layout: default
title: Mysteries
permalink: /mysteries
last_modified_date: '2023-08-30'
---

# Mysteries
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}


![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=mysteries.md)

---

## Overview

I often hit snags that seem to defy logic and Google.  I'll put them here for now and maybe someone can weigh in on the solutions.


## settings.local.php causes WSOD

Updated: 8-30-23

In this case, I loaded a site onto my new m2 mac from git, fired up ddev, imported a current production database, confirmed the site was running and logged into it.  Then I added the stock `sites/default/settings.local/php` as well as `sites/development.services.yml`. Anything I do caused this kind of error.  No amount of cache clearing, config importing etc. will make it work/

```
The website encountered an unexpected error. Please try again later.
RuntimeException: Failed to start the session because headers have already been sent by "/var/www/html/docroot/sites/default/settings.local.php" at line 1. in Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage->start() (line 152 of /var/www/html/vendor/symfony/http-foundation/Session/Storage/NativeSessionStorage.php).
Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage->start() (Line: 162)
Drupal\Core\Session\SessionManager->startNow() (Line: 110)
Drupal\Core\Session\SessionManager->start() (Line: 57)
Symfony\Component\HttpFoundation\Session\Session->start() (Line: 54)
Drupal\Core\StackMiddleware\Session->handle(Object, 1, 1) (Line: 48)
Drupal\Core\StackMiddleware\KernelPreHandle->handle(Object, 1, 1) (Line: 106)
Drupal\page_cache\StackMiddleware\PageCache->pass(Object, 1, 1) (Line: 85)
Drupal\page_cache\StackMiddleware\PageCache->handle(Object, 1, 1) (Line: 50)
Drupal\ban\BanMiddleware->handle(Object, 1, 1) (Line: 48)
Drupal\Core\StackMiddleware\ReverseProxyMiddleware->handle(Object, 1, 1) (Line: 51)
Drupal\Core\StackMiddleware\NegotiationMiddleware->handle(Object, 1, 1) (Line: 23)
Stack\StackedHttpKernel->handle(Object, 1, 1) (Line: 718)
Drupal\Core\DrupalKernel->handle(Object) (Line: 19)
```




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


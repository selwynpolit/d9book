---
layout: default
title: Security
permalink: /security
last_modified_date: '2023-07-31'
---

# Security
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=security.md)

---

## Overview

Drupal is a highly secure platform mostly due to the tireless efforts of the [security team](https://www.drupal.org/drupal-security-team).  


## Sanitizing on output to avoid Cross Site Scripting (XSS) attacks

The Twig theme engine now auto escapes everything by default. That means, every string printed from a Twig template (e.g. anything between `{{ }}`) gets automatically sanitized if no filters are used.

[See Filters - Modifying Variables In Twig Templates](https://www.drupal.org/node/2357633) for the Twig filters available in Drupal. Notably, watch out for the "raw" filter, which does not escape output. Only use this when you are certain the data is trusted.

When rendering attributes in Twig, make sure that you wrap them with double or single quotes. For example, `class="{{ class }}"`` is safe, `class={{ class }}` is not safe.

In order to take advantage of Twig’s automatic escaping (and avoid safe markup being escaped) ideally all HTML should be outputted from Twig templates.



## Reviewing Server logs

### Find the most frequent IP addresses

```
awk '{print $1}' access.log| sort | uniq -c | sort -nr| head
```

### Find the most frequent User Agents

```
awk -F\" '{print $6}' access.log | sort | uniq -c | sort -nr | head
```

### Find the most frequent URLs visited on your site

```
awk -F\" '{print $2}' access.log| awk -F? '{print $1}' | sort | uniq -c | sort -nr | head
```

This query strips the variables (anything after a question mark in your URL).

[Read more on Acquia.com](https://acquia.my.site.com/s/article/360013350193-Analyzing-Your-Traffic)

## General overview of harding your Drupal site

[from Acquia.com](https://acquia.my.site.com/s/article/360041130414-Harden-Drupal-sites-against-security-threats)

**Ensure up-to-date backups are safe and secure**
- Initiate a production database backup
- Download a copy of recent database backups, and keep updated copies offsite
- If possible, also take backups of the file system

**Ensure Drupal Core and Installed Modules are up to date** 
Drupal Core updates often contain security patches. Outdated, unmaintained modules often contain known security vulnerabilities. 
- Look for projects and modules covered by the Drupal Security Advisories
- Remove obsolete and unused modules
- Check for available updates under the Drupal admin console, or by using drush or composer. 

**Perform a user audit**
- Ensure permissions are restricted and implemented correctly
- Remove any old or unneeded admin or privileged accounts

If a breach has occurred or internal threat, an attacker or internal threat may have added user(s) to retain access.

- Check for any new or unexpected user accounts

**Password Checks**

Bad passwords are the most common cause of site compromise. 

- Ensure strong password requirements are enforced. A community contributed module that offers this functionality is Password Policy.
- Perform a check for bad passwords. A community contributed module that offers this functionality is [Drop the Ripper](https://www.drupal.org/project/drop_the_ripper)

**2-Factor Authentication**

- Enforce 2-factor authentication (especially for admin and/or privileged accounts) to mitigate the threat of compromised passwords.
- 
**Review Site Functionality**

- Check that file uploads are restricted to intended file extension type (e.g. Do not allow .html uploads for an image)
- Ensure any sensitive data files are uploaded to secure directories only
(e.g. Do not place personal data ( PII ) such as CVs or job applications in public 'files' directories)
- Review controls on web forms

Attackers will often target forms that generate outbound emails ( e.g. "refer a friend" or "contact-us" )

Try to keep messages generated from forms generic
Ensure CAPTCHA controls are used to prevent abuse

**Web Application Firewall ( WAF )**

If a WAF is not already in place, Acquia strongly recommend implementing one.

[Acquia Cloud Edge Protect](https://docs.acquia.com/guide/edge/) is Acquia's WAF offering.

Edge Protect provides advanced security controls to restrict and block attacker traffic before it reaches the application stack. Common attack methods are identified and blocked automatically. WAFs are extremely effective in mitigating (D)DOS attacks.




## Resources
- [Blocking access using rewrites (Acquia.com)](https://acquia.my.site.com/s/article/360005210634-Blocking-access-using-rewrites)
- [.htaccess documentation (Acquia.com)](https://docs.acquia.com/cloud-platform/manage/htaccess/)
- [Ban module in Drupal core overview for blocking IP addresses (Acquia.com)](https://www.drupal.org/docs/8/core/modules/ban/overview)
- [Advanced Ban module](https://www.drupal.org/project/advban)
- [Restricting website access (Acquia.com)](https://docs.acquia.com/cloud-platform/arch/security/restrict/)
- [Writing secure code for Drupal from Drupal.org update August 2022](https://www.drupal.org/docs/8/security/drupal-8-sanitizing-output)
- [Twig Filters - Modifying Variables In Twig Templates](https://www.drupal.org/node/2357633)



 
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

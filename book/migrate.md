---
layout: default
title: Migrate
permalink: /migrate
last_modified_date: '2023-04-28'
---

# Migration
{: .no_toc .fw-500 }

## Table of contents
{: .no_toc .text-delta }

- TOC
{:toc}

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=migrate.md)

---

## Import content from another Migration into Paragraphs

This migration Process snippet example demonstrates how to populate the fields of a Drupal Paragraph. Whereas standard fields can be simply mapped 1:1, and its value attribute (`value`) is implied (derived), a Paragraph requires _both_ a `target_id` and `target_revision_id`. Annotations included inline to demonstrate what is going on within.

```yaml
  # Field name is `field_paragraph_authors`, specific property `target_id`:
  field_paragraph_authors/target_id:
  - plugin: migration_lookup
    # Dependent Migration called `migration_paragraph_linked_author`
    migration: migration_paragraph_linked_author
    # Don't create stub content if the row currently being processes does not map to an item in the earlier-run Migration
    no_stub: true
    # How to map this Migration with the earlier-run Migration
    source: sku
  - plugin: skip_on_empty
    # Method: If empty, skip only this field mapping (`process`), not the entire Row (`row`)
    method: process
  - plugin: extract
    # This destination property is 1st element in the migration-lookup array.
    index:
      - 0
  # Other half of `field_paragraph_authors`, specific property `target_revision_id`:
  field_paragraph_authors/target_revision_id:
  - plugin: migration_lookup
    migration: migration_paragraph_linked_author
    no_stub: true
    source: sku
  - plugin: skip_on_empty
    method: process
  - plugin: extract
    # This destination property is the 2nd element in the migration-lookup array.
    index:
      - 1
```


## Resources

* 31 days of Drupal migrations by Mauricio Dinarte August 2019 <https://understanddrupal.com/migrations>
* Stop waiting for Feeds module: how to import RSS in Drupal 8 by Campbell Vertesi June 2017 <https://ohthehugemanatee.org/blog/2017/06/07/stop-waiting-for-feeds-module-how-to-import-remote-feeds-in-drupal-8> 
* Issue on Drupal.org where Mike Ryan, the author of the migrate module,  addresses how to start a migration programmatically <https://www.drupal.org/project/drupal/issues/2764287>
* Video from Twin Cities Drupal community where Mauricio Dinarte and Benjamin Melançon demonstrate how to implement migrations - June 2017. <https://www.youtube.com/watch?v=eBP2vQIwx-o>
* Migrating to Drupal From Alternate Sources by Joshua Turton showing migrations from CSV, XML/RSS and Wordpress into Drupal 8 - November 2018  <https://www.phase2technology.com/blog/migrating-drupal-alternate-sources>
* Nice intro to Drupal 8 migration by Chris of Redfin Solutions from Nov 2017.  <https://redfinsolutions.com/blog/understanding-drupal-8s-migrate-api>
* Migrating files and images on Drupal.org updated Feb 2023. <https://www.drupal.org/docs/drupal-apis/migrate-api/migrate-destination-plugins-examples/migrating-files-and-images>
* Drupal 8 content migrations from CSV or spreadsheet August 2020 <https://atendesigngroup.com/articles/drupal-8-content-migrations-csv-spreadsheet>









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

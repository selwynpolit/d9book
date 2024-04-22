---
title: Migrate
---

# Migration
![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=migrate.md)

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

* [31 days of Drupal migrations by Mauricio Dinarte - August 2019](https://understanddrupal.com/migrations)
* [Unraveling the ETL Data Migration Process. A 3 part series from Tag1 Consulting - Dec 2023 & Jan 2024](https://www.tag1consulting.com/blog/unraveling-etl-process-extract)
* [Stop waiting for Feeds module: how to import RSS in Drupal 8 by Campbell Vertesi - June 2017](https://ohthehugemanatee.org/blog/2017/06/07/stop-waiting-for-feeds-module-how-to-import-remote-feeds-in-drupal-8)
* [Issue on Drupal.org where Mike Ryan, co-author of the migrate module,  addresses how to start a migration programmatically](https://www.drupal.org/project/drupal/issues/2764287)
* [Youtube Video from Twin Cities Drupal community where Mauricio Dinarte and Benjamin Melançon demonstrate how to implement migrations - June 2017](https://www.youtube.com/watch?v=eBP2vQIwx-o)
* [Migrating to Drupal From Alternate Sources by Joshua Turton showing migrations from CSV, XML/RSS and Wordpress into Drupal 8 - November 2018](https://www.phase2technology.com/blog/migrating-drupal-alternate-sources)
* [Nice introduction to Drupal 8 migration by Chris of Redfin Solutions - Nov 2017](https://redfinsolutions.com/blog/understanding-drupal-8s-migrate-api)
* [Migrating files and images on Drupal.org - updated February 2023](https://www.drupal.org/docs/drupal-apis/migrate-api/migrate-destination-plugins-examples/migrating-files-and-images)
* [Drupal 8 content migrations from CSV or spreadsheet - August 2020](https://atendesigngroup.com/articles/drupal-8-content-migrations-csv-spreadsheet)


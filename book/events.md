---
title: Events
---

# Event System

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=events.md)

## Overview

Events allow different components of the system to interact and communicate with each other. One system component dispatches the event at an appropriate time; many events are dispatched by Drupal core and the Symfony event system in every request. Other system components can register as event subscribers; when an event is dispatched, a method is called on each registered subscriber, allowing each one to react. For more on the general concept of events, see [The EventDispatcher Component in the Symfony docs](https://symfony.com/doc/current/components/event_dispatcher.html)


Take an example from the [HttpKernel component](https://symfony.com/doc/current/components/http_kernel.html). Once a `Response` object has been created, it may be useful to allow other elements in the system to modify it (e.g. add some cache headers) before it's actually used. To make this possible, the Symfony kernel dispatches an event - `kernel.response`. Here's how it works:

* A listener (PHP object) tells a central dispatcher object that it wants to listen to the `kernel.response` event;
* At some point, the Symfony kernel tells the dispatcher object to dispatch the `kernel.response` event, passing with it an `Event` object that has access to the Response object;
* The dispatcher notifies (i.e. calls a method on) all listeners of the `kernel.response` event, allowing each of them to make modifications to the `Response` object.


Event systems are used in many complex applications as a way to allow extensions to modify how the system works. An event system can be implemented in a variety of ways, but generally, the concepts and components that make up the system are the same.

**Event Subscribers** - Sometimes called "Listeners", are callable methods or functions that react to an event being propagated throughout the Event Registry.
**Event Registry** - Where event subscribers are collected and sorted.
**Event Dispatcher** - The mechanism in which an event is triggered, or "dispatched", throughout the system.
**Event Context** - Many events require a specific set of data that is important to the subscribers to an event. This can be as simple as a value passed to the Event Subscriber, or as complex as a specially created class that contains the relevant data.


## Finding Drupal events

There are several ways to find events to subscribe to:

- Search `web/core` for `@Event`

![Results of searching for @Event](/images/search-for-events-in-web-core.png)

- You can also see a listing at the bottom of [this page](https://api.drupal.org/api/drupal/core%21core.api.php/group/events/)

- You can also use the [webprofiler module](https://www.drupal.org/project/webprofiler) to view events and the event subscribers. Enable it and check the checkbox in it's settings for events. When you view a page, you’ll see the toolbar at the bottom of the page.  Click any link and it will give you useful stats. Select `events` on the left. and you will see a long list of events and the event subscribers that are called.  e.g. in the first line below, the dispatched event is “kernel.request” which is listened to by 
Drupal/Core/Routing/RoutePreloader.php::onRequest 

![Web Profiler Events listing](/images/events-listing.png)

## Generate event subscriber with Drush

You can use drush to generate the code for working on this module using `drush generate service:event-subscriber`

It will create the `module.info.yml` file, the `module.services.yml` file and also the `src/EventSubscriber/EventsExampleSubscriber.php` files for you.


## Subscribe to a core event

Here is an example which subscribes to the `Kernel::REQUEST` event. This happens very early in the process. On each request, it checks to see if the site is in maintenance mode, and if it is, logs the event and redirects the user to www.nytimes.com.

::: tip Note
You can use drush to generate the code for working on this module using `drush generate service:event-subscriber`
:::

In `web/modules/custom/mymodule/mymodule.info.yml`

```yml
name: 'Mymodule'
type: module
description: 'Exploring events'
package: 'Custom'
core_version_requirement: ^10
```

In `web/modules/custom/mymodule/mymodule.services.yml`

```yml
services:
  mymodule.route_finished_subscriber:
    class: Drupal\mymodule\EventSubscriber\RouteFinishedSubscriber
    arguments:
      - '@state'
    tags:
      - { name: event_subscriber }
```

And finally in `web/modules/custom/mymodule/src/EventSubscriber/RouteFinishedSubscriber.php`

```php
<?php declare(strict_types = 1);

namespace Drupal\mymodule\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\State\StateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Redirects to a specific URL when the site is in maintenance mode.
 */
final class RouteFinishedSubscriber implements EventSubscriberInterface {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a new RouteFinishedSubscriber.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * Redirect to nytimes.com when the site is in maintenance mode and logs event.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The event to respond to.
   */
  public function onKernelRequest(RequestEvent $event): void {
    if ($this->state->get('system.maintenance_mode')) {
      $response = new TrustedRedirectResponse('http://www.mytimes.com');
      \Drupal::logger('mymodule')->info("System in maint mode - sent them to the times!");
      $event->setResponse($response);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onKernelRequest'],
    ];
  }
}
```

The `getSubscribedEvents()` method from `vendor/symfony/event-dispatcher/EventSubscriberInterface.php` returns an array of event names this subscriber wants to listen to.
The array keys are event names and the value can be:
- The method name to call (priority defaults to 0)
- An array composed of the method name to call and the priority
- An array of arrays composed of the method names to call and respective priorities, or 0 if unset

For instance:
['eventName' => 'methodName']
['eventName' => ['methodName', $priority]]
['eventName' => [['methodName1', $priority], ['methodName2']]]
The code must not depend on runtime state as it will only be called at compile time. All logic depending on runtime state must be put into the individual methods handling the events.
Returns: array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>




## Resources

- [Subscribe to and dispatch events on Drupal.org](https://www.drupal.org/docs/develop/creating-modules/subscribe-to-and-dispatch-events)
- [Overview of event dispatch and subscribing in the Drupal API](https://api.drupal.org/api/drupal/core%21core.api.php/group/events/)


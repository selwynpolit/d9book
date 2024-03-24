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

## Subscribe to custom events

```php
// For each event key define an array of arrays composed of the method names
// to call and optional priorities. The method name here refers to a method
// on this class to call whenever the event is triggered.
$events[IncidentEvents::NEW_REPORT][] = ['notifyMario'];

// Subscribers can optionally set a priority. If more than one subscriber is
// listening to an event when it is triggered they will be executed in order
// of priority. If no priority is set the default is 0.
$events[IncidentEvents::NEW_REPORT][] = ['notifyBatman', -100];

// We'll set an event listener with a very low priority to catch incident
// types not yet defined. In practice, this will be the 'cat' incident.
$events[IncidentEvents::NEW_REPORT][] = ['notifyDefault', -255];
```

See [Examples module's events_example](https://git.drupalcode.org/project/examples/-/blob/4.0.x/modules/events_example/src/EventSubscriber/EventsExampleSubscriber.php?ref_type=heads) for more



## Define custom events

```php
<?php

namespace Drupal\events_example\Event;

/**
 * Defines events for the events_example module.
 *
 * It is best practice to define the unique names for events as constants in a
 * static class. This provides a place for documentation of the events, as well
 * as allowing the event dispatcher to use the constants instead of hard coding
 * a string.
 *
 * In this example we're defining one new event:
 * 'events_example.new_incident_report'. This event will be dispatched by the
 * form controller \Drupal\events_example\Form\EventsExampleForm whenever a new
 * incident is reported. If your application dispatches more than one event
 * you can use a single class to document multiple events -- just add a new
 * constant for each. Group related events together with a single class;
 * define another class for unrelated events.
 *
 * The docblock for each event name should contain a description of when, and
 * under what conditions, the event is triggered. A module developer should be
 * able to read this description in order to determine whether this is
 * the event that they want to subscribe to.
 *
 * Additionally, the docblock for each event should contain an "@Event" tag.
 * This is used to ensure documentation parsing tools can gather and list all
 * events.
 *
 * Example: https://api.drupal.org/api/drupal/core%21core.api.php/group/events/
 *
 * In core \Drupal\Core\Config\ConfigCrudEvent is a good example of defining and
 * documenting new events.
 *
 * @ingroup events_example
 */
final class IncidentEvents {

  /**
   * Name of the event fired when a new incident is reported.
   *
   * This event allows modules to perform an action whenever a new incident is
   * reported via the incident report form. The event listener method receives a
   * \Drupal\events_example\Event\IncidentReportEvent instance.
   *
   * @Event
   *
   * @see \Drupal\events_example\Event\IncidentReportEvent
   *
   * @var string
   */
  const NEW_REPORT = 'events_example.new_incident_report';

}
```

To subscribe to this new custom event, you can refer to `IncidentEvents::NEW_REPORT` in the `getSubscribedEvents` method as shown below.


```php
 /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[IncidentEvents::NEW_REPORT][] = ['notifyManagers'];
    return $events;
  }
```



## Dispatch a custom event

To dispatch a custom event, you instantiate a new event object and call the `event_dispatcher->dispatch()` method.  In this example, the event is dispatched by the user filling out a form so the `submitForm` method is shown.



```php
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $type = $form_state->getValue('incident_type');
    $report = $form_state->getValue('incident');

    // When dispatching or triggering an event, start by constructing a new
    // event object. Then use the event dispatcher service to notify any event
    // subscribers.
    $event = new IncidentReportEvent($type, $report);

    // Dispatch an event by specifying which event, and providing an event
    // object that will be passed along to any subscribers.
    $this->event_dispatcher->dispatch($event, IncidentEvents::NEW_REPORT);
```


Here is the entire file for clarity:

```php
<?php

namespace Drupal\events_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\events_example\Event\IncidentEvents;
use Drupal\events_example\Event\IncidentReportEvent;

/**
 * Implements the EventsExampleForm form controller.
 *
 * The submitForm() method of this class demonstrates using the event dispatcher
 * service to dispatch an event.
 *
 * @see \Drupal\events_example\Event\IncidentEvents
 * @see \Drupal\events_example\Event\IncidentReportEvent
 * @see \Symfony\Component\EventDispatcher\EventDispatcherInterface
 * @see \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
 *
 * @ingroup events_example
 */
class EventsExampleForm extends FormBase {

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $event_dispatcher;

  /**
   * Constructs a new UserLoginForm.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service.
   */
  public function __construct(EventDispatcherInterface $event_dispatcher) {
    // The event dispatcher service is an implementation of
    // \Symfony\Component\EventDispatcher\EventDispatcherInterface. In Drupal
    // this is generally an instance of the
    // \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher service.
    // This dispatcher improves performance when dispatching events by compiling
    // a list of subscribers into the service container so that they do not need
    // to be looked up every time.
    $this->event_dispatcher = $event_dispatcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('event_dispatcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['incident_type'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => t('What type of incident do you want to report?'),
      '#options' => [
        'stolen_princess' => $this->t('Missing princess'),
        'cat' => $this->t('Cat stuck in tree'),
        'joker' => $this->t('Something involving the Joker'),
      ],
    ];

    $form['incident'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#title' => t('Incident report'),
      '#description' => t('Describe the incident in detail. This information will be passed along to all crime fighters.'),
      '#cols' => 60,
      '#rows' => 5,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'events_example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $type = $form_state->getValue('incident_type');
    $report = $form_state->getValue('incident');

    // When dispatching or triggering an event, start by constructing a new
    // event object. Then use the event dispatcher service to notify any event
    // subscribers.
    $event = new IncidentReportEvent($type, $report);

    // Dispatch an event by specifying which event, and providing an event
    // object that will be passed along to any subscribers.
    $this->event_dispatcher->dispatch($event, IncidentEvents::NEW_REPORT);
  }

}
```


## Resources

- [Subscribe to and dispatch events on Drupal.org](https://www.drupal.org/docs/develop/creating-modules/subscribe-to-and-dispatch-events)
- [Overview of event dispatch and subscribing in the Drupal API](https://api.drupal.org/api/drupal/core%21core.api.php/group/events/)


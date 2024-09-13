---
title: Events
---

# Event System

![views](https://api.visitor.plantree.me/visitor-badge/pv?label=views&color=informational&namespace=d9book&key=events.md)

## Overview

Events allow different components of the system to interact and communicate with each other. One system component dispatches the event at an appropriate time; many events are dispatched by Drupal core and the Symfony event system in every request. Other system components can register as event subscribers. When an event is dispatched, a method is called on each registered subscriber, allowing each one to react. For more on the general concept of events, see [The EventDispatcher Component in the Symfony docs](https://symfony.com/doc/current/components/event_dispatcher.html)


Take an example from the [HttpKernel component](https://symfony.com/doc/current/components/http_kernel.html). Once a `Response` object has been created, it may be useful to allow other elements in the system to modify it (e.g. add some cache headers) before it's actually used. To make this possible, the Symfony kernel dispatches an event - `kernel.response`. Here's how it works:

* A listener (PHP object) tells a central dispatcher object that it wants to listen to the `kernel.response` event;
* At some point, the Symfony kernel tells the dispatcher object to dispatch the `kernel.response` event, passing with it an `Event` object that has access to the Response object;
* The dispatcher notifies (i.e. calls a method on) all listeners of the `kernel.response` event, allowing each of them to make modifications to the `Response` object.


Event systems are used in many complex applications as a way to allow extensions to modify how the system works. An event system can be implemented in a variety of ways, but generally, the concepts and components that make up the system are the same.

- **Event Subscribers** - Sometimes called "Listeners", are callable methods or functions that react to an event being propagated throughout the Event Registry.
- **Event Registry** - Where event subscribers are collected and sorted.
- **Event Dispatcher** - The mechanism in which an event is triggered, or "dispatched", throughout the system.
- **Event Context** - Many events require a specific set of data that is important to the subscribers to an event. This can be as simple as a value passed to the Event Subscriber, or as complex as a specially created class that contains the relevant data.


## Finding Drupal events

There are several ways to find events to subscribe to:

- Search `web/core` for `@Event`

![Results of searching for @Event](/images/search-for-events-in-web-core.png)

- Look in `vendor/symfony/http-kernel/KernelEvents.php` (e.g. for `KernelEvents::REQUEST`)and `web/core/lib/Drupal/Core/Config/ConfigEvents.php` (e.g. for `ConfigEvents::SAVE`)

- You can also see a listing at the bottom of [this page](https://api.drupal.org/api/drupal/core%21core.api.php/group/events/)

- You can also use the [webprofiler module](https://www.drupal.org/project/webprofiler) to view events and the event subscribers. Enable it and check the checkbox in it's settings for events. When you view a page, you’ll see the toolbar at the bottom of the page.  Click any link and it will give you useful stats. Select `events` on the left. and you will see a long list of events and the event subscribers that are called.  e.g. in the first line below, the dispatched event is “kernel.request” which is listened to by 
Drupal/Core/Routing/RoutePreloader.php::onRequest 

![Web Profiler Events listing](/images/events-listing.png)


## Generate event subscriber with Drush

You can use drush to generate the code for working on this module using `drush generate service:event-subscriber`

It will create the `module.info.yml` file, the `module.services.yml` file and also the `src/EventSubscriber/EventsExampleSubscriber.php` files for you.


## Subscribe to a core event

Here is an example which subscribes to the `Kernel::REQUEST` event. This happens very early in the process. On each request, it checks to see if the site is in maintenance mode, and if it is, logs the event and redirects the user to `www.nytimes.com`.

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
      $response = new TrustedRedirectResponse('https://www.nytimes.com');
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

For example:
```php
['eventName' => 'methodName']
['eventName' => ['methodName', $priority]]
['eventName' => [['methodName1', $priority], ['methodName2']]]
```

The code must not depend on runtime state as it will only be called at compile time. All logic depending on runtime state must be put into the individual methods handling the events. 
Returns: `array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>`




## Define custom events

1. Define the event constants in a class
2. Define the event class
3. Dispatch the event

When creating a custom event, first create a file `mymodule/src/Event/IncidentEvents.php` to hold the constants that define the event names like this:

```php
<?php

namespace Drupal\mymodule\Event;

final class IncidentEvents {
  /**
   * Name of the event fired when a new report is created.
   *
   * @Event
   *
   * @var string
   */
  const NEW_REPORT = 'mymodule.new_report';
}
```

 In the example above, we define an event called `NEW_REPORT`. We then subscribe to this event in the `getSubscribedEvents` method of the `EventsExampleSubscriber` class.

There is another example in the [Examples module's events_example](https://git.drupalcode.org/project/examples/-/blob/4.0.x/modules/events_example/src/Event/IncidentEvents.php?ref_type=heads) module.


Then to handle the additional contextual data that you want to provide to the event subscribers when dispatching an event, create a new class that extends `\Symfony\Component\EventDispatcher\Event` `mymodule/src/Event/IncidentReportEvent.php`.

```php
<?php

namespace Drupal\events_example\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Wraps a incident report event for event subscribers.
 *
 * Whenever there is additional contextual data that you want to provide to the
 * event subscribers when dispatching an event you should create a new class
 * that extends \Symfony\Component\EventDispatcher\Event.
 *
 * See \Drupal\Core\Config\ConfigCrudEvent for an example of this in core.
 *
 */
class IncidentReportEvent extends Event {

  /**
   * Incident type.
   *
   * @var string
   */
  protected $type;

  /**
   * Detailed incident report.
   *
   * @var string
   */
  protected $report;

  /**
   * Constructs an incident report event object.
   *
   * @param string $type
   *   The incident report type.
   * @param string $report
   *   A detailed description of the incident provided by the reporter.
   */
  public function __construct($type, $report) {
    $this->type = $type;
    $this->report = $report;
  }

  /**
   * Get the incident type.
   *
   * @return string
   *   The type of report.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Get the detailed incident report.
   *
   * @return string
   *   The text of the report.
   */
  public function getReport() {
    return $this->report;
  }

}
```
See the code [in the example module](https://git.drupalcode.org/project/examples/-/blob/4.0.x/modules/events_example/src/Event/IncidentReportEvent.php?ref_type=heads)


To dispatch your event, you can use:
```php
$event = new IncidentReportEvent($type, $report);
$this->event_dispatcher->dispatch($event, IncidentEvents::NEW_REPORT);
```

Or more completely:
```php
<?php
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Drupal\mymodule\Event\NewReportEvent;

class SomeClass {
  protected $eventDispatcher;

  public function __construct(EventDispatcherInterface $event_dispatcher) {
    $this->eventDispatcher = $event_dispatcher;
  }

  public function someMethod() {
    $report = 'some report data';
    $event = new NewReportEvent($report);
    $this->eventDispatcher->dispatch($event, NewReportEvent::EVENT_NAME);
  }
}
```






## Subscribe to custom events

You need an Event Subscriber class which is the class that responds to the custom event (i.e. the class that does the magic when the event fires). You also need a `mymodule.services.yml` which tells Drupal about the Event Subscriber class.

The EventSubscriber class e.g. `mymodule/src/EventSubscriber/EventsExampleSubscriber.php` looks something like this:
  
```php
<?php
namespace Drupal\mymodule\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\mymodule\Event\IncidentEvents;

class YourModuleEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[IncidentEvents::NEW_REPORT][] = ['notifyMario'];
    return $events;
  }

  /**
   * Method to handle the notifyMario event.
   */
  public function notifyMario($event) {
    // You put your code here to handle the event.
  }
}
```

Then you register your event subscriber in `mymodule.services.yml` like this:

```yml
services:
  your_module.event_subscriber:
    class: Drupal\your_module\EventSubscriber\YourModuleEventSubscriber
    tags:
      - { name: event_subscriber }
```

If you want to subscribe to multiple events, you can add more entries to the `$events` array in the `getSubscribedEvents` method. The key is the event name and the value is an array of method names to call when the event is triggered. You can also specify a priority for each method. The method with the highest priority will be called first. If two methods have the same priority, they will be called in the order they were added to the array.

```php
$events[IncidentEvents::NEW_REPORT][] = ['notifyMario'];
$events[IncidentEvents::NEW_REPORT][] = ['notifyBatman', -100];
$events[IncidentEvents::NEW_REPORT][] = ['notifyDefault', -255];
```



## Dispatch a custom event

To dispatch a custom event, instantiate a new event object and call the `event_dispatcher->dispatch()` method.  In the example below, the event is dispatched by the user filling out a form. Here is the `submitForm` method.


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
      '#title' => $this->t('What type of incident do you want to report?'),
      '#options' => [
        'stolen_princess' => $this->t('Missing princess'),
        'cat' => $this->t('Cat stuck in tree'),
        'joker' => $this->t('Something involving the Joker'),
      ],
    ];

    $form['incident'] = [
      '#type' => 'textarea',
      '#required' => FALSE,
      '#title' => $this->t('Incident report'),
      '#description' => $this->t('Describe the incident in detail. This information will be passed along to all crime fighters.'),
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

## Stop propagation and access information about the event

If you have multiple subscribers to an event and you want to skip them after a certain one is called, you can use `$event->stopPropagation`:

```php
public function notifyMario(IncidentReportEvent $event) {
    // You can use the event object to access information about the event passed
    // along by the event dispatcher.
    if ($event->getType() == 'stolen_princess') {
      $this->messenger()->addStatus($this->t('Mario has been alerted. Thank you. This message was set by an event subscriber. See @method()', ['@method' => __METHOD__]));
      // Optionally use the event object to stop propagation.
      // If there are other subscribers that have not been called yet this will
      // cause them to be skipped.
      $event->stopPropagation();
    }
  }
```

## Use custom autocomplete route

The  `RouteSubscriber` class extends `RouteSubscriberBase` and listens to dynamic route events.  It serves to alter existing routes via the `alterRoutes` method. The `RouteCollection` object parameter contains all the routes defined in the application.

In the `web/modules/custom/abc_admin_enhancements/abc_admin_enhancements.services.yml` file there is a service defined for the `RouteSubscriber` class. This service is tagged as an event subscriber, which means that it will be automatically registered with the event dispatcher when the module is installed. The `RouteSubscriber` class listens for the `system.entity_autocomplete` route and alters it to use a custom controller.

```yml
services:
  abc_admin_enhancements.route_subscriber:
    class: Drupal\abc_admin_enhancements\Routing\RouteSubscriber
    tags:
      - { name: event_subscriber }

  abc_admin_enhancements.autocomplete_matcher:
    class: Drupal\abc_admin_enhancements\EntityAutocompleteMatcher
    arguments: ['@plugin.manager.entity_reference_selection']
```


Here is the `web/modules/custom/abc_admin_enhancements/src/Routing/RouteSubscriber.php` file:

```php
<?php

namespace Drupal\abc_admin_enhancements\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {
  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if($route = $collection->get('system.entity_autocomplete')) {
      $route->setDefault('_controller', '\Drupal\abc_admin_enhancements\Controller\EntityAutocompleteController::handleAutocomplete');
    }
  }
}
```

Here is `web/modules/custom/abc_admin_enhancements/src/Controller/EntityAutocompleteController.php`:

```php
<?php

namespace Drupal\abc_admin_enhancements\Controller;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\abc_admin_enhancements\EntityAutocompleteMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityAutocompleteController extends \Drupal\system\Controller\EntityAutocompleteController {

  /**
   * The autocomplete matcher for entity references.
   */
  protected $matcher;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityAutocompleteMatcher $matcher, KeyValueStoreInterface $key_value) {
    $this->matcher = $matcher;
    $this->keyValue = $key_value;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('abc_admin_enhancements.autocomplete_matcher'),
      $container->get('keyvalue')->get('entity_autocomplete')
    );
  }
}
```

Finally, here is the `web/modules/custom/abc_admin_enhancements/src/EntityAutocompleteMatcher.php` file with the `getMatches` method:

```php
<?php

namespace Drupal\abc_admin_enhancements;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;

class EntityAutocompleteMatcher extends \Drupal\Core\Entity\EntityAutocompleteMatcher {

  /**
   * Gets matched labels based on a given search string.
   */
  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '') {
    $account = \Drupal::currentUser();
    $user_roles = $account->getRoles(true);

    $is_admin = in_array('administrator', $user_roles);
    $is_pcm = in_array('principal_content_manager', $user_roles);
    $is_lcm = in_array('local_content_manager', $user_roles);
    $is_lcm_lite = in_array('local_light_content_manager', $user_roles);
    $is_sce = in_array('staff_content_editor', $user_roles);
    $is_pla = in_array('job_first_approver', $user_roles) || in_array('job_second_approver', $user_roles);

    $matches = [];
    $options = $selection_settings + [
      'target_type' => $target_type,
      'handler' => $selection_handler,
    ];

    $handler = $this->selectionManager->getInstance($options);

    if (isset($string)) {
      // Get an array of matching entities.
      $match_operator = !empty($selection_settings['match_operator']) ? $selection_settings['match_operator'] : 'CONTAINS';
      $entity_labels = $handler->getReferenceableEntities($string, $match_operator, 10);

      // Loop through the entities and convert them into autocomplete output.
      foreach ($entity_labels as $entity_type => $values) {
        // Filter results to only editable labs for LCM's...
        if($entity_type === 'laboratory' && ($is_lcm || $is_lcm_lite)) {
          $user = \Drupal\user\Entity\User::load($account->id());

          $editable_labs = [];
          if($user->hasField('field_editable_labs')) {
            foreach($user->get('field_editable_labs')->getValue() as $editable_lab) {
              $editable_labs[] = $editable_lab['target_id'];
            }
          }

          $values = array_filter($values, function($key) use($editable_labs) {
            return in_array($key, $editable_labs);
          }, ARRAY_FILTER_USE_KEY);
        }

        foreach ($values as $entity_id => $label) {
          /*$entity = \Drupal::entityTypeManager()->getStorage($target_type)->load($entity_id);
          if(!$entity->isPublished()) {
            // Filter out unpublished content.
            continue;
          }*/

          $key = "{$label} ({$entity_id})";

          // Strip things like starting/trailing white spaces, line breaks and
          // tags.
          $key = preg_replace('/\\s\\s+/', ' ', str_replace("\n", '', trim(Html::decodeEntities(strip_tags($key)))));

          // Names containing commas or quotes must be wrapped in quotes.
          $key = Tags::encode($key);
          $matches[] = array(
            'value' => $key,
            'label' => $label,
          );
        }
      }
    }

    return $matches;
  }
}
```



## Resources

- [Subscribe to and dispatch events on Drupal.org](https://www.drupal.org/docs/develop/creating-modules/subscribe-to-and-dispatch-events)
- [Overview of event dispatch and subscribing in the Drupal API](https://api.drupal.org/api/drupal/core%21core.api.php/group/events/)


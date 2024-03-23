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
   * Redirects to a specific URL when the site is in maintenance mode.
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

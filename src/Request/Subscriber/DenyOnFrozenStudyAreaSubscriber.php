<?php

namespace App\Request\Subscriber;

use App\Attribute\DenyOnFrozenStudyArea;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function assert;
use function is_object;
use function method_exists;
use function sprintf;
use function stripos;
use function strlen;
use function substr;

final readonly class DenyOnFrozenStudyAreaSubscriber
{
  use StudyAreaAwareSubscriberTrait;

  public function __construct(
    private TranslatorInterface $translator,
    private RouterInterface $router,
  ) {
  }

  /** Check if a study area is frozen, and if so, prevent editing. */
  #[AsEventListener(event: KernelEvents::CONTROLLER_ARGUMENTS, priority: -50)]
  public function checkFrozenStudyArea(ControllerArgumentsEvent $event): void
  {
    $attributes = $event->getAttributes(DenyOnFrozenStudyArea::class);
    if (empty($attributes)) {
      return;
    }

    $configuration = $attributes[0];

    // Check for frozen
    $studyArea = $this->getStudyArea($event, $configuration->subject);
    if ($studyArea->getFrozenOn() === null) {
      return;
    }

    // Verify forwarding url
    $request = $event->getRequest();
    if ($request->request->get('_route') === $configuration->route) {
      throw new InvalidArgumentException(sprintf('Forwarding route is the same as the route triggering it ("%s")', $configuration->route));
    }

    $session = $request->getSession();
    assert($session instanceof Session);
    $session->getFlashBag()->add('error', $this->translator->trans('study-area.frozen'));

    // Parse route params
    $arguments   = $event->getNamedArguments();
    $routeParams = [];
    foreach ($configuration->routeParams as $key => $param) {
      if (stripos((string)$param, '{') === 0 && stripos((string)$param, '}') === strlen((string)$param) - 1) {
        $param = substr((string)$param, 1, strlen((string)$param) - 2);
        if (array_key_exists($param, $arguments)) {
          $param = $arguments[$param];
          if (is_object($param) && method_exists($param, 'getId')) {
            $param = $param->getId();
          }
        }
      }
      $routeParams[$key] = $param;
    }

    // Redirect to new url
    $redirectRoute = $this->router->generate($configuration->route, $routeParams);
    $event->setController(static fn (): RedirectResponse => new RedirectResponse($redirectRoute));
  }
}

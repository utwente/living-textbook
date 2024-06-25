<?php

namespace App\Request\Subscriber;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Entity\StudyArea;
use App\Request\Wrapper\RequestStudyArea;
use Override;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DenyOnFrozenStudyAreaSubscriber implements EventSubscriberInterface
{
  public function __construct(
    private readonly TranslatorInterface $translator,
    private readonly RouterInterface $router)
  {
  }

  #[Override]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::CONTROLLER_ARGUMENTS => [
        'checkFrozenStudyArea',
        -50,
      ],
    ];
  }

  /** Check if a study area is frozen, and if so, prevent editing. */
  public function checkFrozenStudyArea(ControllerArgumentsEvent $event): void
  {
    $attributes = $event->getAttributes(DenyOnFrozenStudyArea::class);

    if (empty($attributes)) {
      return;
    }

    $configuration = $attributes[0];
    assert($configuration instanceof DenyOnFrozenStudyArea);

    // Retrieve subject
    $arguments = $event->getNamedArguments();
    if (!array_key_exists($configuration->subject, $arguments)) {
      throw new \InvalidArgumentException(sprintf('Subject "%s" not found', $configuration->subject));
    }

    $studyArea = $arguments[$configuration->subject];
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    // A study area is required
    if ($studyArea === null || !$studyArea instanceof StudyArea) {
      throw new InvalidArgumentException(sprintf('Subject "%s" does not contain the expected study area, but a "%s"',
        $configuration->subject, $studyArea === null ? 'null' : $studyArea::class));
    }

    // Check for frozen
    if ($studyArea->getFrozenOn() !== null) {
      // Verify forwarding url
      $request = $event->getRequest();
      if ($request->request->get('_route') === $configuration->route) {
        throw new \InvalidArgumentException(sprintf('Forwarding route is the same as the route triggering it ("%s")', $configuration->route));
      }

      $session = $request->getSession();
      assert($session instanceof Session);
      $session->getFlashBag()->add('error', $this->translator->trans('study-area.frozen'));

      // Parse route params
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
      $event->setController(fn () => new RedirectResponse($redirectRoute));
    }
  }
}

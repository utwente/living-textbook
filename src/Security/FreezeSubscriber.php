<?php

namespace App\Security;


use App\Entity\StudyArea;
use App\Request\Wrapper\RequestStudyArea;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FreezeSubscriber implements EventSubscriberInterface
{

  /** @var SessionInterface */
  private $session;

  /** @var TranslatorInterface */
  private $translator;

  /** @var RouterInterface */
  private $router;

  /**
   * FreezeSubscriber constructor.
   *
   * @param SessionInterface    $session
   * @param TranslatorInterface $translator
   * @param RouterInterface     $router
   */
  public function __construct(SessionInterface $session, TranslatorInterface $translator, RouterInterface $router)
  {
    $this->session    = $session;
    $this->translator = $translator;
    $this->router     = $router;
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return [
        KernelEvents::CONTROLLER_ARGUMENTS => [
            'checkFrozenStudyArea',
            -50,
        ],
    ];
  }

  /**
   * Check if a study area is frozen, and if so, prevent editing
   *
   * @param FilterControllerArgumentsEvent $event
   */
  public function checkFrozenStudyArea(FilterControllerArgumentsEvent $event)
  {
    $request = $event->getRequest();

    if (!$request->attributes->get('_deny_on_frozen_study_area')) {
      return;
    }

    $studyArea = NULL;
    foreach ($event->getArguments() as $argument) {
      if ($argument instanceof RequestStudyArea) {
        $studyArea = $argument->getStudyArea();
        break;
      }
      if ($argument instanceof StudyArea) {
        $studyArea = $argument;
        break;
      }
    }
    if (!$studyArea) {
      throw new InvalidArgumentException('No study area found.');
    }

    if ($studyArea->getFrozenOn() !== NULL) {
      $session = $this->session;
      assert($session instanceof Session);
      $session->getFlashBag()->add('error', $this->translator->trans('study-area.frozen', ['%date%' => $studyArea->getFrozenOn()->format('d-m-Y H:i')]));
      $currentRoute = $request->get('_route');
      $addInvoked   = stripos($currentRoute, 'add') !== false;
      $routeParams  = $request->get('_route_params');
      if (stripos($currentRoute, 'studyarea') !== false) {
        $redirectRoute = $this->router->generate('app_default_dashboard', $routeParams);
      } else if ($addInvoked) {
        $redirectRoute = $this->router->generate(str_replace('add', 'list', $currentRoute), $routeParams);
      } else {
        $redirectRoute = $this->matchAction('edit', $currentRoute, $routeParams)
            ?? $this->matchAction('remove', $currentRoute, $routeParams)
            ?? $this->router->generate('app_default_dashboard', $routeParams);
      }

      $event->setController(function () use ($redirectRoute) {
        return new RedirectResponse($redirectRoute);
      });
    }


  }

  /**
   * Try to match an action in a route, and then generate a new url to a non-editing environment
   *
   * @param string $action
   * @param        $currentRoute
   * @param        $routeParams
   *
   * @return string|null
   */
  private function matchAction(string $action, $currentRoute, $routeParams): ?string
  {
    if (stripos($currentRoute, $action) === false) return NULL;
    try {
      $redirectRoute = $this->router->generate(str_replace($action, 'show', $currentRoute), $routeParams);
    } catch (RouteNotFoundException $e) {
      try {
        $redirectRoute = $this->router->generate(str_replace($action, 'list', $currentRoute), $routeParams);
      } catch (RouteNotFoundException $e) {
        $redirectRoute = NULL;
      }
    }

    return $redirectRoute;
  }

}
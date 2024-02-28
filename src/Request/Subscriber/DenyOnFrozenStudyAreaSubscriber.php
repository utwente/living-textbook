<?php

namespace App\Request\Subscriber;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\StudyArea;
use App\Request\Wrapper\RequestStudyArea;
use Override;
use Sensio\Bundle\FrameworkExtraBundle\Request\ArgumentNameConverter;
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
  private ArgumentNameConverter $argumentNameConverter;

  private TranslatorInterface $translator;

  private RouterInterface $router;

  /** FreezeSubscriber constructor. */
  public function __construct(ArgumentNameConverter $argumentNameConverter, TranslatorInterface $translator, RouterInterface $router)
  {
    $this->argumentNameConverter = $argumentNameConverter;
    $this->translator            = $translator;
    $this->router                = $router;
  }

  /** @return array */
  #[Override]
  public static function getSubscribedEvents()
  {
    return [
      KernelEvents::CONTROLLER_ARGUMENTS => [
        'checkFrozenStudyArea',
        -50,
      ],
    ];
  }

  /** Check if a study area is frozen, and if so, prevent editing. */
  public function checkFrozenStudyArea(ControllerArgumentsEvent $event)
  {
    $request = $event->getRequest();

    // Verify the annotation is enabled
    if (!$configuration = $request->attributes->get('_' . DenyOnFrozenStudyArea::KEY)) {
      return;
    }
    assert($configuration instanceof DenyOnFrozenStudyArea);

    // Retrieve subject
    $arguments = $this->argumentNameConverter->getControllerArguments($event);
    if (!array_key_exists($configuration->getSubject(), $arguments)) {
      throw new \InvalidArgumentException(sprintf('Subject "%s" not found', $configuration->getSubject()));
    }

    $studyArea = $arguments[$configuration->getSubject()];
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    // An study area is required
    if ($studyArea === null || !$studyArea instanceof StudyArea) {
      throw new InvalidArgumentException(sprintf('Subject "%s" does not contain the expected study area, but a "%s"',
        $configuration->getSubject(), $studyArea === null ? 'null' : $studyArea::class));
    }

    // Check for frozen
    if ($studyArea->getFrozenOn() !== null) {
      // Verify forwarding url
      if ($arguments['_route'] === $configuration->getRoute()) {
        throw new \InvalidArgumentException(sprintf('Forwarding route is the same as the route triggering it ("%s")', $configuration->getRoute()));
      }

      $session = $request->getSession();
      assert($session instanceof Session);
      $session->getFlashBag()->add('error', $this->translator->trans('study-area.frozen'));

      // Parse route params
      $routeParams = [];
      foreach ($configuration->getRouteParams() as $key => $param) {
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
      $redirectRoute = $this->router->generate($configuration->getRoute(), $routeParams);
      $event->setController(fn () => new RedirectResponse($redirectRoute));
    }
  }
}

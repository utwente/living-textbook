<?php

namespace App\Security;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\StudyArea;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Request\ArgumentNameConverter;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DenyOnFrozenStudyAreaSubscriber implements EventSubscriberInterface
{

  private $argumentNameConverter;

  /** @var TranslatorInterface */
  private $translator;

  /** @var RouterInterface */
  private $router;

  /**
   * FreezeSubscriber constructor.
   *
   * @param ArgumentNameConverter $argumentNameConverter
   * @param TranslatorInterface   $translator
   * @param RouterInterface       $router
   */
  public function __construct(ArgumentNameConverter $argumentNameConverter, TranslatorInterface $translator, RouterInterface $router)
  {
    $this->argumentNameConverter = $argumentNameConverter;
    $this->translator            = $translator;
    $this->router                = $router;
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
    if ($studyArea === NULL || !$studyArea instanceof StudyArea) {
      throw new InvalidArgumentException(sprintf('Subject "%s" does not contain the expected study area, but a "%s"',
          $configuration->getSubject(), $studyArea === NULL ? 'null' : get_class($studyArea)));
    }

    // Check for frozen
    if ($studyArea->getFrozenOn() !== NULL) {
      // Verify forwarding url
      if ($arguments['_route'] === $configuration->getRoute()) {
        throw new \InvalidArgumentException(sprintf('Forwarding route is the same as the route triggering it ("%s")', $configuration->getRoute()));
      }

      $session = $request->getSession();
      assert($session instanceof Session);
      $session->getFlashBag()->add('error', $this->translator->trans('study-area.frozen', ['%date%' => $studyArea->getFrozenOn()->format('d-m-Y H:i')]));

      // Parse route params
      $routeParams = [];
      foreach ($configuration->getRouteParams() as $key => $param) {
        if (stripos($param, '{') === 0 && stripos($param, '}') === strlen($param) - 1) {
          $param = substr($param, 1, strlen($param) - 2);
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
      $event->setController(function () use ($redirectRoute) {
        return new RedirectResponse($redirectRoute);
      });
    }
  }
}

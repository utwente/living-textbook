<?php

namespace App\Request\Subscriber;

use App\Entity\StudyArea;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class KernelControllerSubscriber
 * Subscriber for KernelEvents related to controllers
 *
 * @author BobV
 */
class KernelControllerSubscriber implements EventSubscriberInterface
{

  /** @var string Study area session key */
  const SESSION_STUDY_AREA = '_studyArea';

  /** @var RouterInterface */
  private $router;

  /** @var StudyAreaRepository */
  private $studyAreaRepository;

  /** @var StudyArea|null */
  private $studyArea;

  /** @var int|null */
  private $studyAreaId;

  /**
   * KernelControllerSubscriber constructor.
   *
   * @param RouterInterface     $router
   * @param StudyAreaRepository $studyAreaRepository
   */
  public function __construct(RouterInterface $router, StudyAreaRepository $studyAreaRepository)
  {
    $this->router              = $router;
    $this->studyAreaRepository = $studyAreaRepository;
    $this->studyArea           = NULL;
    $this->studyAreaId         = NULL;
  }

  /**
   * Determine the events to subscribe to
   *
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return array(
        KernelEvents::CONTROLLER           => [
            array('determineStudyArea', 0),
        ],
        KernelEvents::CONTROLLER_ARGUMENTS => [
            array('injectStudyArea', 0),
        ],

    );
  }

  /**
   * Determine the study area for this request
   *
   * @param FilterControllerEvent $event
   */
  public function determineStudyArea(FilterControllerEvent $event)
  {
    $request = $event->getRequest();
    $session = $request->getSession();

    // Retrieve study area id from route
    $studyAreaId = $request->attributes->get(self::SESSION_STUDY_AREA, NULL);

    // Check the study area
    if (!$studyAreaId) {
      // Try to retrieve it from the session
      if ($session->has(self::SESSION_STUDY_AREA)) {
        $studyAreaId = $session->get(self::SESSION_STUDY_AREA);
      } else {
        // @todo determine default in case of null
        $studyAreaId = 1;
      }
    }

    // Save in memory for usage and in session as backup
    $this->studyAreaId = $studyAreaId;
    $session->set(self::SESSION_STUDY_AREA, $studyAreaId);

    // Inject this into the router context
    $this->router->getContext()->setParameter(self::SESSION_STUDY_AREA, $studyAreaId);
  }

  public function injectStudyArea(FilterControllerArgumentsEvent $event)
  {
    if ($this->studyAreaId === NULL) {
      // Check for session value
      $session = $event->getRequest()->getSession();
      if (!$session->has(self::SESSION_STUDY_AREA)) return;

      $this->studyAreaId = $session->get(self::SESSION_STUDY_AREA);
    }

    $controller = $event->getController();
    $arguments  = $event->getArguments();

    try {
      if (!is_array($controller) || count($controller) != 2) return;
      $reflFunction = new \ReflectionMethod($controller[0], $controller[1]);
      $reflParams   = $reflFunction->getParameters();
      foreach ($reflParams as $key => $reflParam) {
        // Check for correct method argument
        if (!$reflParam->hasType()) continue;
        if ($reflParam->getType()->getName() != RequestStudyArea::class) continue;

        // Check whether it is already set
        /** @var RequestStudyArea|null $argument */
        $argument = $arguments[$key];
        if ($argument !== NULL && $argument->hasValue()) continue;

        // Cache study area during request
        if ($this->studyArea == NULL) {
          $this->studyArea = $this->studyAreaRepository->find($this->studyAreaId);
        }

        // Save value
        $arguments[$key] = new RequestStudyArea($this->studyArea);
      }

      // Set the arguments
      $event->setArguments($arguments);
    } catch (\ReflectionException $e) {
      // Do nothing
    }
  }
}

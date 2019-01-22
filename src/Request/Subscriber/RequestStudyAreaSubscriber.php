<?php

namespace App\Request\Subscriber;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class RequestStudyAreaSubscriber
 * Subscriber for KernelEvents related to controllers
 *
 * @author BobV
 */
class RequestStudyAreaSubscriber implements EventSubscriberInterface
{

  /** @var string Study area session/request key */
  public const STUDY_AREA_KEY = '_studyArea';

  /** @var string Study area twig key */
  public const TWIG_STUDY_AREA_KEY = '_twigStudyArea';

  /** @var RouterInterface */
  private $router;

  /** @var StudyAreaRepository */
  private $studyAreaRepository;

  /** @var TokenStorageInterface */
  private $tokenStorage;

  /** @var \Twig_Environment */
  private $twig;

  /** @var StudyArea|null */
  private $studyArea;

  /** @var int|null */
  private $studyAreaId;

  /**
   * RequestStudyAreaSubscriber constructor.
   *
   * @param RouterInterface       $router
   * @param StudyAreaRepository   $studyAreaRepository
   * @param TokenStorageInterface $tokenStorage
   * @param \Twig_Environment     $twig
   */
  public function __construct(RouterInterface $router, StudyAreaRepository $studyAreaRepository, TokenStorageInterface $tokenStorage, \Twig_Environment $twig)
  {
    $this->router              = $router;
    $this->studyAreaRepository = $studyAreaRepository;
    $this->tokenStorage        = $tokenStorage;
    $this->twig                = $twig;
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
            array('determineStudyArea', 100),
        ],
        KernelEvents::CONTROLLER_ARGUMENTS => [
            array('injectStudyAreaInControllerArguments', 100),
        ],
        KernelEvents::VIEW                 => [
            array('injectStudyAreaInView', 255),
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
    $studyAreaId = $request->attributes->get(self::STUDY_AREA_KEY, NULL);

    // Check whether it actually exists, throw not found otherwise
    if ($studyAreaId && !$this->studyAreaRepository->find($studyAreaId)) {
      throw new NotFoundHttpException("Study area not found");
    }

    // Check the study area
    if (!$studyAreaId) {

      // Set to NULL to force empty study area
      $studyAreaId = NULL;

      // Try to retrieve it from the session
      if ($session->has(self::STUDY_AREA_KEY)) {
        $studyAreaId = $session->get(self::STUDY_AREA_KEY);

        // Check whether it actually still exists, remove from session otherwise
        if (!$studyAreaId || !$this->studyAreaRepository->find($studyAreaId)) {
          $session->remove(self::STUDY_AREA_KEY);
          $studyAreaId = NULL;
        }
      }

      // Invalid or no result from session
      if ($studyAreaId === NULL) {
        // Try to find a visible study area
        $token = $this->tokenStorage->getToken();
        if ($token !== NULL && ($user = $token->getUser()) instanceof User) {
          if (NULL !== ($studyArea = $this->studyAreaRepository->getFirstVisible($user))) {
            assert($studyArea instanceof StudyArea);
            $studyAreaId     = $studyArea->getId();
            $this->studyArea = $studyArea;
          }
        }
      }
    }

    // Save in memory for usage and in session as backup
    if ($this->studyAreaId !== $studyAreaId) {
      $this->studyArea   = NULL;
      $this->studyAreaId = $studyAreaId;
      $session->set(self::STUDY_AREA_KEY, $studyAreaId);
    }

    // Inject this into the router context
    $this->router->getContext()->setParameter(self::STUDY_AREA_KEY, $studyAreaId);
  }

  /**
   * Inject the StudyArea in the controller arguments when required
   *
   * @param FilterControllerArgumentsEvent $event
   */
  public function injectStudyAreaInControllerArguments(FilterControllerArgumentsEvent $event)
  {
    if ($this->studyAreaId === NULL) {
      // Check for session value
      $session = $event->getRequest()->getSession();
      if (!$session->has(self::STUDY_AREA_KEY)) return;

      $this->studyAreaId = $session->get(self::STUDY_AREA_KEY);
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
        if ($this->studyArea == NULL && $this->studyAreaId !== -1) {
          $this->studyArea = $this->studyAreaRepository->find($this->studyAreaId);
        }

        // Save value in wrapper (as otherwise the Doctrine mapper would kick in)
        // The value might be null
        $arguments[$key] = new RequestStudyArea($this->studyArea);
      }

      // Set the arguments
      $event->setArguments($arguments);
    } catch (\ReflectionException $e) {
      // Do nothing
    }
  }

  /**
   * Inject the StudyArea in the twig variables for the view
   */
  public function injectStudyAreaInView()
  {
    $this->twig->addGlobal(self::TWIG_STUDY_AREA_KEY, $this->studyArea);
  }
}

<?php

namespace App\Request\Subscriber;

use App\Api\ApiErrorResponse;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Naming\NamingService;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use function Symfony\Component\String\b;

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
  /**
   * @var NamingService
   */
  private $namingService;

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

  public function __construct(
      RouterInterface       $router,
      StudyAreaRepository   $studyAreaRepository,
      TokenStorageInterface $tokenStorage,
      Environment           $twig,
      NamingService         $namingService)
  {
    $this->router              = $router;
    $this->studyAreaRepository = $studyAreaRepository;
    $this->tokenStorage        = $tokenStorage;
    $this->twig                = $twig;
    $this->namingService       = $namingService;
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
    return [
        KernelEvents::CONTROLLER           => [
            ['determineStudyArea', 100],
            ['validateApiStudyArea', 99],
            ['injectStudyAreaInNamingService', 98],
            ['injectStudyAreaInView', 98],
        ],
        KernelEvents::CONTROLLER_ARGUMENTS => [
            ['injectStudyAreaInControllerArguments', 100],
        ],
    ];
  }

  /**
   * Determine the study area for this request
   *
   * @param ControllerEvent $event
   */
  public function determineStudyArea(ControllerEvent $event)
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
      if ($session && $session->has(self::STUDY_AREA_KEY)) {
        $studyAreaId = $session->get(self::STUDY_AREA_KEY);

        // Check whether it actually still exists, remove from session otherwise
        if (!$studyAreaId || !$this->studyAreaRepository->find($studyAreaId)) {
          $session->remove(self::STUDY_AREA_KEY);
          $studyAreaId = NULL;
        }
      }

      // Invalid or no result from session
      if ($studyAreaId === NULL) {
        // Resolve the user
        $token = $this->tokenStorage->getToken();
        $user  = $token !== NULL ? $token->getUser() : NULL;
        $user  = is_object($user) ? $user : NULL;
        assert($user === NULL || $user instanceof User);

        // Try to find a visible study area
        if (NULL !== ($studyArea = $this->studyAreaRepository->getFirstVisible($user))) {
          assert($studyArea instanceof StudyArea);
          $studyAreaId     = $studyArea->getId();
          $this->studyArea = $studyArea;
        }
      }
    }

    // Save in memory for usage and in session as backup
    if ($this->studyAreaId !== $studyAreaId) {
      $this->studyArea   = NULL;
      $this->studyAreaId = $studyAreaId;
      if ($session) {
        $session->set(self::STUDY_AREA_KEY, $studyAreaId);
      }
    }

    // Inject this into the router context
    $this->router->getContext()->setParameter(self::STUDY_AREA_KEY, $studyAreaId);
  }

  /**
   * Inject the StudyArea in the controller arguments when required
   *
   * @param ControllerArgumentsEvent $event
   */
  public function injectStudyAreaInControllerArguments(ControllerArgumentsEvent $event)
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
      $reflFunction = new ReflectionMethod($controller[0], $controller[1]);
      $reflParams   = $reflFunction->getParameters();
      foreach ($reflParams as $key => $reflParam) {
        // Check for correct method argument
        if (!$reflParam->hasType()) continue;
        /* @phan-suppress-next-line PhanUndeclaredMethod */
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
    } catch (ReflectionException $e) {
      // Do nothing
    }
  }

  /**
   * Inject the StudyArea in the twig variables for the view
   */
  public function injectStudyAreaInView()
  {
    $this->testCache();
    $this->twig->addGlobal(self::TWIG_STUDY_AREA_KEY, $this->studyArea);
  }

  /**
   * Inject the StudyArea in the naming service
   */
  public function injectStudyAreaInNamingService()
  {
    $this->testCache();
    $this->namingService->injectStudyArea($this->studyArea);
  }

  public function validateApiStudyArea(ControllerEvent $event): void
  {
    $this->testCache();

    // API must be enabled for the selected study area
    if (!$this->studyArea || $this->studyArea->isApiEnabled()) {
      return;
    }

    // Validate it is actually an API request
    $controllerName = b($event->getRequest()->attributes->get('_controller'));
    if (!$controllerName->startsWith('App\\Api\\Controller\\')) {
      return;
    }

    // Return an error response
    $event->setController(function () {
      return new ApiErrorResponse('API disabled', Response::HTTP_FORBIDDEN, 'API not enabled for this study area');
    });
  }

  /**
   * Test the internal study area cache
   */
  private function testCache(): void
  {
    // Cache study area during request
    if ($this->studyArea == NULL && $this->studyAreaId !== NULL && $this->studyAreaId !== -1) {
      $this->studyArea = $this->studyAreaRepository->find($this->studyAreaId);
    }
  }
}

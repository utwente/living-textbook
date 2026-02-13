<?php

namespace App\Request\Subscriber;

use App\Api\ApiErrorResponse;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Naming\NamingService;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Override;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

use function assert;
use function count;
use function is_array;
use function is_object;
use function Symfony\Component\String\b;

/**
 * Subscriber for KernelEvents related to controllers.
 */
class RequestStudyAreaSubscriber implements EventSubscriberInterface
{
  /** @var string Study area session/request key */
  final public const string STUDY_AREA_KEY = '_studyArea';

  /** @var string Study area twig key */
  final public const string TWIG_STUDY_AREA_KEY = '_twigStudyArea';

  private ?StudyArea $studyArea = null;

  private ?int $studyAreaId = null;

  public function __construct(
    private readonly RouterInterface $router,
    private readonly StudyAreaRepository $studyAreaRepository,
    private readonly TokenStorageInterface $tokenStorage,
    private readonly Environment $twig,
    private readonly NamingService $namingService)
  {
  }

  /** Determine the events to subscribe to. */
  #[Override]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::CONTROLLER => [
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

  /** Determine the study area for this request. */
  public function determineStudyArea(ControllerEvent $event): void
  {
    $request = $event->getRequest();
    $session = $request->getSession();

    // Retrieve study area id from route
    $studyAreaId = $request->attributes->get(self::STUDY_AREA_KEY, null);

    // Check whether it actually exists, throw not found otherwise
    if ($studyAreaId && !$this->studyAreaRepository->find($studyAreaId)) {
      throw new NotFoundHttpException('Study area not found');
    }

    // Check the study area
    if (!$studyAreaId) {
      // Set to NULL to force empty study area
      $studyAreaId = null;

      // Try to retrieve it from the session
      if ($session && $session->has(self::STUDY_AREA_KEY)) {
        $studyAreaId = $session->get(self::STUDY_AREA_KEY);

        // Check whether it actually still exists, remove from session otherwise
        if (!$studyAreaId || !$this->studyAreaRepository->find($studyAreaId)) {
          $session->remove(self::STUDY_AREA_KEY);
          $studyAreaId = null;
        }
      }

      // Invalid or no result from session
      if ($studyAreaId === null) {
        // Resolve the user
        $token = $this->tokenStorage->getToken();
        $user  = $token?->getUser();
        $user  = is_object($user) ? $user : null;
        assert($user === null || $user instanceof User);

        // Try to find a visible study area
        if (null !== ($studyArea = $this->studyAreaRepository->getFirstVisible($user))) {
          assert($studyArea instanceof StudyArea);
          $studyAreaId     = $studyArea->getId();
          $this->studyArea = $studyArea;
        }
      }
    }

    // Save in memory for usage and in session as backup
    if ($this->studyAreaId !== $studyAreaId) {
      $this->studyArea   = null;
      $this->studyAreaId = $studyAreaId;
      if ($session) {
        $session->set(self::STUDY_AREA_KEY, $studyAreaId);
      }
    }

    // Inject this into the router context
    $this->router->getContext()->setParameter(self::STUDY_AREA_KEY, $studyAreaId);
  }

  /** Inject the StudyArea in the controller arguments when required. */
  public function injectStudyAreaInControllerArguments(ControllerArgumentsEvent $event): void
  {
    if ($this->studyAreaId === null) {
      // Check for session value
      $session = $event->getRequest()->getSession();
      if (!$session->has(self::STUDY_AREA_KEY)) {
        return;
      }

      $this->studyAreaId = $session->get(self::STUDY_AREA_KEY);
    }

    $controller = $event->getController();
    $arguments  = $event->getArguments();

    try {
      if (!is_array($controller) || count($controller) != 2) {
        return;
      }
      $reflFunction = new ReflectionMethod($controller[0], $controller[1]);
      $reflParams   = $reflFunction->getParameters();
      foreach ($reflParams as $key => $reflParam) {
        // Check for correct method argument
        if (!$reflParam->hasType()) {
          continue;
        }
        $reflType = $reflParam->getType();
        if (!$reflType instanceof ReflectionNamedType || $reflType->getName() !== RequestStudyArea::class) {
          continue;
        }

        // Check whether it is already set
        /** @var RequestStudyArea|null $argument */
        $argument = $arguments[$key];
        if ($argument !== null && $argument->hasValue()) {
          continue;
        }

        // Cache study area during request
        if ($this->studyArea == null && $this->studyAreaId !== -1) {
          $this->studyArea = $this->studyAreaRepository->find($this->studyAreaId);
        }

        // Save value in wrapper (as otherwise the Doctrine mapper would kick in)
        // The value might be null
        $arguments[$key] = new RequestStudyArea($this->studyArea);
      }

      // Set the arguments
      $event->setArguments($arguments);
    } catch (ReflectionException) {
      // Do nothing
    }
  }

  /** Inject the StudyArea in the twig variables for the view */
  public function injectStudyAreaInView(): void
  {
    $this->testCache();
    $this->twig->addGlobal(self::TWIG_STUDY_AREA_KEY, $this->studyArea);
  }

  /** Inject the StudyArea in the naming service */
  public function injectStudyAreaInNamingService(): void
  {
    $this->testCache();
    $this->namingService->injectStudyArea($this->studyArea);
  }

  public function validateApiStudyArea(ControllerEvent $event): void
  {
    $this->testCache();

    // If a study area has not been found, we cannot check anything here
    if (!$this->studyArea) {
      return;
    }

    // Validate it is actually an API request, exclude study area list to allow checking which API enabled study areas are available to this token/user
    $requestAttributes = $event->getRequest()->attributes;
    if (!b($requestAttributes->get('_controller'))->startsWith('App\\Api\\Controller\\')
        || $requestAttributes->get('_route') === 'api_study_area_list') {
      return;
    }

    // API must be enabled for the selected study area
    if ($this->studyArea->isApiEnabled()) {
      // Validate whether the area is frozen
      if (!$event->getRequest()->isMethod('GET') && $this->studyArea->isFrozen()) {
        $event->setController(static fn () => new ApiErrorResponse(
          'Study area frozen',
          Response::HTTP_BAD_REQUEST,
          'This study area has been frozen'
        ));
      }

      return;
    }

    // Return an error response
    $event->setController(static fn () => new ApiErrorResponse(
      'API disabled',
      Response::HTTP_FORBIDDEN,
      'API not enabled for this study area'
    ));
  }

  /** Test the internal study area cache */
  private function testCache(): void
  {
    // Cache study area during request
    if ($this->studyArea == null && $this->studyAreaId !== null && $this->studyAreaId !== -1) {
      $this->studyArea = $this->studyAreaRepository->find($this->studyAreaId);
    }
  }
}

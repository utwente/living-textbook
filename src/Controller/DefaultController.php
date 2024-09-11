<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\Contributor;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Form\Authentication\LoginType;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\LearningPathRepository;
use App\Repository\StudyAreaRepository;
use App\Repository\TagRepository;
use App\Repository\UserBrowserStateRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use App\UrlUtils\Model\Url;
use App\UrlUtils\UrlChecker;
use App\UrlUtils\UrlScanner;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
  /** @var Concept[] */
  private ?array $concepts = null;

  /** @var Contributor[] */
  private ?array $contributors = null;

  /** @var LearningOutcome[] */
  private ?array $learningOutcomes = null;

  /** @var ExternalResource[] */
  private ?array $externalResources = null;

  /** @var LearningPath[] */
  private ?array $learningPaths = null;

  #[Route('/page/{_studyArea<\d+>}/{pageUrl<.+>}', name: '_home', options: ['expose' => true, 'no_login_wrap' => true], defaults: ['_studyArea' => null, 'pageUrl' => ''])]
  #[Route('/page/{pageUrl<.+>}', name: '_home_simple', options: ['no_login_wrap' => true], defaults: ['_studyArea' => null, 'pageUrl' => ''])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function index(
    Request $request, RequestStudyArea $requestStudyArea, string $pageUrl, RouterInterface $router,
    UserBrowserStateRepository $userBrowserStateRepository, ?Profiler $profiler): Response
  {
    // Disable profiler on the home page
    $profiler?->disable();

    // Check for empty study area
    if (!$requestStudyArea->hasValue()) {
      // This means that there is no study area found for the user
      return $this->redirectToRoute('app_studyarea_add_first', ['_studyArea' => 0]);
    }

    // Retrieve actual study area from wrapper
    $studyArea = $requestStudyArea->getStudyArea();

    // Validate authentication
    if (!$this->isGranted(StudyAreaVoter::SHOW, $studyArea)) {
      // Forward to dashboard
      return $this->redirectToRoute('app_default_landing');
    }

    $user = $this->getUser();
    assert($user === null || $user instanceof User);

    return $this->render('double_column.html.twig', [
      'studyArea'    => $studyArea,
      'browserState' => $user ? $userBrowserStateRepository->findForUser($user, $studyArea) : null,
      'pageUrl'      => $pageUrl != ''
          ? '/' . $studyArea->getId() . '/' . $pageUrl
          : $router->generate('app_default_dashboard', ['_studyArea' => $studyArea->getId()]),
      'openMap' => $request->query->has('open'),
    ]);
  }

  #[Route('/', options: ['no_login_wrap' => true])]
  #[Route('', name: 'base_url', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function landing(
    Request $request, FormFactoryInterface $formFactory, TranslatorInterface $translator,
    StudyAreaRepository $studyAreaRepository): Response
  {
    $user    = $this->getUser();
    $session = $request->getSession();
    assert($user === null || $user instanceof User);

    // When there is no user, render the login form
    if (!$user) {
      $loginForm = $this->createForm(LoginType::class, [
        '_username' => $session->get(SecurityRequestAttributes::LAST_USERNAME, ''),
      ], [
        'action' => $this->generateUrl('login_check'),
      ]);

      // Retrieve the error and remove it from the session
      if ($session->has(SecurityRequestAttributes::AUTHENTICATION_ERROR)) {
        $authError = $session->get(SecurityRequestAttributes::AUTHENTICATION_ERROR);
        $session->remove(SecurityRequestAttributes::AUTHENTICATION_ERROR);

        // Check the actual error
        if ($authError instanceof BadCredentialsException) {
          // Bad credentials given
          $this->addFlash('authError', $translator->trans('login.bad-credentials'));
        } else {
          // General error occurred
          $this->addFlash('authError', $translator->trans('login.general-error'));
        }
      }
    }

    // Retrieve available study areas (not authenticated users can have them as well!)
    $studyAreas = $studyAreaRepository->getVisible($user);

    // Only show select form when there is more than 1 visible study area
    $studyAreaCount = count($studyAreas);
    $studyAreaForm  = $studyAreaCount > 1
        ? $this->createStudyAreaForm($formFactory, $translator, $user, null, 'dashboard.open') : null;

    return $this->render('default/landing.html.twig', [
      'loginForm'       => isset($loginForm) ? $loginForm->createView() : null,
      'loginFormActive' => $session->get(SecurityRequestAttributes::LAST_USERNAME, '') !== '',
      'singleStudyArea' => count($studyAreas) === 1 ? reset($studyAreas) : null,
      'studyAreaCount'  => $studyAreaCount,
      'studyAreaForm'   => $studyAreaForm?->createView(),
    ]);
  }

  /**
   * @throws InvalidArgumentException
   * @throws NonUniqueResultException
   */
  #[Route('/{_studyArea<\d+>}/dashboard')]
  #[Route('/{_studyArea<\d+>}/dashboard', name: 'app_studyarea_list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function dashboard(
    RequestStudyArea $requestStudyArea, FormFactoryInterface $formFactory, StudyAreaRepository $studyAreaRepository,
    ConceptRepository $conceptRepo, ConceptRelationRepository $conceptRelationRepo,
    ContributorRepository $contributorRepository, AbbreviationRepository $abbreviationRepository,
    ExternalResourceRepository $externalResourceRepo, LearningOutcomeRepository $learningOutcomeRepo,
    LearningPathRepository $learningPathRepo, TagRepository $tagRepository,
    UrlChecker $urlChecker, TranslatorInterface $translator): Response
  {
    $user = $this->getUser();
    assert($user instanceof User);

    $studyArea  = $requestStudyArea->getStudyArea();
    $studyAreas = $studyAreaRepository->getVisible($user);

    // Only show switch form when there is more than 1 visible study area
    $studyAreaForm = count($studyAreas) > 1
        ? $this->createStudyAreaForm($formFactory, $translator, $user, $studyArea) : null;

    $conceptForm = $formFactory->createNamedBuilder('concept_form')
      ->add('concept', EntityType::class, [
        'placeholder'   => 'dashboard.select-one',
        'hide_label'    => true,
        'choice_label'  => 'name',
        'class'         => Concept::class,
        'select2'       => true,
        'query_builder' => fn (ConceptRepository $conceptRepository) => $conceptRepository->findForStudyAreaOrderByNameQb($studyArea),
      ])
      ->add('submit', SubmitType::class, [
        'disabled' => true,
        'label'    => 'browser.search',
        'icon'     => 'fa-chevron-right',
        'attr'     => [
          'class' => 'btn btn-outline-success',
        ],
      ])
      ->getForm();

    // Check for urls, if one has edit roles
    $urlData = [];
    if ($this->isGranted('STUDYAREA_EDIT', $studyArea)) {
      $urls        = $urlChecker->getUrlsForStudyArea($studyArea);
      $badUrls     = $urlChecker->checkStudyArea($studyArea);
      $urlsScanned = $urls !== null;

      $urlData = [
        'urlScanned'      => $urlsScanned,
        'urlScanProgress' => ($urlsScanned ? $urls['urls'] === null : false),
        'urlCount'        => ($urlsScanned ? count($urls['urls']) : -1),
        'brokenUrlCount'  => ($badUrls !== null ? count($badUrls['bad']) : -1),
      ];
    }

    return $this->render('default/dashboard.html.twig', array_merge([
      'conceptForm'           => $conceptForm->createView(),
      'studyAreaForm'         => $studyAreaForm?->createView(),
      'studyArea'             => $studyArea,
      'studyAreas'            => $studyAreas,
      'currentStudyArea'      => $requestStudyArea->getStudyArea(),
      'conceptCount'          => $conceptRepo->getCountForStudyArea($studyArea, true),
      'instanceCount'         => $conceptRepo->getCountForStudyArea($studyArea, false, true),
      'relationCount'         => $conceptRelationRepo->getCountForStudyArea($studyArea),
      'abbreviationCount'     => $abbreviationRepository->getCountForStudyArea($studyArea),
      'contributorCount'      => $contributorRepository->getCountForStudyArea($studyArea),
      'externalResourceCount' => $externalResourceRepo->getCountForStudyArea($studyArea),
      'learningOutcomeCount'  => $learningOutcomeRepo->getCountForStudyArea($studyArea),
      'learningPathCount'     => $learningPathRepo->getCountForStudyArea($studyArea),
      'tagCount'              => $tagRepository->getCountForStudyArea($studyArea),
    ], $urlData));
  }

  /** @throws InvalidArgumentException */
  #[Route('/{_studyArea<\d+>}/urls')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function urlOverview(
    RequestStudyArea $requestStudyArea, UrlChecker $urlChecker, ConceptRepository $conceptRepository,
    ContributorRepository $contributorRepository, LearningOutcomeRepository $learningOutcomeRepository,
    ExternalResourceRepository $externalResourceRepository, LearningPathRepository $learningPathRepository): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    $urls      = $urlChecker->getUrlsForStudyArea($studyArea);
    // Not scanned yet, early return
    if ($urls === null) {
      return $this->render('default/url_overview.html.twig', ['lastScanned' => null]);
    }
    $badUrls = $urlChecker->checkStudyArea($studyArea) ?? ['bad' => [], 'unscanned' => [], 'wrongStudyArea' => []];
    // Get good urls
    $goodUrls = array_diff($urls['urls'], $badUrls['bad'], $badUrls['unscanned'], $badUrls['wrongStudyArea']);

    // Get linked objects
    $this->concepts          = $this->mapArrayById($conceptRepository->findForStudyAreaOrderedByName($studyArea));
    $this->contributors      = $this->mapArrayById($contributorRepository->findForStudyArea($studyArea));
    $this->learningOutcomes  = $this->mapArrayById($learningOutcomeRepository->findForStudyArea($studyArea));
    $this->externalResources = $this->mapArrayById($externalResourceRepository->findForStudyArea($studyArea));
    $this->learningPaths     = $this->mapArrayById($learningPathRepository->findForStudyArea($studyArea));

    // Split the various arrays, while simultaneously sorting them
    [$badInternalUrls, $badExternalUrls]             = $this->splitUrlLocation($badUrls['bad']);
    [$unscannedInternalUrls, $unscannedExternalUrls] = $this->splitUrlLocation($badUrls['unscanned']);
    [$goodInternalUrls, $goodExternalUrls]           = $this->splitUrlLocation($goodUrls);

    return $this->render('default/url_overview.html.twig', [
      'lastScanned'           => $urls['lastScanned'],
      'badInternalUrls'       => $badInternalUrls,
      'wrongStudyAreaUrls'    => $badUrls['wrongStudyArea'],
      'badExternalUrls'       => $badExternalUrls,
      'unscannedInternalUrls' => $unscannedInternalUrls,
      'unscannedExternalUrls' => $unscannedExternalUrls,
      'goodInternalUrls'      => $goodInternalUrls,
      'goodExternalUrls'      => $goodExternalUrls,
      'objects'               => [
        'concepts'          => $this->concepts,
        'contributors'      => $this->contributors,
        'learningOutcomes'  => $this->learningOutcomes,
        'externalResources' => $this->externalResources,
        'learningPaths'     => $this->learningPaths,
      ],
    ]);
  }

  /** @throws InvalidArgumentException */
  #[Route('/{_studyArea<\d+>}/rescanurl/{url}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function urlRescan(RequestStudyArea $requestStudyArea, UrlChecker $urlChecker, UrlScanner $urlScanner, TranslatorInterface $translator, $url): Response
  {
    $url    = $urlScanner->scanText(sprintf('src="%s"', urldecode((string)$url)));
    $result = $urlChecker->checkUrl($url[0], true, false) ?
        $translator->trans('url.good') :
        $translator->trans('url.bad');
    $this->addFlash('info', $translator->trans('url.rescanned', ['%result%' => strtolower($result)]));

    return $this->redirect($this->generateUrl('app_default_urloverview'));
  }

  /** @throws InvalidArgumentException */
  #[Route('/{_studyArea<\d+>}/rescanurls')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function urlRescanStudyArea(RequestStudyArea $requestStudyArea, UrlChecker $urlChecker, TranslatorInterface $translator): Response
  {
    $urlChecker->checkStudyArea($requestStudyArea->getStudyArea(), false, false);
    $this->addFlash('info', $translator->trans('url.rescanned-study-area'));

    return $this->redirect($this->generateUrl('app_default_urloverview'));
  }

  private function createStudyAreaForm(
    FormFactoryInterface $formFactory, TranslatorInterface $translator,
    ?User $user, ?StudyArea $studyArea, string $buttonLabel = 'study-area.switch-to'): FormInterface
  {
    $defaultGroupName = $translator->trans('study-area.groups.default-name');

    return $formFactory->createNamedBuilder('studyarea_form')
      ->add('studyArea', EntityType::class, [
        'placeholder' => 'dashboard.select-one',
        'hide_label'  => true,
        'class'       => StudyArea::class,
        'select2'     => true,
        'group_by'    => function (StudyArea $studyArea) use ($defaultGroupName) {
          if (!$studyArea->getGroup()) {
            return $defaultGroupName;
          }

          return $studyArea->getGroup()->getName();
        },
        'query_builder' => function (StudyAreaRepository $studyAreaRepository) use ($user, $studyArea) {
          $qb = $studyAreaRepository->getVisibleQueryBuilder($user);
          if ($studyArea !== null) {
            $qb->andWhere('sa != :current')
              ->setParameter('current', $studyArea);
          }

          return $qb;
        },
      ])
      ->add('submit', SubmitType::class, [
        'disabled' => true,
        'label'    => $buttonLabel,
        'icon'     => 'fa-chevron-right',
        'attr'     => [
          'class' => 'btn btn-outline-success',
        ],
      ])
      ->getForm();
  }

  /** Filter an Url based on whether it's original object is deleted. */
  private function filterDeleted(Url $entry): bool
  {
    $context = $entry->getContext();
    $class   = $context->getClass();
    $id      = $context->getId();

    switch ($class) {
      case StudyArea::class:
        return true; // Cannot be deleted in this context
      case Concept::class:
        return array_key_exists($id, $this->concepts);
      case LearningOutcome::class:
        return array_key_exists($id, $this->learningOutcomes);
      case LearningPath::class:
        return array_key_exists($id, $this->learningPaths);
      case ExternalResource::class:
        return array_key_exists($id, $this->externalResources);
      case Contributor::class:
        return array_key_exists($id, $this->contributors);
      default:
        return false;
    }
  }

  /** Filter an Url based on it being internal or not. */
  private function filterInternal(Url $entry): bool
  {
    return $entry->isInternal();
  }

  /** Get the Id of an object if it exists. */
  private function findId($entry): int
  {
    return method_exists($entry, 'getId') ? $entry->getId() : -1;
  }

  /** Add the Id of an array of objects as key, for easier searching of arrays. */
  private function mapArrayById(array $objects): array
  {
    $objectIds = array_map($this->findId(...), $objects);

    return array_combine($objectIds, $objects);
  }

  /** Sort an array of urls for viewing in the overview, then split them according to location. */
  private function splitUrlLocation(?array $urls): array
  {
    $urls = array_filter($urls, $this->filterDeleted(...));
    usort($urls, $this->sortUrls(...));
    $internalUrls = array_filter($urls, $this->filterInternal(...));
    $externalUrls = array_diff($urls, $internalUrls);

    return [$internalUrls, $externalUrls];
  }

  /**
   * Sort two urls for in the overview page. First they are ordered based on class, then on entity name or number, then
   * on property name.
   */
  private function sortUrls(Url $a, Url $b): int
  {
    $aContext = $a->getContext();
    $bContext = $b->getContext();
    $aId      = $aContext->getId();
    $bId      = $bContext->getId();
    $aClass   = $aContext->getClass();
    $bClass   = $bContext->getClass();
    $aPath    = $aContext->getPath();
    $bPath    = $bContext->getPath();
    switch ($aClass) {
      case StudyArea::class:
        return $bClass === StudyArea::class ? strcasecmp($aPath, $bPath) : -1;
      case Concept::class:
        switch ($bClass) {
          case StudyArea::class:
            return 1;
          case Concept::class:
            if ($aId === $bId) {
              return strcasecmp($aPath, $bPath);
            }

            return strcasecmp($this->concepts[$aId]->getName(), $this->concepts[$bId]->getName());
          default:
            return -1;
        }
        // no break
      case LearningOutcome::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
            return 1;
          case LearningOutcome::class:
            if ($aId === $bId) {
              return strcasecmp($aPath, $bPath);
            }

            return $this->learningOutcomes[$aId]->getNumber() <=> $this->learningOutcomes[$bId]->getNumber();
          default:
            return -1;
        }
        // no break
      case LearningPath::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
          case LearningOutcome::class:
            return 1;
          case LearningPath::class:
            if ($aId === $bId) {
              return strcasecmp($aPath, $bPath);
            }

            return strcasecmp($this->learningPaths[$aId]->getName(), $this->learningPaths[$bId]->getName());
          default:
            return -1;
        }
        // no break
      case ExternalResource::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
          case LearningOutcome::class:
          case LearningPath::class:
            return 1;
          case ExternalResource::class:
            if ($aId === $bId) {
              return strcasecmp($aPath, $bPath);
            }

            return strcasecmp($this->externalResources[$aId]->getTitle(), $this->externalResources[$bId]->getTitle());
          default:
            return -1;
        }
        // no break
      case Contributor::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
          case LearningOutcome::class:
          case LearningPath::class:
          case ExternalResource::class:
            return 1;
          case Contributor::class:
            if ($aId === $bId) {
              return strcasecmp($aPath, $bPath);
            }

            return strcasecmp($this->contributors[$aId]->getName(), $this->contributors[$bId]->getName());
          default:
            return -1;
        }
        // no break
      default:
        return 1;
    }
  }
}

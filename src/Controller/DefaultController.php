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
use App\Request\Wrapper\RequestStudyArea;
use App\UrlUtils\Model\Url;
use App\UrlUtils\UrlChecker;
use App\UrlUtils\UrlScanner;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Cache\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
  /** @var Concept[] */
  private $concepts;

  /** @var Contributor[] */
  private $contributors;

  /** @var LearningOutcome[] */
  private $learningOutcomes;

  /** @var ExternalResource[] */
  private $externalResources;

  /** @var LearningPath[] */
  private $learningPaths;

  /**
   * @Route("/page/{_studyArea}/{pageUrl}", defaults={"_studyArea"=null, "pageUrl"=""},
   *                                        requirements={"_studyArea"="\d+", "pageUrl"=".+"}, name="_home",
   *                                        options={"expose"=true, "no_login_wrap"=true})
   * @Route("/page/{pageUrl}", defaults={"_studyArea"=null, "pageUrl"=""}, requirements={"pageUrl"=".+"},
   *                           name="_home_simple", options={"no_login_wrap"=true})
   * @Template("double_column.html.twig")
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param string           $pageUrl
   * @param RouterInterface  $router
   * @param Profiler|null    $profiler
   *
   * @return array|RedirectResponse
   */
  public function index(RequestStudyArea $requestStudyArea, string $pageUrl, RouterInterface $router, ?Profiler $profiler)
  {
    // Disable profiler on the home page
    if ($profiler) {
      $profiler->disable();
    }

    // Check for empty study area
    if (!$requestStudyArea->hasValue()) {
      // This means that there is no study area found for the user
      return $this->redirectToRoute('app_studyarea_add_first', ['_studyArea' => 0]);
    }

    // Retrieve actual study area from wrapper
    $studyArea = $requestStudyArea->getStudyArea();

    // Validate authentication
    if (!$this->isGranted('STUDYAREA_SHOW', $studyArea)) {
      // Forward to dashboard
      return $this->redirectToRoute('app_default_landing');
    }

    return [
        'studyArea' => $studyArea,
        'pageUrl'   => $pageUrl != ''
            ? '/' . $studyArea->getId() . '/' . $pageUrl
            : $router->generate('app_default_dashboard', ['_studyArea' => $studyArea->getId()]),
    ];
  }

  /**
   * @Route("/", options={"no_login_wrap"=true})
   * @Route("", name="base_url", options={"no_login_wrap"=true})
   * @Template()
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param Request              $request
   * @param FormFactoryInterface $formFactory
   * @param TranslatorInterface  $translator
   * @param StudyAreaRepository  $studyAreaRepository
   *
   * @return array|Response
   */
  public function landing(
      Request $request, FormFactoryInterface $formFactory, TranslatorInterface $translator,
      StudyAreaRepository $studyAreaRepository)
  {
    $user    = $this->getUser();
    $session = $request->getSession();
    assert($user === NULL || $user instanceof User);

    // When there is no user, render the login form
    if (!$user) {
      $loginForm = $this->createForm(LoginType::class, array(
          '_username' => $session->get(Security::LAST_USERNAME, ''),
      ), array(
          'action' => $this->generateUrl('login_check'),
      ));

      // Retrieve the error and remove it from the session
      if ($session->has(Security::AUTHENTICATION_ERROR)) {
        $authError = $session->get(Security::AUTHENTICATION_ERROR);
        $session->remove(Security::AUTHENTICATION_ERROR);

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
        ? $this->createStudyAreaForm($formFactory, $translator, $user, NULL, 'dashboard.open') : NULL;

    return [
        'loginForm'       => isset($loginForm) ? $loginForm->createView() : NULL,
        'loginFormActive' => $session->get(Security::LAST_USERNAME, '') !== '',
        'singleStudyArea' => count($studyAreas) === 1 ? reset($studyAreas) : NULL,
        'studyAreaCount'  => $studyAreaCount,
        'studyAreaForm'   => $studyAreaForm ? $studyAreaForm->createView() : NULL,
    ];
  }

  /**
   * @Route("/{_studyArea}/dashboard", requirements={"_studyArea"="\d+"})
   * @Route("/{_studyArea}/dashboard", requirements={"_studyArea"="\d+"}, name="app_studyarea_list")
   * @Template
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea           $requestStudyArea
   * @param FormFactoryInterface       $formFactory
   * @param StudyAreaRepository        $studyAreaRepository
   * @param ConceptRepository          $conceptRepo
   * @param ConceptRelationRepository  $conceptRelationRepo
   * @param ContributorRepository      $contributorRepository
   * @param AbbreviationRepository     $abbreviationRepository
   * @param ExternalResourceRepository $externalResourceRepo
   * @param LearningOutcomeRepository  $learningOutcomeRepo
   * @param LearningPathRepository     $learningPathRepo
   * @param UrlChecker                 $urlChecker
   * @param TranslatorInterface        $translator
   *
   * @return array
   *
   * @throws InvalidArgumentException
   * @throws NonUniqueResultException
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function dashboard(
      RequestStudyArea $requestStudyArea, FormFactoryInterface $formFactory, StudyAreaRepository $studyAreaRepository,
      ConceptRepository $conceptRepo, ConceptRelationRepository $conceptRelationRepo,
      ContributorRepository $contributorRepository, AbbreviationRepository $abbreviationRepository,
      ExternalResourceRepository $externalResourceRepo, LearningOutcomeRepository $learningOutcomeRepo,
      LearningPathRepository $learningPathRepo, UrlChecker $urlChecker, TranslatorInterface $translator)
  {
    $user       = $this->getUser();
    $studyArea  = $requestStudyArea->getStudyArea();
    $studyAreas = $studyAreaRepository->getVisible($this->getUser());

    // Only show switch form when there is more than 1 visible study area
    $studyAreaForm = count($studyAreas) > 1
        ? $this->createStudyAreaForm($formFactory, $translator, $user, $studyArea) : NULL;

    $conceptForm = $formFactory->createNamedBuilder('concept_form')
        ->add('concept', EntityType::class, [
            'placeholder'   => 'dashboard.select-one',
            'hide_label'    => true,
            'choice_label'  => 'name',
            'class'         => Concept::class,
            'select2'       => true,
            'query_builder' => function (ConceptRepository $conceptRepository) use ($studyArea) {
              return $conceptRepository->findForStudyAreaOrderByNameQb($studyArea);
            },
        ])
        ->add('submit', SubmitType::class, [
            'disabled' => true,
            'label'    => 'browser.search',
            'icon'     => 'fa-chevron-right',
            'attr'     => array(
                'class' => 'btn btn-outline-success',
            ),
        ])
        ->getForm();

    // Check for urls, if one has edit roles
    $urlData = [];
    if ($this->isGranted('STUDYAREA_EDIT', $studyArea)) {
      $urls        = $urlChecker->getUrlsForStudyArea($studyArea);
      $badUrls     = $urlChecker->checkStudyArea($studyArea);
      $urlsScanned = $urls !== NULL;

      $urlData = [
          'urlScanned'      => $urlsScanned,
          'urlScanProgress' => ($urlsScanned ? $urls['urls'] === NULL : false),
          'urlCount'        => ($urlsScanned ? count($urls['urls']) : -1),
          'brokenUrlCount'  => ($badUrls !== NULL ? count($badUrls['bad']) : -1),
      ];
    }

    return array_merge([
        'conceptForm'           => $conceptForm->createView(),
        'studyAreaForm'         => $studyAreaForm ? $studyAreaForm->createView() : NULL,
        'studyArea'             => $studyArea,
        'studyAreas'            => $studyAreas,
        'currentStudyArea'      => $requestStudyArea->getStudyArea(),
        'conceptCount'          => $conceptRepo->getCountForStudyArea($studyArea),
        'relationCount'         => $conceptRelationRepo->getCountForStudyArea($studyArea),
        'abbreviationCount'     => $abbreviationRepository->getCountForStudyArea($studyArea),
        'contributorCount'      => $contributorRepository->getCountForStudyArea($studyArea),
        'externalResourceCount' => $externalResourceRepo->getCountForStudyArea($studyArea),
        'learningOutcomeCount'  => $learningOutcomeRepo->getCountForStudyArea($studyArea),
        'learningPathCount'     => $learningPathRepo->getCountForStudyArea($studyArea),
    ], $urlData);
  }

  /**
   * @Route("/{_studyArea}/urls", requirements={"_studyArea"="\d+"})
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param RequestStudyArea           $requestStudyArea
   * @param UrlChecker                 $urlChecker
   * @param ConceptRepository          $conceptRepository
   * @param ContributorRepository      $contributorRepository
   * @param LearningOutcomeRepository  $learningOutcomeRepository
   * @param ExternalResourceRepository $externalResourceRepository
   * @param LearningPathRepository     $learningPathRepository
   *
   * @return array
   * @throws InvalidArgumentException
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function urlOverview(
      RequestStudyArea $requestStudyArea, UrlChecker $urlChecker, ConceptRepository $conceptRepository,
      ContributorRepository $contributorRepository, LearningOutcomeRepository $learningOutcomeRepository,
      ExternalResourceRepository $externalResourceRepository, LearningPathRepository $learningPathRepository)
  {
    $studyArea = $requestStudyArea->getStudyArea();
    $urls      = $urlChecker->getUrlsForStudyArea($studyArea);
    // Not scanned yet, early return
    if ($urls === NULL) return ['lastScanned' => NULL];
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
    list($badInternalUrls, $badExternalUrls) = $this->splitUrlLocation($badUrls['bad']);
    list($unscannedInternalUrls, $unscannedExternalUrls) = $this->splitUrlLocation($badUrls['unscanned']);
    list($goodInternalUrls, $goodExternalUrls) = $this->splitUrlLocation($goodUrls);

    return [
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
    ];
  }

  /**
   * @Route("/{_studyArea}/rescanurl/{url}", requirements={"_studyArea"="\d+"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param RequestStudyArea    $requestStudyArea
   * @param UrlChecker          $urlChecker
   * @param UrlScanner          $urlScanner
   * @param TranslatorInterface $translator
   * @param                     $url
   *
   * @return RedirectResponse
   * @throws InvalidArgumentException
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function urlRescan(RequestStudyArea $requestStudyArea, UrlChecker $urlChecker, UrlScanner $urlScanner, TranslatorInterface $translator, $url)
  {
    $url    = $urlScanner->scanText(sprintf('src="%s"', urldecode($url)));
    $result = $urlChecker->checkUrl($url[0], true, false) ?
        $translator->trans('url.good') :
        $translator->trans('url.bad');
    $this->addFlash('info', $translator->trans('url.rescanned', ['%result%' => strtolower($result)]));

    return $this->redirect($this->generateUrl('app_default_urloverview'));
  }

  /**
   * @Route("/{_studyArea}/rescanurls", requirements={"_studyArea"="\d+"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param RequestStudyArea    $requestStudyArea
   * @param UrlChecker          $urlChecker
   * @param TranslatorInterface $translator
   *
   * @return RedirectResponse
   * @throws InvalidArgumentException
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function urlRescanStudyArea(RequestStudyArea $requestStudyArea, UrlChecker $urlChecker, TranslatorInterface $translator)
  {
    $urlChecker->checkStudyArea($requestStudyArea->getStudyArea(), false, false);
    $this->addFlash('info', $translator->trans('url.rescanned-study-area'));

    return $this->redirect($this->generateUrl('app_default_urloverview'));
  }

  /**
   * @param FormFactoryInterface $formFactory
   * @param TranslatorInterface  $translator
   * @param User|null            $user
   * @param StudyArea|null       $studyArea
   * @param string               $buttonLabel
   *
   * @return FormInterface
   */
  private function createStudyAreaForm(
      FormFactoryInterface $formFactory, TranslatorInterface $translator,
      ?User $user, ?StudyArea $studyArea, string $buttonLabel = 'study-area.switch-to'): FormInterface
  {
    $defaultGroupName = $translator->trans('study-area.groups.default-name');

    return $formFactory->createNamedBuilder('studyarea_form')
        ->add('studyArea', EntityType::class, [
            'placeholder'   => 'dashboard.select-one',
            'hide_label'    => true,
            'class'         => StudyArea::class,
            'select2'       => true,
            'group_by'      => function (StudyArea $studyArea) use ($defaultGroupName) {
              if (!$studyArea->getGroup()) {
                return $defaultGroupName;
              }

              return $studyArea->getGroup()->getName();
            },
            'query_builder' => function (StudyAreaRepository $studyAreaRepository) use ($user, $studyArea) {
              $qb = $studyAreaRepository->getVisibleQueryBuilder($user);
              if ($studyArea !== NULL) {
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
            'attr'     => array(
                'class' => 'btn btn-outline-success',
            ),
        ])
        ->getForm();
  }

  /**
   * Filter an Url based on it being internal or not
   *
   * @param $entry
   *
   * @return bool
   */
  private function filterInternal(Url $entry): bool
  {
    return $entry->isInternal();
  }

  /**
   * Get the Id of an object if it exists.
   *
   * @param $entry
   *
   * @return int
   */
  private function findId($entry): int
  {
    return method_exists($entry, 'getId') ? $entry->getId() : -1;
  }

  /**
   * Add the Id of an array of objects as key, for easier searching of arrays.
   *
   * @param array $objects
   *
   * @return array
   */
  private function mapArrayById(array $objects): array
  {
    $objectIds = array_map([$this, 'findId'], $objects);

    return array_combine($objectIds, $objects);
  }

  /**
   * Sort an array of urls for viewing in the overview, then split them according to location.
   *
   * @param array|null $urls
   *
   * @return array
   */
  private function splitUrlLocation(?array $urls): array
  {
    usort($urls, [$this, 'sortUrls']);
    $internalUrls = array_filter($urls, [$this, 'filterInternal']);
    $externalUrls = array_diff($urls, $internalUrls);

    return array($internalUrls, $externalUrls);
  }

  /**
   * Sort two urls for in the overview page. First they are ordered based on class, then on entity name or number, then
   * on property name.
   *
   * @param $a
   * @param $b
   *
   * @return int
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
            if ($aId === $bId) return strcasecmp($aPath, $bPath);

            return strcasecmp($this->concepts[$aId]->getName(), $this->concepts[$bId]->getName());
          default:
            return -1;
        }
      case LearningOutcome::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
            return 1;
          case LearningOutcome::class:
            if ($aId === $bId) return strcasecmp($aPath, $bPath);

            return $this->learningOutcomes[$aId]->getNumber() <=> $this->learningOutcomes[$bId]->getNumber();
          default:
            return -1;
        }
      case LearningPath::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
          case LearningOutcome::class:
            return 1;
          case LearningPath::class:
            if ($aId === $bId) return strcasecmp($aPath, $bPath);

            return strcasecmp($this->learningPaths[$aId]->getName(), $this->learningPaths[$bId]->getName());
          default:
            return -1;
        }
      case ExternalResource::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
          case LearningOutcome::class:
          case LearningPath::class:
            return 1;
          case ExternalResource::class:
            if ($aId === $bId) return strcasecmp($aPath, $bPath);

            return strcasecmp($this->externalResources[$aId]->getTitle(), $this->externalResources[$bId]->getTitle());
          default:
            return -1;
        }
      case Contributor::class:
        switch ($bClass) {
          case StudyArea::class:
          case Concept::class:
          case LearningOutcome::class:
          case LearningPath::class:
          case ExternalResource::class:
            return 1;
          case Contributor::class:
            if ($aId === $bId) return strcasecmp($aPath, $bPath);

            return strcasecmp($this->contributors[$aId]->getName(), $this->contributors[$bId]->getName());
          default:
            return -1;
        }
      default:
        return 1;
    }
  }

}

<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Entity\StudyAreaFieldConfiguration;
use App\Entity\StudyAreaGroup;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\StudyArea\FieldConfigurationType;
use App\Form\StudyArea\TransferOwnerType;
use App\Form\StudyAreaGroup\StudyAreaGroupType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Naming\NamingService;
use App\Repository\PageLoadRepository;
use App\Repository\StudyAreaGroupRepository;
use App\Repository\TrackingEventRepository;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use App\Request\Wrapper\RequestStudyArea;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StudyAreaController.
 *
 * @author TobiasF
 *
 * @Route("/{_studyArea}/studyarea", requirements={"_studyArea"="\d+"})
 */
class StudyAreaController extends AbstractController
{
  /**
   * @Route("/add")
   * @Route("/add/first", defaults={"first" = true}, name="app_studyarea_add_first")
   *
   * @Template()
   *
   * @IsGranted("ROLE_USER")
   *
   * @param bool $first
   */
  public function add(Request $request, EntityManagerInterface $em, TranslatorInterface $trans, $first = false): array|Response
  {
    // Create new StudyArea
    $studyArea = (new StudyArea())->setOwner($this->getUser());
    if ($first) {
      $studyArea->setAccessType(StudyArea::ACCESS_PRIVATE);
    }

    $form = $this->createForm(EditStudyAreaType::class, $studyArea, [
      'studyArea'    => $studyArea,
      'select_owner' => false,
      'save_only'    => $first,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($studyArea);
      $em->flush();

      // Add default relation types
      for ($i = 1; $i <= 3; $i++) {
        $name         = $trans->trans('relation.default-' . $i);
        $relationType = (new RelationType())
          ->setStudyArea($studyArea)
          ->setName($name);

        $em->persist($relationType);
      }
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.saved', ['%item%' => $studyArea->getName()]));

      // Check for forward to home
      if ($first) {
        return $this->redirectToRoute('_home', ['_studyArea' => $studyArea->getId()]);
      }

      // Check for forward to dashboard (via list button)
      if (SaveType::isListClicked($form)) {
        // Load reloading page in order to switch to the new study area
        return $this->render('reloading_fullscreen.html.twig', [
          'reloadUrl' => $this->generateUrl('_home', ['_studyArea' => $studyArea->getId()]),
        ]);
      }

      return $this->redirectToRoute('app_studyarea_list');
    }

    $params = [
      'form'       => $form->createView(),
      'list_route' => 'app_studyarea_list',
    ];

    // Check for first
    if ($first) {
      return $this->render('study_area/add_first.html.twig', $params);
    }

    return $params;
  }

  /**
   * @Route("/group/add")
   *
   * @Template()
   *
   * @IsGranted("ROLE_SUPER_ADMIN")
   */
  public function addGroup(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): array|Response
  {
    // Create a new group
    $group = new StudyAreaGroup();
    $form  = $this->createForm(StudyAreaGroupType::class, $group, [
      'study_area_group' => $group,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the entity
      $em->persist($group);
      $em->flush();

      // Set message
      $this->addFlash('success', $translator->trans('study-area.groups.created', ['%item%' => $group->getName()]));

      // Forward to show
      return $this->redirectToRoute('app_studyarea_editgroup', ['group' => $group->getId()]);
    }

    return [
      'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{studyArea}", requirements={"studyArea"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @DenyOnFrozenStudyArea(route="app_default_dashboard", subject="studyArea")
   */
  public function edit(
    Request $request, StudyArea $studyArea, EntityManagerInterface $em, UserGroupRepository $userGroupRepo,
    PageLoadRepository $pageLoadRepository, TrackingEventRepository $trackingEventRepository, TranslatorInterface $trans): Response|array
  {
    // Check whether permissions flag is set
    $permissions = $request->query->get('permissions', false);

    // Create form and handle request
    $form = $this->createForm(EditStudyAreaType::class, $studyArea, [
      'studyArea'         => $studyArea,
      'select_owner'      => false,
      'save_and_list'     => !$permissions,
      'cancel_route_edit' => $permissions ? 'app_permissions_studyarea' : 'app_studyarea_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Check if permissions must be reset
      $userGroupRepo->removeObsoleteGroups($studyArea);

      // Remove tracking data if tracking is disabled
      if (!$studyArea->isTrackUsers()) {
        $trackingEventRepository->purgeForStudyArea($studyArea);
        $pageLoadRepository->purgeForStudyArea($studyArea);

        // Disable analytics dashboard if tracking is disabled
        $studyArea->setAnalyticsDashboardEnabled(false);
      }

      // Save the data
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.updated', ['%item%' => $studyArea->getName()]));

      // Check for forward to dashboard (via list button)
      if (!$permissions && SaveType::isListClicked($form)) {
        // Load reloading page in order to switch to the new study area
        return $this->render('reloading_fullscreen.html.twig', [
          'reloadUrl' => $this->generateUrl('_home', ['_studyArea' => $studyArea->getId()]),
        ]);
      }

      // Always return to list (or permissions page) as there is no show
      return $this->redirectToRoute($permissions ? 'app_permissions_studyarea' : 'app_studyarea_list');
    }

    return [
      'studyArea'       => $studyArea,
      'form'            => $form->createView(),
      'list_route'      => $permissions ? 'app_permissions_studyarea' : 'app_studyarea_list',
      'trackingEnabled' => $studyArea->isTrackUsers(),
    ];
  }

  /**
   * @Route("/group/{group}/edit", requirements={"group": "\d+"})
   *
   * @Template
   *
   * @IsGranted("ROLE_SUPER_ADMIN")
   */
  public function editGroup(Request $request, StudyAreaGroup $group, EntityManagerInterface $em, TranslatorInterface $translator): array|Response
  {
    $form = $this->createForm(StudyAreaGroupType::class, $group, [
      'study_area_group' => $group,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      // Set message
      $this->addFlash('success', $translator->trans('study-area.groups.updated', ['%item%' => $group->getName()]));

      // Forward to show
      return $this->redirectToRoute('app_studyarea_editgroup', ['group' => $group->getId()]);
    }

    return [
      'group' => $group,
      'form'  => $form->createView(),
    ];
  }

  /**
   * @Route("/fields")
   *
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @Template
   */
  public function fieldConfiguration(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $entityManager,
    TranslatorInterface $translator, NamingService $namingService): array|RedirectResponse
  {
    $studyAreaConfiguration = $requestStudyArea->getStudyArea()->getFieldConfiguration() ?: new StudyAreaFieldConfiguration();
    $form                   = $this->createForm(FieldConfigurationType::class, $studyAreaConfiguration)
      ->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $requestStudyArea->getStudyArea()->setFieldConfiguration($studyAreaConfiguration);
      $entityManager->flush();

      $namingService->clearCache();
      $this->addFlash('success', $translator->trans('study-area.field-configuration.updated'));

      return $this->redirectToRoute('app_studyarea_fieldconfiguration');
    }

    return [
      'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/freeze/{studyArea}", requirements={"studyArea"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @DenyOnFrozenStudyArea(route="app_default_dashboard", subject="studyArea")
   *
   * @throws Exception
   */
  public function freeze(Request $request, StudyArea $studyArea, EntityManagerInterface $em, TranslatorInterface $translator): array|Response
  {
    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'        => 'app_default_dashboard',
      'cancel_route_params' => ['studyArea' => $studyArea->getId()],
      'remove_label'        => 'form.confirm-freeze',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $studyArea->setFrozenOn(new DateTime());
      $em->flush();

      $this->addFlash('success', $translator->trans('study-area.freeze-succeeded', ['%item%' => $studyArea->getName()]));

      return $this->redirectToRoute('app_default_dashboard', ['studyArea' => $studyArea]);
    }

    return [
      'form'      => $form->createView(),
      'studyArea' => $studyArea,
    ];
  }

  /**
   * List the study area groups.
   *
   * @Route("/group/list")
   *
   * @Template()
   *
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @return array
   */
  public function listGroups(StudyAreaGroupRepository $repository)
  {
    return [
      'groups' => $repository->findAll(),
    ];
  }

  /**
   * @Route("/remove/{studyArea}", requirements={"studyArea"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, StudyArea $studyArea,
    EntityManagerInterface $em, TranslatorInterface $trans): array|Response
  {
    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'        => 'app_default_dashboard',
      'cancel_route_params' => ['studyArea' => $studyArea->getId()],
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $em->remove($studyArea);
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.removed', ['%item%' => $studyArea->getName()]));

      if ($requestStudyArea->getStudyArea()->getId() == $studyArea->getId()) {
        return $this->render('reloading_fullscreen.html.twig', [
          'reloadUrl' => $this->generateUrl('app_default_landing'),
        ]);
      }

      return $this->redirectToRoute('app_studyarea_list');
    }

    return [
      'form'      => $form->createView(),
      'studyArea' => $studyArea,
    ];
  }

  /**
   * @Route("/group/{group}/remove", requirements={"group":"\d+"})
   *
   * @Template()
   *
   * @IsGranted("ROLE_SUPER_ADMIN")
   */
  public function removeGroup(
    Request $request, StudyAreaGroup $group, EntityManagerInterface $em, TranslatorInterface $translator): array|Response
  {
    // Do not allow removal when it still contains study areas
    if ($group->studyAreaCount() > 0) {
      $this->addFlash('warning', $translator->trans('study-area.groups.remove-not-possible', [
        '%item%' => $group->getName(),
      ]));

      return $this->redirectToRoute('app_studyarea_listgroups');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_studyarea_listgroups',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $em->remove($group);
      $em->flush();

      $this->addFlash('success', $translator->trans('study-area.groups.removed', ['%item%' => $group->getName()]));

      return $this->redirectToRoute('app_studyarea_listgroups');
    }

    return [
      'group' => $group,
      'form'  => $form->createView(),
    ];
  }

  /**
   * @Route("/transfer/{studyArea}", requirements={"studyArea"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   */
  public function transferOwner(
    Request $request, RequestStudyArea $requestStudyArea, StudyArea $studyArea, EntityManagerInterface $em,
    TranslatorInterface $trans, UserRepository $userRepository): array|Response
  {
    $form = $this->createForm(TransferOwnerType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Find the user for the submitted email
      $user = $userRepository->getUserForEmail($form->getData()['new_owner']);
      if ($user) {
        $studyArea->setOwner($user);
        $em->flush();

        $this->addFlash('success', $trans->trans('study-area.owner-transferred', [
          '%item%'  => $studyArea->getName(),
          '%owner%' => $studyArea->getOwner()->getFullName(),
        ]));

        // Check whether this area is still visible
        if ($requestStudyArea->getStudyArea()->getId() == $studyArea->getId()
            && !$this->isGranted('STUDYAREA_SHOW', $studyArea)) {
          return $this->render('reloading_fullscreen.html.twig', [
            'reloadUrl' => $this->generateUrl('app_default_landing'),
          ]);
        }

        return $this->redirectToRoute('app_studyarea_list');
      }

      // New owner not found
      $this->addFlash('error', $trans->trans('study-area.new-owner-not-found'));
    }

    return [
      'studyArea' => $studyArea,
      'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/unfreeze/{studyArea}", requirements={"studyArea"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   */
  public function unfreeze(StudyArea $studyArea, Request $request, TranslatorInterface $translator, EntityManagerInterface $em): array|Response
  {
    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'        => 'app_default_dashboard',
      'cancel_route_params' => ['studyArea' => $studyArea->getId()],
      'remove_label'        => 'form.confirm-unfreeze',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $studyArea->setFrozenOn(null);
      $em->flush();

      $this->addFlash('success', $translator->trans('study-area.unfreeze-succeeded', ['%item%' => $studyArea->getName()]));

      return $this->redirectToRoute('app_default_dashboard', ['studyArea' => $studyArea]);
    }

    return [
      'form'      => $form->createView(),
      'studyArea' => $studyArea,
    ];
  }
}

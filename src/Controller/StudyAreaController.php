<?php

namespace App\Controller;

use App\Entity\RelationType;
use App\Entity\StudyArea;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\StudyArea\TransferOwnerType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\StudyAreaRepository;
use App\Repository\UserGroupRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class StudyAreaController
 *
 * @author TobiasF
 *
 * @Route("/{_studyArea}/studyarea", requirements={"_studyArea"="\d+"})
 */
class StudyAreaController extends Controller
{
  /**
   * @Route("/add")
   * @Route("/add/first", defaults={"first" = true}, name="app_studyarea_add_first")
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @param Request                $request
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   * @param bool                   $first
   *
   * @return array|Response
   */
  public function add(Request $request, EntityManagerInterface $em, TranslatorInterface $trans, $first = false)
  {
    // Create new StudyArea
    $studyArea = (new StudyArea())->setOwner($this->getUser());
    if ($first) $studyArea->setAccessType(StudyArea::ACCESS_PRIVATE);

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

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_studyarea_list');
      }

      // Load reloading page in order to switch to the new study area
      return $this->render('reloading_fullscreen.html.twig', [
          'reloadUrl' => $this->generateUrl('_home', ['_studyArea' => $studyArea->getId()]),
      ]);
    }

    $params = [
        'form' => $form->createView(),
    ];

    // Check for first
    if ($first) {
      return $this->render('study_area/add_first.html.twig', $params);
    }

    return $params;
  }

  /**
   * @Route("/edit/{studyArea}", requirements={"studyArea"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @param Request                $request
   * @param StudyArea              $studyArea
   * @param EntityManagerInterface $em
   * @param UserGroupRepository    $userGroupRepo
   * @param TranslatorInterface    $trans
   *
   * @return Response|array
   */
  public function edit(Request $request, StudyArea $studyArea, EntityManagerInterface $em, UserGroupRepository $userGroupRepo, TranslatorInterface $trans)
  {

    // Check whether permissions flag is set
    $permissions = $request->query->get('permissions', false);

    // Create form and handle request
    $form = $this->createForm(EditStudyAreaType::class, $studyArea, [
        'studyArea'         => $studyArea,
        'select_owner'      => false,
        'save_and_list'     => !$permissions,
        'cancel_route_edit' => $permissions ? 'app_permissions_studyarea' : 'app_studyarea_list',
        'list_route'        => $permissions ? 'app_permissions_studyarea' : 'app_studyarea_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Check if permissions must be reset
      $userGroupRepo->removeObsoleteGroups($studyArea);

      // Save the data
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.updated', ['%item%' => $studyArea->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_studyarea_list');
      }

      // Forward to show
      return $this->redirectToRoute($permissions ? 'app_permissions_studyarea' : 'app_default_dashboard',
          $permissions ? ['studyArea' => $studyArea->getId()] : []);
    }

    return [
        'studyArea' => $studyArea,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/remove/{studyArea}", requirements={"studyArea"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param StudyArea              $studyArea
   * @param StudyAreaRepository    $studyAreaRepository
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, StudyArea $studyArea, StudyAreaRepository $studyAreaRepository,
                         EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if this is the only study area
    if ($studyAreaRepository->getOwnerAmount($this->getUser()) == 1) {
      $this->addFlash('warning', $trans->trans('study-area.owner-last-remove'));

      return $this->redirectToRoute('app_studyarea_list');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
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
            'reloadUrl' => $this->generateUrl('app_default_index'),
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
   * @Route("/transfer/{studyArea}", requirements={"studyArea"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @param Request                $request
   * @param StudyArea              $studyArea
   * @param StudyAreaRepository    $studyAreaRepository
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   *
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function transferOwner(Request $request, StudyArea $studyArea, StudyAreaRepository $studyAreaRepository,
                                EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if this is the only study area
    if ($studyAreaRepository->getOwnerAmount($this->getUser()) == 1) {
      $this->addFlash('warning', $trans->trans('study-area.owner-last-transfer'));

      return $this->redirectToRoute('app_studyarea_list');
    }

    $form = $this->createForm(TransferOwnerType::class, $studyArea, [
        'current_owner' => $studyArea->getOwner(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.owner-transferred', [
          '%item%'  => $studyArea->getName(),
          '%owner%' => $studyArea->getOwner()->getFullName(),
      ]));

      return $this->redirectToRoute('app_studyarea_list');
    }

    return [
        'studyArea' => $studyArea,
        'form'      => $form->createView(),
    ];
  }

}

<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Contributor;
use App\Entity\PendingChange;
use App\Form\Contributor\EditContributorType;
use App\Form\Type\RemoveType;
use App\Repository\ContributorRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContributorController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/contributor", requirements={"_studyArea"="\d+"})
 */
class ContributorController extends AbstractController
{

  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_contributor_list", subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|Response
   */
  public function add(
      Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $contributor = (new Contributor())->setStudyArea($studyArea);
    $snapshot    = $reviewService->getSnapshot($contributor);

    $form = $this->createForm(EditContributorType::class, $contributor, ['studyArea' => $studyArea]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $contributor, PendingChange::CHANGE_TYPE_ADD, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('contributor.saved', ['%item%' => $contributor->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_contributor_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{contributor}", requirements={"contributor"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_contributor_list", subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param Contributor         $contributor
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, Contributor $contributor,
      ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($contributor->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$reviewService->canObjectBeEdited($studyArea, $contributor)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
          '%item%' => $trans->trans('contributor._name'),
      ]));

      return $this->redirectToRoute('app_contributor_list');
    }

    // Create snapshot
    $snapshot = $reviewService->getSnapshot($contributor);

    // Create form and handle request
    $form = $this->createForm(EditContributorType::class, $contributor, [
        'studyArea'       => $studyArea,
        'disabled_fields' => $reviewService->getDisabledFieldsForObject($studyArea, $contributor),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $contributor, PendingChange::CHANGE_TYPE_EDIT, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('contributor.updated', ['%item%' => $contributor->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_contributor_list');
    }

    return [
        'contributor' => $contributor,
        'form'        => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea      $requestStudyArea
   * @param ContributorRepository $repo
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, ContributorRepository $repo)
  {
    return [
        'studyArea'    => $requestStudyArea->getStudyArea(),
        'contributors' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{contributor}", requirements={"contributor"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_contributor_list", subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param Contributor         $contributor
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|Response
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, Contributor $contributor,
      ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($contributor->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be deleted
    if (!$reviewService->canObjectBeRemoved($studyArea, $contributor)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
          '%item%' => $trans->trans('contributor._name'),
      ]));

      return $this->redirectToRoute('app_contributor_list');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_contributor_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $contributor, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('contributor.removed', ['%item%' => $contributor->getName()]));

      return $this->redirectToRoute('app_contributor_list');
    }

    return [
        'contributor' => $contributor,
        'form'        => $form->createView(),
    ];
  }

}

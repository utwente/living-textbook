<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Entity\Contributor;
use App\Entity\PendingChange;
use App\Form\Contributor\EditContributorType;
use App\Form\Type\RemoveType;
use App\Repository\ContributorRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use App\Security\Voters\StudyAreaVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_studyArea<\d+>}/contributor')]
class ContributorController extends AbstractController
{
  #[Route('/add')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_contributor_add', subject: 'requestStudyArea')]
  public function add(
    Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $contributor = new Contributor()->setStudyArea($studyArea);
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

    return $this->render('contributor/add.html.twig', [
      'contributor' => $contributor,
      'form'        => $form,
    ]);
  }

  #[Route('/edit/{contributor<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_contributor_list', subject: 'requestStudyArea')]
  public function edit(
    Request $request, RequestStudyArea $requestStudyArea, Contributor $contributor,
    ReviewService $reviewService, TranslatorInterface $trans): Response
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
      'studyArea'           => $studyArea,
      'pending_change_info' => $reviewService->getPendingChangeObjectInformation($studyArea, $contributor),
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

    return $this->render('contributor/edit.html.twig', [
      'contributor' => $contributor,
      'form'        => $form,
    ]);
  }

  #[Route('/list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea, ContributorRepository $repo): Response
  {
    return $this->render('contributor/list.html.twig', [
      'studyArea'    => $requestStudyArea->getStudyArea(),
      'contributors' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ]);
  }

  #[Route('/remove/{contributor<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_contributor_list', subject: 'requestStudyArea')]
  public function remove(
    Request $request, RequestStudyArea $requestStudyArea, Contributor $contributor,
    ReviewService $reviewService, TranslatorInterface $trans): Response
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

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_contributor_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $contributor, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('contributor.removed', ['%item%' => $contributor->getName()]));

      return $this->redirectToRoute('app_contributor_list');
    }

    return $this->render('contributor/remove.html.twig', [
      'contributor' => $contributor,
      'form'        => $form,
    ]);
  }
}

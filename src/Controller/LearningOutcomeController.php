<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Entity\LearningOutcome;
use App\Entity\PendingChange;
use App\Form\LearningOutcome\EditLearningOutcomeType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Naming\NamingService;
use App\Repository\LearningOutcomeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use App\Security\Voters\StudyAreaVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_studyArea<\d+>}/learningoutcome')]
class LearningOutcomeController extends AbstractController
{
  #[Route('/add')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_learningoutcome_list', subject: 'requestStudyArea')]
  public function add(
    Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans,
    NamingService $namingService): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $learningOutcome = (new LearningOutcome())->setStudyArea($studyArea);
    $snapshot        = $reviewService->getSnapshot($learningOutcome);

    $form = $this->createForm(EditLearningOutcomeType::class, $learningOutcome, [
      'studyArea'       => $studyArea,
      'learningOutcome' => $learningOutcome,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $learningOutcome, PendingChange::CHANGE_TYPE_ADD, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('learning-outcome.saved', [
        '%item%'     => $learningOutcome->getShortName(),
        '%singular%' => ucfirst($namingService->get()->learningOutcome()->obj()),
      ]));

      if (!$learningOutcome->getId() || SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningoutcome_list');
      }

      return $this->redirectToRoute('app_learningoutcome_show', ['learningOutcome' => $learningOutcome->getId()]);
    }

    return $this->render('learning_outcome/add.html.twig', [
      'learningOutcome' => $learningOutcome,
      'form'            => $form,
    ]);
  }

  #[Route('/edit/{learningOutcome<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_learningoutcome_show', routeParams: ['learningOutcome' => '{learningOutcome}'], subject: 'requestStudyArea')]
  public function edit(
    Request $request, RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome,
    ReviewService $reviewService, TranslatorInterface $trans, NamingService $namingService): Response
  {
    // Check if correct study area
    $studyArea = $requestStudyArea->getStudyArea();
    if ($learningOutcome->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$reviewService->canObjectBeEdited($studyArea, $learningOutcome)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
        '%item%' => ucfirst($namingService->get()->learningOutcome()->obj()),
      ]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    // Create snapshot
    $snapshot = $reviewService->getSnapshot($learningOutcome);

    // Create form and handle request
    $form = $this->createForm(EditLearningOutcomeType::class, $learningOutcome, [
      'studyArea'           => $studyArea,
      'learningOutcome'     => $learningOutcome,
      'pending_change_info' => $reviewService->getPendingChangeObjectInformation($studyArea, $learningOutcome),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $learningOutcome, PendingChange::CHANGE_TYPE_EDIT, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('learning-outcome.updated', [
        '%item%'     => $learningOutcome->getShortName(),
        '%singular%' => ucfirst($namingService->get()->learningOutcome()->obj()),
      ]));

      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningoutcome_list');
      }

      return $this->redirectToRoute('app_learningoutcome_show', ['learningOutcome' => $learningOutcome->getId()]);
    }

    return $this->render('learning_outcome/edit.html.twig', [
      'learningOutcome' => $learningOutcome,
      'form'            => $form,
    ]);
  }

  #[Route('/list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea, LearningOutcomeRepository $repo): Response
  {
    return $this->render('learning_outcome/list.html.twig', [
      'studyArea'        => $requestStudyArea->getStudyArea(),
      'learningOutcomes' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ]);
  }

  #[Route('/remove/{learningOutcome<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_learningoutcome_show', routeParams: ['learningOutcome' => '{learningOutcome}'], subject: 'requestStudyArea')]
  public function remove(
    Request $request, RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome,
    ReviewService $reviewService, TranslatorInterface $trans, NamingService $namingService): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($learningOutcome->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be deleted
    if (!$reviewService->canObjectBeRemoved($studyArea, $learningOutcome)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
        '%item%' => ucfirst($namingService->get()->learningOutcome()->obj()),
      ]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'        => 'app_learningoutcome_show',
      'cancel_route_params' => ['learningOutcome' => $learningOutcome->getId()],
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Remove it
      $reviewService->storeChange($studyArea, $learningOutcome, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('learning-outcome.removed', [
        '%item%'     => $learningOutcome->getShortName(),
        '%singular%' => ucfirst($namingService->get()->learningOutcome()->obj()),
      ]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    return $this->render('learning_outcome/remove.html.twig', [
      'learningOutcome' => $learningOutcome,
      'form'            => $form,
    ]);
  }

  #[Route('/remove/unused')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_learningoutcome_list', subject: 'requestStudyArea')]
  public function removeUnused(
    Request $request, RequestStudyArea $requestStudyArea, LearningOutcomeRepository $learningOutcomeRepository,
    ReviewService $reviewService, TranslatorInterface $trans, NamingService $namingService): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    $unusedLearningOutcomes = $learningOutcomeRepository->findUnusedInStudyArea($studyArea);
    if (count($unusedLearningOutcomes) === 0) {
      $this->addFlash('info', $trans->trans('learning-outcome.no-unused', [
        '%plural%' => $namingService->get()->learningOutcome()->objs(),
      ]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_learningoutcome_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      foreach ($unusedLearningOutcomes as $unusedLearningOutcome) {
        // Verify it can be deleted
        if (!$reviewService->canObjectBeRemoved($studyArea, $unusedLearningOutcome)) {
          $this->addFlash('error', $trans->trans('review.remove-not-possible', [
            '%item%' => sprintf('%s "%s"',
              ucfirst($namingService->get()->learningOutcome()->obj()), $unusedLearningOutcome->getName()),
          ]));

          // Only add warning, but continue with the others
          continue;
        }

        // Remove it
        $reviewService->storeChange($studyArea, $unusedLearningOutcome, PendingChange::CHANGE_TYPE_REMOVE);
      }

      $this->addFlash('success', $trans->trans('learning-outcome.unused-removed', [
        '%plural%' => $namingService->get()->learningOutcome()->objs(),
      ]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    return $this->render('learning_outcome/remove_unused.html.twig', [
      'unusedLearningOutcome' => $unusedLearningOutcomes,
      'form'                  => $form,
    ]);
  }

  #[Route('/show/{learningOutcome<\d+>}')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function show(RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome): Response
  {
    // Check if correct study area
    if ($learningOutcome->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    return $this->render('learning_outcome/show.html.twig', [
      'learningOutcome' => $learningOutcome,
    ]);
  }
}

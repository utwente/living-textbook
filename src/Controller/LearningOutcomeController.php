<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\LearningOutcome;
use App\Entity\PendingChange;
use App\Form\LearningOutcome\EditLearningOutcomeType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Naming\NamingService;
use App\Repository\LearningOutcomeRepository;
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
 * Class LearningOutcomeController.
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/learningoutcome", requirements={"_studyArea"="\d+"})
 */
class LearningOutcomeController extends AbstractController
{
  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningoutcome_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function add(
      Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans,
      NamingService $namingService)
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

    return [
        'learningOutcome' => $learningOutcome,
        'form'            => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{learningOutcome}", requirements={"learningOutcome"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningoutcome_show", routeParams={"learningOutcome"="{learningOutcome}"},
   *                                                          subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome,
      ReviewService $reviewService, TranslatorInterface $trans, NamingService $namingService)
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

    return [
        'learningOutcome' => $learningOutcome,
        'form'            => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, LearningOutcomeRepository $repo)
  {
    return [
        'studyArea'        => $requestStudyArea->getStudyArea(),
        'learningOutcomes' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{learningOutcome}", requirements={"learningOutcome"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningoutcome_show",
   *   routeParams={"learningOutcome"="{learningOutcome}"}, subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome,
      ReviewService $reviewService, TranslatorInterface $trans, NamingService $namingService)
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

    return [
        'learningOutcome' => $learningOutcome,
        'form'            => $form->createView(),
    ];
  }

  /**
   * @Route("/remove/unused")
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningoutcome_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function removeUnused(
      Request $request, RequestStudyArea $requestStudyArea, LearningOutcomeRepository $learningOutcomeRepository,
      ReviewService $reviewService, TranslatorInterface $trans, NamingService $namingService)
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

    return [
        'unusedLearningOutcome' => $unusedLearningOutcomes,
        'form'                  => $form->createView(),
    ];
  }

  /**
   * @Route("/show/{learningOutcome}", requirements={"learningOurcome"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return array
   */
  public function show(RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome)
  {
    // Check if correct study area
    if ($learningOutcome->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    return [
        'learningOutcome' => $learningOutcome,
    ];
  }
}

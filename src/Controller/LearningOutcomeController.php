<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\LearningOutcome;
use App\Entity\PendingChange;
use App\Form\LearningOutcome\EditLearningOutcomeType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
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
 * Class LearningOutcomeController
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
      $this->addFlash('success', $trans->trans('learning-outcome.saved', ['%item%' => $learningOutcome->getShortName()]));

      if (!$learningOutcome->getId() || SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningoutcome_list');
      }

      return $this->redirectToRoute('app_learningoutcome_show', ['learningOutcome' => $learningOutcome->getId()]);
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{learningOutcome}", requirements={"learningOutcome"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningoutcome_show", routeParams={"learningOutcome"="{learningOutcome}"},
   *                                                          subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param LearningOutcome     $learningOutcome
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome,
      ReviewService $reviewService, TranslatorInterface $trans)
  {
    // Check if correct study area
    $studyArea = $requestStudyArea->getStudyArea();
    if ($learningOutcome->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$reviewService->canObjectBeEdited($studyArea, $learningOutcome)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
          '%item%' => $trans->trans('learning-outcome._name'),
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
      $this->addFlash('success', $trans->trans('learning-outcome.updated', ['%item%' => $learningOutcome->getShortName()]));

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
   * @param RequestStudyArea          $requestStudyArea
   * @param LearningOutcomeRepository $repo
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
   * @DenyOnFrozenStudyArea(route="app_learningoutcome_show", routeParams={"learningOutcome"="{learningOutcome}"},
   *                                                          subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param LearningOutcome     $learningOutcome
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|Response
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome,
      ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($learningOutcome->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be deleted
    if (!$reviewService->canObjectBeRemoved($studyArea, $learningOutcome)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
          '%item%' => $trans->trans('learning-outcome._name'),
      ]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route'        => 'app_learningoutcome_show',
        'cancel_route_params' => ['learningOutcome' => $learningOutcome->getId()],
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Remove it
      $reviewService->storeChange($studyArea, $learningOutcome, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('learning-outcome.removed', ['%item%' => $learningOutcome->getShortName()]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    return [
        'learningOutcome' => $learningOutcome,
        'form'            => $form->createView(),
    ];
  }

  /**
   * @Route("/show/{learningOutcome}", requirements={"learningOurcome"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param LearningOutcome  $learningOutcome
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

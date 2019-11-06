<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Form\Review\SubmitReviewType;
use App\Repository\PendingChangeRepository;
use App\Repository\ReviewRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ReviewController
 *
 * @Route("/{_studyArea}/review", requirements={"_studyArea"="\d+"})
 */
class ReviewController extends AbstractController
{

  /**
   * Show the pending reviews for the current user
   *
   * @Route("/submissions")
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @Template
   *
   * @param RequestStudyArea $requestStudyArea
   * @param ReviewRepository $reviewRepository
   *
   * @return array|Response
   */
  public function submissions(RequestStudyArea $requestStudyArea, ReviewRepository $reviewRepository)
  {
    $this->isReviewable($requestStudyArea);

    return [
        'reviews' => $reviewRepository->findBy(
            ['studyArea' => $requestStudyArea->getStudyArea()],
            ['requestedReviewAt' => 'DESC']),
    ];
  }

  /**
   * Shows the pending changes of the current user which haven't been submitted for review.
   *
   * @Route("/submit")
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @Template()
   *
   * @param Request                 $request
   * @param RequestStudyArea        $requestStudyArea
   * @param PendingChangeRepository $pendingChangeRepository
   * @param ReviewService           $reviewService
   * @param TranslatorInterface     $translator
   *
   * @return array|Response
   */
  public function submit(
      Request $request, RequestStudyArea $requestStudyArea, PendingChangeRepository $pendingChangeRepository,
      ReviewService $reviewService, TranslatorInterface $translator)
  {
    $this->isReviewable($requestStudyArea);

    $studyArea = $requestStudyArea->getStudyArea();
    $user      = $this->getUser();
    assert($user instanceof User);

    $pendingChanges = $pendingChangeRepository->getSubmittableForUser($studyArea, $user);

    $form = $this->createForm(SubmitReviewType::class, NULL, [
        'study_area' => $studyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $formData = $form->getData();

      // Retrieve the marked changes, and parse the data so that it can be handled by the service
      $markedChanges = [];
      foreach ($formData['pending_changes'] as $key => $markedChangeFields) {
        if (!is_numeric($key)) {
          continue;
        }
        $markedChanges[$key] = array_keys($markedChangeFields);
      }

      if (0 === count($markedChanges)) {
        $this->addFlash('warning', $translator->trans('review.nothing-selected-for-submit'));

        return [
            'form'           => $form->createView(),
            'pendingChanges' => $pendingChanges,
        ];
      }

      // Retrieve the form data
      $reviewer = $formData['reviewer'];
      $notes    = $formData['notes'];

      // Create the review
      $reviewService->createReview($studyArea, $markedChanges, $reviewer, $notes);

      $this->addFlash('success', $translator->trans('review.submitted'));

      return $this->redirectToRoute('app_review_submissions');
    }

    return [
        'form'           => $form->createView(),
        'pendingChanges' => $pendingChanges,
    ];
  }

  /**
   * Verify whether the study area has access to review mode.
   *
   * @param StudyArea|RequestStudyArea $studyArea
   */
  private function isReviewable($studyArea)
  {
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    if (!$studyArea->isReviewModeEnabled()) {
      throw new NotFoundHttpException();
    }
  }
}

<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Communication\Notification\ReviewNotificationService;
use App\Entity\Review;
use App\Entity\StudyArea;
use App\Entity\User;
use App\Form\Review\EditReviewType;
use App\Form\Review\ReviewSubmissionType;
use App\Form\Review\SubmitReviewType;
use App\Form\Type\RemoveType;
use App\Repository\PendingChangeRepository;
use App\Repository\ReviewRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Class ReviewController.
 *
 * @Route("/{_studyArea}/review", requirements={"_studyArea"="\d+"})
 */
class ReviewController extends AbstractController
{
  /**
   * Edit the pending review. It is only possible to edit the notes and requested reviewer.
   *
   * @Route("/{review}/edit", requirements={"review"="\d+"})
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @Template()
   */
  public function editReview(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    TranslatorInterface $translator): array|Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    $this->checkAccess($studyArea, $review);

    // Create the form
    $form = $this->createForm(EditReviewType::class, $review, [
      'study_area' => $studyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      $this->addFlash('success', $translator->trans('review.edit-successful'));

      return $this->redirectToRoute('app_review_submissions');
    }

    // Create form, although this is only show. This way, we can reuse the show logic from the review process
    $changesForm = $this->createForm(ReviewSubmissionType::class, $review, [
      'review'        => false,
      'show_comments' => $review->getReviewedAt() !== null,
    ]);

    return [
      'form'        => $form->createView(),
      'changesForm' => $changesForm->createView(),
      'review'      => $review,
    ];
  }

  /**
   * Publish reviews after review approval.
   *
   * @Route("/publish")
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @DenyOnFrozenStudyArea(route="app_default_dashboard", subject="requestStudyArea")
   *
   * @return array
   */
  public function publish(RequestStudyArea $requestStudyArea, ReviewRepository $reviewRepository)
  {
    return [
      'reviews' => $reviewRepository->getApproved($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * Publish the clicked review.
   *
   * @Route("/{review}/publish", requirements={"review"="\d+"})
   *
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @Template()
   *
   * @throws ORMException
   * @throws Throwable
   */
  public function publishReview(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, ReviewService $reviewService,
    TranslatorInterface $translator): array|Response
  {
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Create the form
    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'       => 'app_review_publish',
      'remove_label'       => 'review.publish',
      'remove_btn_variant' => 'outline-success',
    ]);
    $form->handleRequest($request);

    // Create an extra form. This way, we can reuse the show logic from the review process
    $submissionForm = $this->createForm(ReviewSubmissionType::class, $review, [
      'review' => false,
    ]);

    if ($form->isSubmitted() && $form->isValid()) {
      $reviewService->publishReview($review);

      $this->addFlash('success', $translator->trans('review.publish-successful'));

      return $this->redirectToRoute('app_review_publish');
    }

    return [
      'form'            => $form->createView(),
      'submission_form' => $submissionForm->createView(),
      'review'          => $review,
    ];
  }

  /**
   * Remove the pending review.
   *
   * @Route("/{review}/remove", requirements={"review"="\d+"})
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @Template()
   */
  public function removeReview(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    TranslatorInterface $translator): array|Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    $this->checkAccess($studyArea, $review);

    // Create the form
    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_review_submissions',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && RemoveType::isRemoveClicked($form)) {
      $em->remove($review);
      $em->flush();

      $this->addFlash('success', $translator->trans('review.remove-successful'));

      return $this->redirectToRoute('app_review_submissions');
    }

    return [
      'form'   => $form->createView(),
      'review' => $review,
    ];
  }

  /**
   * Resubmits the review.
   *
   * @Route("/{review}/resubmit", requirements={"review"="\d+"})
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @Template()
   *
   * @throws TransportExceptionInterface
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function resubmitSubmission(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    ReviewNotificationService $reviewNotificationService, TranslatorInterface $translator): array|Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    $this->isReviewable($studyArea);
    $this->checkAccess($studyArea, $review);

    $user = $this->getUser();
    assert($user instanceof User);

    $form = $this->createForm(EditReviewType::class, $review, [
      'save_label' => 'review.resubmit-review',
      'study_area' => $studyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Reset review state
      $review
        ->setRequestedReviewAt(new DateTime())
        ->setReviewedAt(null)
        ->setReviewedBy(null);

      // Store the data
      $em->flush();

      $reviewNotificationService->reviewRequested($review);

      $this->addFlash('success', $translator->trans('review.resubmitted'));

      return $this->redirectToRoute('app_review_submissions');
    }

    return [
      'form' => $form->createView(),
    ];
  }

  /**
   * Review a submission.
   *
   * @Route("/{review}", requirements={"review"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_REVIEW", subject="requestStudyArea")
   *
   * @throws TransportExceptionInterface
   *
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function reviewSubmission(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    TranslatorInterface $translator, ReviewNotificationService $reviewNotificationService): array|Response
  {
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Check if not yet reviewed
    if ($review->getReviewedAt() !== null) {
      throw new NotFoundHttpException('Requested review has already been reviewed');
    }

    // Check if not yet approved
    if ($review->getApprovedAt() !== null) {
      throw new NotFoundHttpException('Requested review has already been approved');
    }

    // Create form
    $form = $this->createForm(ReviewSubmissionType::class, $review);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $now  = new DateTime();
      $user = $this->getUser();
      assert($user instanceof User);

      $review
        ->setReviewedBy($user)
        ->setReviewedAt($now);

      $hasComments = $review->hasComments();
      if (!$hasComments) {
        // Set as approved
        $review
          ->setApprovedAt($now)
          ->setApprovedBy($user);
      }

      $em->flush();

      if ($hasComments) {
        $reviewNotificationService
          ->submissionDenied($review);
      } else {
        $reviewNotificationService
          ->submissionApproved($review);
      }

      $this->addFlash('success', $hasComments
          ? $translator->trans('review.review-comments')
          : $translator->trans('review.review-approved'));

      return $this->redirectToRoute('app_review_submissions');
    }

    return [
      'form'   => $form->createView(),
      'review' => $review,
    ];
  }

  /**
   * Show a submission.
   *
   * @Route("/{review}/show", requirements={"review"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_REVIEW", subject="requestStudyArea")
   */
  public function showSubmission(RequestStudyArea $requestStudyArea, Review $review): array|Response
  {
    // Check study area
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Create form, although this is only show. This way, we can reuse the show logic from the review process
    $form = $this->createForm(ReviewSubmissionType::class, $review, [
      'review'        => false,
      'show_comments' => $review->getReviewedAt() !== null,
    ]);

    return [
      'form'   => $form->createView(),
      'review' => $review,
    ];
  }

  /**
   * Show the pending reviews for the current user.
   *
   * @Route("/submissions")
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @Template
   */
  public function submissions(RequestStudyArea $requestStudyArea, ReviewRepository $reviewRepository): array|Response
  {
    $this->isReviewable($requestStudyArea);

    return [
      'reviews' => $reviewRepository->getSubmissions($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * Shows the pending changes of the current user which haven't been submitted for review.
   *
   * @Route("/submit")
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @Template()
   */
  public function submit(
    Request $request, RequestStudyArea $requestStudyArea, PendingChangeRepository $pendingChangeRepository,
    ReviewService $reviewService, TranslatorInterface $translator): array|Response
  {
    $this->isReviewable($requestStudyArea);

    $studyArea = $requestStudyArea->getStudyArea();
    $user      = $this->getUser();
    assert($user instanceof User);

    $pendingChanges = $pendingChangeRepository->getSubmittableForUser($studyArea, $user);

    // Create review object for display
    $review = new Review();
    foreach ($pendingChanges as $pendingChange) {
      $review->addPendingChange($pendingChange);
    }

    $form = $this->createForm(SubmitReviewType::class, null, [
      'study_area' => $studyArea,
      'review'     => $review,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $formData = $form->getData();

      // Clear review object from the pending change
      foreach ($pendingChanges as $pendingChange) {
        $pendingChange->setReview(null);
      }

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
      $reviewer = $formData['requestedReviewBy'];
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

  /** Verify whether the study area has access to review mode. */
  private function isReviewable(StudyArea|RequestStudyArea $studyArea)
  {
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    if (!$studyArea->isReviewModeEnabled()) {
      throw new NotFoundHttpException();
    }
  }

  /** Checks access for the supplied review. */
  private function checkAccess(StudyArea $studyArea, Review $review, bool $checkOwner = true)
  {
    // Check study area
    if ($studyArea->getId() !== $review->getStudyArea()->getId()) {
      throw new NotFoundHttpException('Study area does not match');
    }

    // Check for owner enabled?
    if (!$checkOwner) {
      return;
    }

    // Check access
    $user = $this->getUser();
    assert($user instanceof User);
    if ($review->getOwner()->getId() !== $user->getId()
        && !$this->isGranted('STUDYAREA_OWNER', $studyArea)) {
      throw new NotFoundHttpException('Access denied');
    }
  }
}

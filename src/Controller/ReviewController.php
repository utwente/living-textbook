<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
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
use App\Security\Voters\StudyAreaVoter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

use function array_keys;
use function assert;
use function count;
use function is_numeric;

#[Route('/{_studyArea<\d+>}/review')]
class ReviewController extends AbstractController
{
  /** Edit the pending review. It is only possible to edit the notes and requested reviewer. */
  #[Route('/{review<\d+>}/edit')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function editReview(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    TranslatorInterface $translator): Response
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

    return $this->render('review/edit_review.html.twig', [
      'form'        => $form,
      'changesForm' => $changesForm,
      'review'      => $review,
    ]);
  }

  /** Publish reviews after review approval. */
  #[Route('/publish')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_default_dashboard', subject: 'requestStudyArea')]
  public function publish(RequestStudyArea $requestStudyArea, ReviewRepository $reviewRepository): Response
  {
    return $this->render('review/publish.html.twig', [
      'reviews' => $reviewRepository->getApproved($requestStudyArea->getStudyArea()),
    ]);
  }

  /**
   * Publish the clicked review.
   *
   * @throws ORMException
   * @throws Throwable
   */
  #[Route('/{review<\d+>}/publish')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function publishReview(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, ReviewService $reviewService,
    TranslatorInterface $translator): Response
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

    return $this->render('review/publish_review.html.twig', [
      'form'            => $form,
      'submission_form' => $submissionForm,
      'review'          => $review,
    ]);
  }

  /** Remove the pending review. */
  #[Route('/{review<\d+>}/remove')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function removeReview(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    TranslatorInterface $translator): Response
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

    return $this->render('review/remove_review.html.twig', [
      'form'   => $form,
      'review' => $review,
    ]);
  }

  /**
   * Resubmits the review.
   *
   * @throws TransportExceptionInterface
   */
  #[Route('/{review<\d+>}/resubmit')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function resubmitSubmission(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    ReviewNotificationService $reviewNotificationService, TranslatorInterface $translator): Response
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

    return $this->render('review/resubmit_submission.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * Review a submission.
   *
   * @throws TransportExceptionInterface
   */
  #[Route('/{review<\d+>}')]
  #[IsGranted(StudyAreaVoter::REVIEW, subject: 'requestStudyArea')]
  public function reviewSubmission(
    Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
    TranslatorInterface $translator, ReviewNotificationService $reviewNotificationService): Response
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

    return $this->render('review/review_submission.html.twig', [
      'form'   => $form,
      'review' => $review,
    ]);
  }

  /** Show a submission. */
  #[Route('/{review<\d+>}/show')]
  #[IsGranted(StudyAreaVoter::REVIEW, subject: 'requestStudyArea')]
  public function showSubmission(RequestStudyArea $requestStudyArea, Review $review): Response
  {
    // Check study area
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Create form, although this is only show. This way, we can reuse the show logic from the review process
    $form = $this->createForm(ReviewSubmissionType::class, $review, [
      'review'        => false,
      'show_comments' => $review->getReviewedAt() !== null,
    ]);

    return $this->render('review/show_submission.html.twig', [
      'form'   => $form,
      'review' => $review,
    ]);
  }

  /** Show the pending reviews for the current user. */
  #[Route('/submissions')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function submissions(RequestStudyArea $requestStudyArea, ReviewRepository $reviewRepository): Response
  {
    $this->isReviewable($requestStudyArea);

    return $this->render('review/submissions.html.twig', [
      'reviews' => $reviewRepository->getSubmissions($requestStudyArea->getStudyArea()),
    ]);
  }

  /** Shows the pending changes of the current user which haven't been submitted for review. */
  #[Route('/submit')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function submit(
    Request $request, RequestStudyArea $requestStudyArea, PendingChangeRepository $pendingChangeRepository,
    ReviewService $reviewService, TranslatorInterface $translator): Response
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

        return $this->render('review/submit.html.twig', [
          'form'           => $form,
          'pendingChanges' => $pendingChanges,
        ]);
      }

      // Retrieve the form data
      $reviewer = $formData['requestedReviewBy'];
      $notes    = $formData['notes'];

      // Create the review
      $reviewService->createReview($studyArea, $markedChanges, $reviewer, $notes);

      $this->addFlash('success', $translator->trans('review.submitted'));

      return $this->redirectToRoute('app_review_submissions');
    }

    return $this->render('review/submit.html.twig', [
      'form'           => $form,
      'pendingChanges' => $pendingChanges,
    ]);
  }

  /** Verify whether the study area has access to review mode. */
  private function isReviewable(StudyArea|RequestStudyArea $studyArea): void
  {
    if ($studyArea instanceof RequestStudyArea) {
      $studyArea = $studyArea->getStudyArea();
    }

    if (!$studyArea->isReviewModeEnabled()) {
      throw new NotFoundHttpException();
    }
  }

  /** Checks access for the supplied review. */
  private function checkAccess(StudyArea $studyArea, Review $review, bool $checkOwner = true): void
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

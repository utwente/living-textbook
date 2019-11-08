<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
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
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Class ReviewController
 *
 * @Route("/{_studyArea}/review", requirements={"_studyArea"="\d+"})
 */
class ReviewController extends AbstractController
{

  /**
   * Edit the pending review. It is only possible to edit the notes and requested reviewer
   *
   * @Route("/{review}/edit", requirements={"review"="\d+"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @Template()
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Review                 $review
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $translator
   *
   * @return array|Response
   */
  public function editReview(
      Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
      TranslatorInterface $translator)
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

    return [
        'form'   => $form->createView(),
        'review' => $review,
    ];
  }

  /**
   * Publish reviews after review approval
   *
   * @Route("/publish")
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_default_dashboard", subject="requestStudyArea")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param ReviewRepository $reviewRepository
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
   * Publish the clicked review
   *
   * @Route("/{review}/publish", requirements={"review"="\d+"})
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @Template()
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param Review              $review
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $translator
   *
   * @return array|Response
   * @throws ORMException
   * @throws Throwable
   */
  public function publishReview(
      Request $request, RequestStudyArea $requestStudyArea, Review $review, ReviewService $reviewService,
      TranslatorInterface $translator)
  {
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Create the form
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route'       => 'app_review_publish',
        'remove_label'       => 'review.publish',
        'remove_btn_variant' => 'outline-success',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $reviewService->publishReview($review);

      $this->addFlash('success', $translator->trans('review.publish-successful'));

      return $this->redirectToRoute('app_review_publish');
    }

    return [
        'form'   => $form->createView(),
        'review' => $review,
    ];
  }

  /**
   * Remove the pending review
   *
   * @Route("/{review}/remove", requirements={"review"="\d+"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @Template()
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Review                 $review
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $translator
   *
   * @return array|Response
   */
  public function removeReview(
      Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
      TranslatorInterface $translator)
  {
    $studyArea = $requestStudyArea->getStudyArea();
    $this->checkAccess($studyArea, $review);

    // Create the form
    $form = $this->createForm(RemoveType::class, NULL, [
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
   * Review a submission
   *
   * @Route("/{review}", requirements={"review"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_REVIEW", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Review                 $review
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $translator
   *
   * @return array|Response
   * @throws Exception
   */
  public function reviewSubmission(
      Request $request, RequestStudyArea $requestStudyArea, Review $review, EntityManagerInterface $em,
      TranslatorInterface $translator)
  {
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Check if not yet approved
    if ($review->getApprovedAt() !== NULL) {
      throw new NotFoundHttpException("Requested review has already been approved");
    }

    // Create form
    $form = $this->createForm(ReviewSubmissionType::class, $review);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $hasComments = $review->hasComments();
      if (!$hasComments) {
        // Set as approved
        $user = $this->getUser();
        assert($user instanceof User);
        $review
            ->setApprovedAt(new DateTime())
            ->setApprovedBy($user);
      }

      $em->flush();

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
   * Show a submission
   *
   * @Route("/{review}/show", requirements={"review"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_REVIEW", subject="requestStudyArea")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param Review           $review
   *
   * @return array|Response
   */
  public function showSubmission(RequestStudyArea $requestStudyArea, Review $review)
  {
    // Check study area
    $this->checkAccess($requestStudyArea->getStudyArea(), $review, false);

    // Create form, although this is only show. This way, we can reuse the show logic from the review process
    $form = $this->createForm(ReviewSubmissionType::class, $review, [
        'review' => false,
    ]);

    return [
        'form'   => $form->createView(),
        'review' => $review,
    ];
  }

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
        'reviews' => $reviewRepository->getSubmissions($requestStudyArea->getStudyArea()),
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

  /**
   * Checks access for the supplied review
   *
   * @param StudyArea $studyArea
   * @param Review    $review
   * @param bool      $checkOwner
   */
  private function checkAccess(StudyArea $studyArea, Review $review, bool $checkOwner = true)
  {
    // Check study area
    if ($studyArea->getId() !== $review->getStudyArea()->getId()) {
      throw new NotFoundHttpException("Study area does not match");
    }

    // Check for owner enabled?
    if (!$checkOwner) {
      return;
    }

    // Check access
    if ($review->getOwner()->getId() !== $this->getUser()->getId()
        && !$this->isGranted('STUDYAREA_OWNER', $studyArea)) {
      throw new NotFoundHttpException("Access denied");
    }
  }
}

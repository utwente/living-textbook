<?php

namespace App\Communication\Notification;

use App\Entity\Review;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewNotificationService
{
  private MailerInterface $mailer;
  private TranslatorInterface $translator;

  public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
  {
    $this->mailer     = $mailer;
    $this->translator = $translator;
  }

  /**
   *Notify the requested reviewer there is a new review waiting.
   *
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function reviewRequested(Review $review)
  {
    $changeCount = 0;
    foreach ($review->getPendingChanges() as $pendingChange) {
      $changeCount += count((array)$pendingChange->getChangedFields());
    }

    $this->mailer->send(
        (new TemplatedEmail())
            ->to($review->getRequestedReviewBy()->getAddress())
            ->subject($this->trans('review.requested.subject'))
            ->htmlTemplate($this->template('review_requested'))
            ->context([
                'study_area_name' => $review->getStudyArea()->getName(),
                'study_area_id'   => $review->getStudyArea()->getId(),
                'reviewer'        => $review->getRequestedReviewBy()->getFullName(),
                'editor'          => $review->getOwner()->getFullName(),
                'review_id'       => $review->getId(),
                'review_notes'    => $review->getNotes(),
                'change_count'    => $changeCount,
            ])
    );
  }

  /**
   * Notify the editor that his/her submission has been denied.
   *
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function submissionDenied(Review $review)
  {
    $this->mailer->send(
        (new TemplatedEmail())
            ->to($review->getOwner()->getAddress())
            ->subject($this->trans('review.submission.denied.subject'))
            ->htmlTemplate($this->template('submission_denied'))
            ->context([
                'reviewer'        => $review->getReviewedBy()->getFullName(),
                'editor'          => $review->getOwner()->getFullName(),
                'study_area_name' => $review->getStudyArea()->getName(),
                'study_area_id'   => $review->getStudyArea()->getId(),
                'review_id'       => $review->getId(),
            ])
    );
  }

  /**
   * Notify the editor that his/her submission has been approved and is pending for publish
   * Notify the study area owner that there is a submission that needs to be published.
   *
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function submissionApproved(Review $review)
  {
    $this->mailer->send(
        (new TemplatedEmail())
            ->to($review->getOwner()->getAddress())
            ->subject($this->trans('review.submission.approved.subject'))
            ->htmlTemplate($this->template('submission_approved'))
            ->context([
                'reviewer'        => $review->getReviewedBy()->getFullName(),
                'editor'          => $review->getOwner()->getFullName(),
                'publisher'       => $review->getStudyArea()->getOwner()->getFullName(),
                'study_area_name' => $review->getStudyArea()->getName(),
            ])
    );

    $this->mailer->send(
        (new TemplatedEmail())
            ->to($review->getStudyArea()->getOwner()->getAddress())
            ->subject($this->trans('review.submission.approved-owner.subject'))
            ->htmlTemplate($this->template('submission_approved_owner'))
            ->context([
                'reviewer'        => $review->getReviewedBy()->getFullName(),
                'editor'          => $review->getOwner()->getFullName(),
                'publisher'       => $review->getStudyArea()->getOwner()->getFullName(),
                'study_area_name' => $review->getStudyArea()->getName(),
                'study_area_id'   => $review->getStudyArea()->getId(),
            ])
    );
  }

  /**
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function submissionPublished(Review $review)
  {
    $this->mailer->send(
        (new TemplatedEmail())
            ->to($review->getOwner()->getAddress())
            ->subject($this->trans('review.submission.published.subject'))
            ->htmlTemplate($this->template('submission_published'))
            ->context([
                'editor'          => $review->getOwner()->getFullName(),
                'study_area_name' => $review->getStudyArea()->getName(),
                'study_area_id'   => $review->getStudyArea()->getId(),
            ])
    );
  }

  private function template(string $template): string
  {
    return 'communication/review_notification/' . $template . '.html.twig';
  }

  private function trans(string $id, array $parameters = []): string
  {
    return $this->translator->trans($id, $parameters, 'communication');
  }
}

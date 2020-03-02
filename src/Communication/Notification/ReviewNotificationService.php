<?php

namespace App\Communication\Notification;

use App\Entity\Review;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReviewNotificationService
{
  /**
   * @var MailerInterface
   */
  private $mailer;
  /**
   * @var TranslatorInterface
   */
  private $translator;

  public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
  {
    $this->mailer     = $mailer;
    $this->translator = $translator;
  }

  /**
   * Notify the requested reviewer there is a new review waiting
   *
   * @param Review $review
   *
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function reviewRequested(Review $review)
  {
    $changeCount = 0;
    foreach ($review->getPendingChanges() as $pendingChange) {
      $changeCount += count($pendingChange->getChangedFields());
    }

    $email = (new TemplatedEmail())
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
        ]);

    $this->mailer->send($email);
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

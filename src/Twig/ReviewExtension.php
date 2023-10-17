<?php

namespace App\Twig;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\StudyArea;
use App\Review\ReviewService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReviewExtension extends AbstractExtension
{
  private ReviewService $reviewService;

  public function __construct(ReviewService $reviewService)
  {
    $this->reviewService = $reviewService;
  }

  public function getFunctions()
  {
    return [
        new TwigFunction('reviewEnabled', $this->reviewEnabled(...)),
    ];
  }

  public function reviewEnabled(StudyArea $studyArea, ReviewableInterface $reviewable): bool
  {
    return $this->reviewService->isReviewModeEnabledForObject($studyArea, $reviewable);
  }
}

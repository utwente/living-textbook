<?php

namespace App\Twig;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\StudyArea;
use App\Review\ReviewService;
use Twig\Attribute\AsTwigFunction;

class ReviewExtension
{
  public function __construct(private readonly ReviewService $reviewService)
  {
  }

  #[AsTwigFunction(name: 'reviewEnabled')]
  public function reviewEnabled(StudyArea $studyArea, ReviewableInterface $reviewable): bool
  {
    return $this->reviewService->isReviewModeEnabledForObject($studyArea, $reviewable);
  }
}

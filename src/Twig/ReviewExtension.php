<?php

namespace App\Twig;

use App\Entity\Contracts\ReviewableInterface;
use App\Entity\StudyArea;
use App\Review\ReviewService;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReviewExtension extends AbstractExtension
{
  public function __construct(private readonly ReviewService $reviewService)
  {
  }

  #[Override]
  public function getFunctions(): array
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

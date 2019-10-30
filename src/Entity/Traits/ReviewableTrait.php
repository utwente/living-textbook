<?php

namespace App\Entity\Traits;

/**
 * Trait ReviewableTrait
 * Some default function for reviewable entities
 */
trait ReviewableTrait
{
  public function getReviewName(): string
  {
    return self::class;
  }
}

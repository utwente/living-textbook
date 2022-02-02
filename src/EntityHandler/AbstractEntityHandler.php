<?php

namespace App\EntityHandler;

use App\Review\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Drenso\Shared\Exception\EntityValidationFailedException;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEntityHandler
{
  public function __construct(
      protected EntityManagerInterface $em,
      protected ?ValidatorInterface    $validator,
      protected ?ReviewService         $reviewService
  )
  {
  }

  protected function validate(mixed $value): void
  {
    if (!$this->validator) {
      return;
    }

    $violations = $this->validator->validate($value);
    if ($violations->count() === 0) {
      return;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    throw new EntityValidationFailedException($violations);
  }

  protected function useReviewService(?string $snapshot): bool
  {
    if ($this->reviewService === NULL) {
      return false;
    }

    if ($snapshot === NULL) {
      throw new RuntimeException('Snapshot must be provided when using the review service');
    }

    return true;
  }
}

<?php

namespace App\EntityHandler;

use Doctrine\ORM\EntityManagerInterface;
use Drenso\Shared\Exception\EntityValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractEntityHandler
{
  public function __construct(
      protected EntityManagerInterface $em,
      protected ?ValidatorInterface    $validator,
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
}

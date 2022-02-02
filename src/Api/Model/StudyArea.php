<?php

namespace App\Api\Model;

use OpenApi\Attributes as OA;

class StudyArea
{
  protected function __construct(
      protected readonly int $id,
      protected readonly string $name,
      #[OA\Property(nullable: true)]
      protected readonly ?string $description,
      #[OA\Property(nullable: true)]
      protected readonly ?string $group)
  {
  }

  public static function fromEntity(\App\Entity\StudyArea $studyArea): self
  {
    return new self(
        $studyArea->getId(),
        $studyArea->getName(),
        $studyArea->getDescription(),
        $studyArea->getGroup()?->getName(),
    );
  }
}

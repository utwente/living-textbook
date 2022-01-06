<?php

namespace App\Api\Model;

use OpenApi\Attributes as OA;

class StudyArea
{
  public int $id;
  public string $name;
  #[OA\Property(nullable: true)]
  public ?string $description;
  #[OA\Property(nullable: true)]
  public ?string $group;

  private function __construct(int $id, string $name, ?string $description, ?string $group)
  {
    $this->id          = $id;
    $this->name        = $name;
    $this->description = $description;
    $this->group       = $group;
  }

  public static function fromEntity(\App\Entity\StudyArea $studyArea): self
  {
    return new self(
        $studyArea->getId(),
        $studyArea->getName(),
        $studyArea->getDescription(),
        $studyArea->getGroup() ? $studyArea->getGroup()->getName() : NULL,
    );
  }
}

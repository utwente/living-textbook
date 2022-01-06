<?php

namespace App\Api\Model;

use OpenApi\Attributes as OA;

class RelationType
{
  protected function __construct(
      public readonly int $id,
      public readonly string $name,
             #[OA\Property(nullable: true)]
      public readonly ?string $description,
  )
  {
  }

  public static function fromEntity(\App\Entity\RelationType $relationType): self
  {
    return new self(
        $relationType->getId(),
        $relationType->getName(),
        $relationType->getDescription(),
    );
  }
}

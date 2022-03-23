<?php

namespace App\Api\Model;

use App\Entity\RelationType;
use JMS\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

class RelationTypeApiModel
{
  protected function __construct(
      protected readonly int $id,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $name,
      #[Groups(['Default', 'mutate'])]
      #[OA\Property(nullable: true)]
      protected readonly ?string $description,
  ) {
  }

  public static function fromEntity(RelationType $relationType): self
  {
    return new self(
        $relationType->getId(),
        $relationType->getName(),
        $relationType->getDescription(),
    );
  }

  public function mapToEntity(?RelationType $relationType): RelationType
  {
    return ($relationType ?? new RelationType())
        ->setName($this->name ?? $relationType?->getName() ?? '')
        ->setDescription($this->description ?? $relationType?->getDescription() ?? null);
  }
}

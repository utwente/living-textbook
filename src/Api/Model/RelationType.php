<?php

namespace App\Api\Model;

use JMS\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

class RelationType
{
  protected function __construct(
      protected readonly int $id,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $name,
      #[Groups(['Default', 'mutate'])]
      #[OA\Property(nullable: true)]
      protected readonly ?string $description,
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

  public function mapToEntity(?\App\Entity\RelationType $relationType): \App\Entity\RelationType
  {
    return ($relationType ?? new \App\Entity\RelationType())
        ->setName($this->name ?? $relationType?->getName() ?? '')
        ->setDescription($this->description ?? $relationType?->getDescription() ?? null);
  }
}

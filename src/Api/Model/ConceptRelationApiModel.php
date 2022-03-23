<?php

namespace App\Api\Model;

use App\Entity\ConceptRelation;

class ConceptRelationApiModel
{
  protected function __construct(
      protected readonly int $id,
      protected readonly int $sourceId,
      protected readonly int $targetId,
  ) {
  }

  public static function fromEntity(ConceptRelation $conceptRelation): self
  {
    return new self(
        $conceptRelation->getId(),
        $conceptRelation->getSourceId(),
        $conceptRelation->getTargetId(),
    );
  }
}

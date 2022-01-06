<?php

namespace App\Api\Model;

class ConceptRelation
{
  protected function __construct(
      public readonly int $id,
      public readonly int $sourceId,
      public readonly int $targetId,
  )
  {
  }

  public static function fromEntity(\App\Entity\ConceptRelation $conceptRelation): self
  {
    return new self(
        $conceptRelation->getId(),
        $conceptRelation->getSourceId(),
        $conceptRelation->getTargetId(),
    );
  }
}

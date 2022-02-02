<?php

namespace App\Api\Model;

class ConceptRelation
{
  protected function __construct(
      protected readonly int $id,
      protected readonly int $sourceId,
      protected readonly int $targetId,
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

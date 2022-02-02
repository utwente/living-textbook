<?php

namespace App\Api\Model\Detailed;

use App\Api\Model\ConceptRelation;
use OpenApi\Attributes as OA;

class DetailedConceptRelation extends ConceptRelation
{
  protected function __construct(
      int    $id,
      int    $sourceId,
      int    $targetId,
      protected readonly string $name,
      #[OA\Property(nullable: true)]
      protected readonly ?string $description
  )
  {
    parent::__construct($id, $sourceId, $targetId);
  }

  public static function fromEntity(\App\Entity\ConceptRelation $conceptRelation): self
  {
    return new self(
        $conceptRelation->getId(),
        $conceptRelation->getSourceId(),
        $conceptRelation->getTargetId(),
        $conceptRelation->getRelationName(),
        $conceptRelation->getRelationType()?->getDescription()
    );
  }
}

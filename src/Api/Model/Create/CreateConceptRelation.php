<?php

namespace App\Api\Model\Create;

use OpenApi\Attributes\Schema;

#[Schema(required: ['sourceId', 'targetId', 'relationTypeId'])]
class CreateConceptRelation
{
  protected function __construct(
      protected readonly int $sourceId,
      protected readonly int $targetId,
      protected readonly int $relationTypeId,
  ) {
  }

  public function isValid(): bool
  {
    return $this->getSourceId() !== null
        && $this->getTargetId() !== null
        && $this->getRelationTypeId() !== null;
  }

  public function getSourceId(): ?int
  {
    return $this->sourceId ?? null;
  }

  public function getTargetId(): ?int
  {
    return $this->targetId ?? null;
  }

  public function getRelationTypeId(): ?int
  {
    return $this->relationTypeId ?? null;
  }
}

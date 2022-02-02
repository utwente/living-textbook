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
  )
  {
  }

  public function isValid(): bool
  {
    return $this->getSourceId() !== NULL
        && $this->getTargetId() !== NULL
        && $this->getRelationTypeId() !== NULL;
  }

  public function getSourceId(): ?int
  {
    return $this->sourceId ?? NULL;
  }

  public function getTargetId(): ?int
  {
    return $this->targetId ?? NULL;
  }

  public function getRelationTypeId(): ?int
  {
    return $this->relationTypeId ?? NULL;
  }
}

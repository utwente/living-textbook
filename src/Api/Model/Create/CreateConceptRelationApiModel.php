<?php

namespace App\Api\Model\Create;

use OpenApi\Attributes\Schema;

#[Schema(required: ['sourceId', 'targetId', 'relationTypeId'])]
class CreateConceptRelationApiModel
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
    /* @phpstan-ignore nullCoalesce.initializedProperty (Needs fallback for old values) */
    return $this->sourceId ?? null;
  }

  public function getTargetId(): ?int
  {
    /* @phpstan-ignore nullCoalesce.initializedProperty (Needs fallback for old values) */
    return $this->targetId ?? null;
  }

  public function getRelationTypeId(): ?int
  {
    /* @phpstan-ignore nullCoalesce.initializedProperty (Needs fallback for old values) */
    return $this->relationTypeId ?? null;
  }
}

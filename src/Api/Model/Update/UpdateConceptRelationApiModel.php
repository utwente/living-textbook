<?php

namespace App\Api\Model\Update;

use App\Api\Model\ConceptRelationApiModel;
use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use OpenApi\Attributes as OA;

class UpdateConceptRelationApiModel extends ConceptRelationApiModel implements IdInterface
{
  protected function __construct(
    protected readonly int $id,
    protected readonly int $sourceId,
    protected readonly int $targetId,
    #[Groups(['Default', 'mutate'])]
    protected readonly ?int $relationTypeId,
    #[OA\Property(description: 'Specific Dotron configuration for a concept relation, only returned when Dotron is been enabled', type: 'object', nullable: true)]
    #[Type('array')]
    #[Groups(['dotron'])]
    protected readonly ?array $dotronConfig
  ) {
    parent::__construct($id, $sourceId, $targetId);
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getRelationTypeId(): ?int
  {
    return $this->relationTypeId ?? null;
  }

  public static function fromEntity(ConceptRelation $conceptRelation): self
  {
    return new self(
      $conceptRelation->getId(),
      $conceptRelation->getSourceId(),
      $conceptRelation->getTargetId(),
      $conceptRelation->getRelationType()->getId(),
      $conceptRelation->getDotronConfig(),
    );
  }

  public function mapToEntity(?ConceptRelation $conceptRelation, ?RelationType $relationType): ConceptRelation
  {
    return ($conceptRelation ?? new ConceptRelation())
      ->setRelationType($relationType ?? $conceptRelation->getRelationType())
      ->setDotronConfig($this->dotronConfig ?? $conceptRelation->getDotronConfig() ?? null);
  }
}

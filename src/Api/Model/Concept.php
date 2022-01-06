<?php

namespace App\Api\Model;

use App\Entity\Tag;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class Concept
{
  protected function __construct(
      public readonly string $name,
      public readonly string $definition,
      public readonly string $synonyms,
             #[OA\Property(description: 'Tag id list', type: 'array', items: new OA\Items(type: 'number'))]
      public readonly array $tags,
             #[OA\Property(type: 'array', items: new OA\Items(new Model(type: ConceptRelation::class)))]
      public readonly array $outgoingRelations,
  )
  {
  }

  public static function fromEntity(\App\Entity\Concept $concept): self
  {
    return new self(
        $concept->getName(),
        $concept->getDefinition(),
        $concept->getSynonyms(),
        $concept->getTags()->map(fn(Tag $tag) => $tag->getId())->toArray(),
        $concept->getOutgoingRelations()
            ->map(fn(\App\Entity\ConceptRelation $conceptRelation) => ConceptRelation::fromEntity($conceptRelation))
            ->toArray()
    );
  }
}

<?php

namespace App\Api\Model;

use App\Entity\Tag;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class Concept
{
  protected function __construct(
      protected readonly int $id,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $name,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $definition,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $synonyms,
      #[OA\Property(description: 'Tag id list', type: 'array', items: new OA\Items(type: 'number'))]
      protected readonly array $tags,
      #[OA\Property(type: 'array', items: new OA\Items(new Model(type: ConceptRelation::class)))]
      protected readonly array $outgoingRelations,
      #[OA\Property(type: 'object', nullable: true, description: 'Specific dotron configuration for a concept')]
      #[Groups(['dotron'])]
      #[Type("array")]
      protected readonly ?array $dotronConfig
  )
  {
  }

  public static function fromEntity(\App\Entity\Concept $concept): self
  {
    return new self(
        $concept->getId(),
        $concept->getName(),
        $concept->getDefinition(),
        $concept->getSynonyms(),
        $concept->getTags()->map(fn(Tag $tag) => $tag->getId())->getValues(),
        $concept->getOutgoingRelations()
            ->map(fn(\App\Entity\ConceptRelation $conceptRelation) => ConceptRelation::fromEntity($conceptRelation))
            ->getValues(),
        $concept->getDotronConfig()
    );
  }

  public function mapToEntity(?\App\Entity\Concept $concept): \App\Entity\Concept
  {
    return ($concept ?? new \App\Entity\Concept())
        ->setName($this->name ?? $concept?->getName() ?? '')
        ->setDefinition($this->definition ?? $concept?->getDefinition() ?? '')
        ->setSynonyms($this->synonyms ?? $concept?->getSynonyms() ?? '')
        ->setDotronConfig($this->dotronConfig ?? $concept?->getDotronConfig() ?? NULL);
  }
}

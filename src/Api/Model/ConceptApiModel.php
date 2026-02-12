<?php

namespace App\Api\Model;

use App\Entity\Concept;
use App\Entity\Tag;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Override;

use function array_map;

class ConceptApiModel implements IdInterface
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
    #[Type('array')]
    #[Groups(['Default', 'mutate'])]
    protected readonly array $tags,
    #[OA\Property(type: 'array', items: new OA\Items(new Model(type: ConceptRelationApiModel::class)))]
    protected readonly array $outgoingRelations,
    #[OA\Property(description: 'Specific Dotron configuration for a concept, only returned when Dotron is been enabled', type: 'object', nullable: true)]
    #[Type('array')]
    #[Groups(['dotron'])]
    protected readonly ?array $dotronConfig,
  ) {
  }

  #[Override]
  public function getId(): int
  {
    return $this->id;
  }

  #[Override]
  public function getNonNullId(): int
  {
    return $this->getId();
  }

  /** @return int[]|null */
  public function getTags(): ?array
  {
    /* @phpstan-ignore nullCoalesce.initializedProperty (Needs fallback for old values) */
    return $this->tags ?? null;
  }

  public static function fromEntity(Concept $concept): self
  {
    return new self(
      $concept->getId(),
      $concept->getName(),
      $concept->getDefinition(),
      $concept->getSynonyms(),
      $concept->getTags()->map(static fn (Tag $tag) => $tag->getId())->getValues(),
      $concept->getOutgoingRelations()
        ->map(ConceptRelationApiModel::fromEntity(...))
        ->getValues(),
      $concept->getDotronConfig()
    );
  }

  /** @param Tag[]|null $tags */
  public function mapToEntity(?Concept $concept, ?array $tags): Concept
  {
    $concept =  ($concept ?? new Concept())
      ->setName($this->name ?? $concept?->getName() ?? '')
      ->setDefinition($this->definition ?? $concept?->getDefinition() ?? '')
      ->setSynonyms($this->synonyms ?? $concept?->getSynonyms() ?? '')
      ->setDotronConfig($this->dotronConfig ?? $concept?->getDotronConfig() ?? null);

    if ($tags !== null) {
      $concept->getTags()->clear();

      array_map($concept->addTag(...), $tags);
    }

    return $concept;
  }
}

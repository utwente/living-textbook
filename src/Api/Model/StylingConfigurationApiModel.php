<?php

namespace App\Api\Model;

use App\Entity\StylingConfiguration;
use Drenso\Shared\IdMap\IdMap;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class StylingConfigurationApiModel
{
  protected function __construct(
    protected readonly int $id,
    #[Groups(['Default', 'mutate'])]
    protected readonly string $name,
    #[Type('array<string, array>')]
    #[Groups(['Default', 'mutate'])]
    protected readonly ?array $stylings,
    #[Groups(['Default'])]
    #[Type(IdMap::class)]
    protected readonly IdMap $conceptOverrides,
    #[Groups(['Default'])]
    #[Type(IdMap::class)]
    protected readonly IdMap $relationOverrides,
  ) {
  }

  public static function fromEntity(StylingConfiguration $stylingConfiguration): self
  {
    return new self(
      $stylingConfiguration->getId(),
      $stylingConfiguration->getName(),
      $stylingConfiguration->getStylings(),
      new IdMap($stylingConfiguration->getConceptOverrides()
        ->map(StylingConfigurationConceptOverrideApiModel::fromEntity(...))->getValues()),
      new IdMap($stylingConfiguration->getRelationOverrides()
        ->map(StylingConfigurationRelationOverrideApiModel::fromEntity(...))->getValues()),
    );
  }

  public function mapToEntity(?StylingConfiguration $stylingConfiguration): StylingConfiguration
  {
    $stylingConfiguration ??= new StylingConfiguration();

    return $stylingConfiguration
      ->setName($this->name ?? $stylingConfiguration->getName())
      ->setStylings($this->stylings ?? $stylingConfiguration->getStylings() ?? []);
  }
}

<?php

namespace App\Api\Model;

use App\Entity\StylingConfigurationRelationOverride;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class StylingConfigurationRelationOverrideApiModel implements IdInterface
{
  protected function __construct(
      #[Groups(['Default'])]
      protected readonly int $relation,
      #[Groups(['Default'])]
      protected readonly int $stylingConfiguration,
      #[Groups(['Default', 'mutate', 'create'])]
      #[Type('array')]
      protected readonly array $override
  ) {
  }

  // Used as index when responding with multiple
  public function getId(): ?int
  {
    return $this->relation;
  }

  public function getRelation(): int
  {
    return $this->relation;
  }

  public function getStylingConfiguration(): int
  {
    return $this->stylingConfiguration;
  }

  public function getOverride(): array
  {
    return $this->override;
  }

  public static function fromEntity(StylingConfigurationRelationOverride $override): self
  {
    return new self(
        $override->getRelation()->getId(),
        $override->getStylingConfiguration()->getId(),
        $override->getOverride(),
    );
  }

  public function mapToEntity(StylingConfigurationRelationOverride $override): StylingConfigurationRelationOverride
  {
    return $override->setOverride($this->override);
  }
}

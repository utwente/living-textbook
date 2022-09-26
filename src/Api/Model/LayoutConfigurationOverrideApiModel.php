<?php

namespace App\Api\Model;

use App\Entity\LayoutConfigurationOverride;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class LayoutConfigurationOverrideApiModel
{
  protected function __construct(
      #[Groups(['Default'])]
      protected readonly int $id,
      #[Groups(['Default', 'create'])]
      protected readonly int $concept,
      #[Groups(['Default', 'create'])]
      protected readonly int $layoutConfiguration,
      #[Groups(['Default', 'mutate', 'create'])]
      #[Type('array')]
      protected readonly array $override
  ) {
  }

  public function getId(): ?int
  {
    return $this->id ?? null;
  }

  public function getConcept(): int
  {
    return $this->concept;
  }

  public function getLayoutConfiguration(): int
  {
    return $this->layoutConfiguration;
  }

  public function getOverride(): array
  {
    return $this->override;
  }

  public static function fromEntity(LayoutConfigurationOverride $override): self
  {
    return new self(
        $override->getId(),
        $override->getConcept()->getId(),
        $override->getLayoutConfiguration()->getId(),
        $override->getOverride(),
    );
  }

  public function mapToEntity(LayoutConfigurationOverride $layoutConfigurationOverride): LayoutConfigurationOverride
  {
    return $layoutConfigurationOverride->setOverride($this->override);
  }
}

<?php

namespace App\Api\Model;

use App\Entity\LayoutConfigurationOverride;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Override;

readonly class LayoutConfigurationOverrideApiModel implements IdInterface
{
  protected function __construct(
    #[Groups(['Default'])]
    protected int $concept,
    #[Groups(['Default'])]
    protected int $layoutConfiguration,
    #[Groups(['Default', 'mutate', 'create'])]
    #[Type('array')]
    protected ?array $override,
  ) {
  }

  #[Override]
  public function getId(): int
  {
    return $this->concept;
  }

  #[Override]
  public function getNonNullId(): int
  {
    return $this->concept;
  }

  public function getConcept(): int
  {
    return $this->concept;
  }

  public function getLayoutConfiguration(): int
  {
    return $this->layoutConfiguration;
  }

  public function getOverride(): ?array
  {
    return $this->override;
  }

  public static function fromEntity(LayoutConfigurationOverride $override): self
  {
    return new self(
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

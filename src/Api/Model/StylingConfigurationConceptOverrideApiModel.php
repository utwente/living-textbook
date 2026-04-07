<?php

namespace App\Api\Model;

use App\Entity\StylingConfigurationConceptOverride;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;
use Override;

readonly class StylingConfigurationConceptOverrideApiModel implements IdInterface
{
  protected function __construct(
    #[Groups(['Default'])]
    protected int $concept,
    #[Groups(['Default'])]
    protected int $stylingConfiguration,
    #[Groups(['Default', 'mutate', 'create'])]
    #[Type('array')]
    protected ?array $override,
  ) {
  }

  // Used as index when responding with multiple
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

  public function getStylingConfiguration(): int
  {
    return $this->stylingConfiguration;
  }

  public function getOverride(): ?array
  {
    return $this->override;
  }

  public static function fromEntity(StylingConfigurationConceptOverride $override): self
  {
    return new self(
      $override->getConcept()->getId(),
      $override->getStylingConfiguration()->getId(),
      $override->getOverride(),
    );
  }

  public function mapToEntity(StylingConfigurationConceptOverride $override): StylingConfigurationConceptOverride
  {
    return $override->setOverride($this->override);
  }
}

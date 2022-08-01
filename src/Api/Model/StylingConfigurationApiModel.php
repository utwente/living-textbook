<?php

namespace App\Api\Model;

use App\Entity\StylingConfiguration;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class StylingConfigurationApiModel
{
  protected function __construct(
      protected readonly int $id,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $name,
      #[Type('array')]
      #[Groups(['Default', 'mutate'])]
      protected readonly ?array $stylings,
  ) {
  }

  public static function fromEntity(StylingConfiguration $stylingConfiguration): self
  {
    return new self(
        $stylingConfiguration->getId(),
        $stylingConfiguration->getName(),
        $stylingConfiguration->getStylings()
    );
  }

  public function mapToEntity(?StylingConfiguration $stylingConfiguration): StylingConfiguration
  {
    $stylingConfiguration = $stylingConfiguration ?? new StylingConfiguration();

    return $stylingConfiguration
        ->setName($this->name ?? $stylingConfiguration->getName())
        ->setStylings($this->stylings ?? $stylingConfiguration->getStylings() ?? []);
  }
}

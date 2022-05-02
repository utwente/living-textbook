<?php

namespace App\Api\Model;

use App\Entity\StylingConfiguration;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Type;

class StylingConfigurationApiModel
{
  protected function __construct(
      protected readonly int $id,
      #[Type('array')]
      #[Groups(['Default', 'mutate'])]
      protected readonly ?array $stylings,
  ) {
  }

  public static function fromEntity(StylingConfiguration $stylingConfiguration): self
  {
    return new self(
        $stylingConfiguration->getId(),
        $stylingConfiguration->getStylings()
    );
  }

  public function mapToEntity(?StylingConfiguration $stylingConfiguration): StylingConfiguration
  {
    return ($stylingConfiguration ?? new StylingConfiguration())
        ->setStylings($this->stylings ?? $stylingConfiguration->getStylings());
  }
}

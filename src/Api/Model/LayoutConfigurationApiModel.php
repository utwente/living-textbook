<?php

namespace App\Api\Model;

use App\Entity\LayoutConfiguration;
use JMS\Serializer\Annotation\Type;

class LayoutConfigurationApiModel
{
  protected function __construct(
      protected readonly int $id,
      #[Type('array')]
      protected readonly ?array $layouts
  ) {
  }

  public static function fromEntity(LayoutConfiguration $layoutConfiguration): self
  {
    return new self(
        $layoutConfiguration->getId(),
        $layoutConfiguration->getLayouts()
    );
  }

  public function mapToEntity(?LayoutConfiguration $layoutConfiguration): LayoutConfiguration
  {
    return ($layoutConfiguration ?? new LayoutConfiguration())
        ->setLayouts($this->layouts ?? $layoutConfiguration->getLayouts());
  }
}
